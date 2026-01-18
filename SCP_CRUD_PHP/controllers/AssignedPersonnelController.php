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
        require_once 'views/CRUD/assigned/assigned.php';
    }

    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        require_once 'views/CRUD/assigned/assignedCreate.php';
    }

    public function store()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. SANITIZACIÓN
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $scpId = filter_input(INPUT_POST, 'scp_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

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
                    echo "<script>alert('Database Error: " . addslashes(htmlspecialchars($e->getMessage())) . "'); window.history.back();</script>";
                }
            }
            exit;
        }
    }

    // --- Edit assignment ---
    public function edit()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();

        // Sanitización de GET parameters
        $uid = isset($_GET['uid']) ? htmlspecialchars($_GET['uid']) : null;
        $sid = isset($_GET['sid']) ? htmlspecialchars($_GET['sid']) : null;

        $assignment = $this->repository->getByIds($uid, $sid);

        if ($assignment) {
            require_once 'views/CRUD/assigned/assignedEdit.php';
        } else {
            echo "<script>alert('Assignment record not found.'); window.close();</script>";
        }
    }

    // --- Update assignment ---
    public function update()
    {
        $this->verifyAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Read only inputs
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_SPECIAL_CHARS);
            $scpId = filter_input(INPUT_POST, 'scp_id', FILTER_SANITIZE_SPECIAL_CHARS);
            // Role es el único editable
            $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_SPECIAL_CHARS);

            $assignment = new AssignedPersonnel($userId, $scpId, $role);

            try {
                $this->repository->update($assignment);
                echo "<script>
                        alert('Assignment role updated.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes(htmlspecialchars($e->getMessage())) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    // --- Secure delete (POST) ---
    public function delete()
    {
        $this->verifyAuth();

        $userId = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_SPECIAL_CHARS);
        $scpId = filter_input(INPUT_POST, 'sid', FILTER_SANITIZE_SPECIAL_CHARS);

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
