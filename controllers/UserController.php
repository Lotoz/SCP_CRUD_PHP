<?php

require_once 'models/User.php';
require_once 'interfaces/IUserRepository.php';

class UserController
{
    private $repository;

    public function __construct(IUserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAdminAuth();
        $usersList = $this->repository->getAll();
        // RUTA ACTUALIZADA
        require_once 'views/CRUD/users/users.php';
    }

    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAdminAuth();
        // RUTA ACTUALIZADA
        require_once 'views/CRUD/users/usersCreate.php';
    }

    public function store()
    {
        $this->verifyAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // CSRF Check handled by middleware

            $id = trim($_POST['id']);
            $name = trim($_POST['name']);
            $lastname = trim($_POST['lastname']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $rol = $_POST['rol'];
            $level = (int)$_POST['level'];
            $theme = $_POST['theme'];
            $state = isset($_POST['state']) ? 1 : 0;

            if (empty($id) || empty($password)) {
                echo "<script>alert('ID and Password are required.'); window.history.back();</script>";
                exit;
            }

            $user = new User($id, $name, $lastname, $email, $password, $level, $rol, $theme);
            $user->setstate($state);

            try {
                $this->repository->create($user);
                echo "<script>
                        alert('Personnel registered successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    // --- EDICIÓN ---
    public function edit($id)

    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAdminAuth();
        $user = $this->repository->getById($id);

        if ($user) {
            // RUTA ACTUALIZADA
            require_once 'views/CRUD/users/usersEdit.php';
        } else {
            echo "<script>alert('User not found.'); window.close();</script>";
        }
    }

    public function update()
    {
        $this->verifyAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id']; // Read only
            $name = $_POST['name'];
            $lastname = $_POST['lastname'];
            $email = $_POST['email'];
            $password = $_POST['password']; // Si está vacío, el repo lo ignora
            $rol = $_POST['rol'];
            $level = $_POST['level'];
            $theme = $_POST['theme'];
            $state = isset($_POST['state']) ? 1 : 0;

            $user = new User($id, $name, $lastname, $email, $password, $level, $rol, $theme);
            $user->setstate($state);

            try {
                $this->repository->update($user);
                echo "<script>
                        alert('Personnel file updated.');
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
        $this->verifyAdminAuth();

        $id = $_POST['id'] ?? null;

        // Prevent deleting yourself
        if ($id === $_SESSION['user_id']) {
            $_SESSION['error'] = "Protocol Violation: You cannot expunge your own record.";
        } else if ($id) {
            $this->repository->delete($id);
        }

        header('Location: index.php?action=users_index');
        exit;
    }

    private function verifyAdminAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 5) {
            $_SESSION['error'] = "Access Denied: Clearance Level 5 required.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
