<?php

require_once 'models/AssignedPersonnel.php';
require_once 'interfaces/IAssignedPersonnelRepository.php';

class AssignedPersonnelController
{
    private $repository;

    public function __construct(IAssignedPersonnelRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        $assignments = $this->repository->getAll();
        // RUTA ACTUALIZADA
        require_once 'views/CRUD/assigned/assigned.php';
    }

    public function create()
    {
        //Dejo el token CSRF para el formulario
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        // RUTA ACTUALIZADA
        require_once 'views/CRUD/assigned/assignedCreate.php';
    }

    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // CSRF Check (Middleware)

            $userId = trim($_POST['user_id']);
            $scpId = trim($_POST['scp_id']);
            $role = trim($_POST['role']);

            if (empty($userId) || empty($scpId)) {
                echo "<script>alert('Error: User ID and SCP ID are required.'); window.history.back();</script>";
                exit;
            }

            $assignment = new AssignedPersonnel($userId, $scpId, $role);

            try {
                $this->repository->create($assignment);
                echo "<script>
                        alert('Personnel assigned successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), '23000') !== false) {
                    echo "<script>alert('Error: Duplicate entry or Invalid IDs.'); window.history.back();</script>";
                } else {
                    echo "<script>alert('Database Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
                }
            }
            exit;
        }
    }

    // --- NUEVO: EDITAR ---
    public function edit()
    {
        // Dejo el token CSRF para el formulario
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        // Necesitamos ambos IDs para encontrar el registro
        $uid = $_GET['uid'] ?? null;
        $sid = $_GET['sid'] ?? null;

        $assignment = $this->repository->getByIds($uid, $sid);

        if ($assignment) {
            // RUTA ACTUALIZADA
            require_once 'views/CRUD/assigned/assignedEdit.php';
        } else {
            echo "<script>alert('Assignment record not found.'); window.close();</script>";
        }
    }

    // --- NUEVO: ACTUALIZAR ---
    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_POST['user_id']; // Read only
            $scpId = $_POST['scp_id'];   // Read only
            $role = trim($_POST['role']);

            $assignment = new AssignedPersonnel($userId, $scpId, $role);

            try {
                $this->repository->update($assignment);
                echo "<script>
                        alert('Assignment role updated.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    // --- DELETE SEGURO (POST) ---
    public function delete()
    {
        $this->verifyAuth();
        // Leemos del POST
        $userId = $_POST['uid'] ?? null;
        $scpId = $_POST['sid'] ?? null;

        if ($userId && $scpId) {
            $this->repository->delete($userId, $scpId);
        }

        header('Location: index.php?action=assigned_index');
        exit;
    }

    private function verifyAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 4) {
            $_SESSION['error'] = "Access Denied: Level 4 Clearance required.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
