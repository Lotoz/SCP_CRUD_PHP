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
        require_once 'views/CRUD/users/users.php';
    }

    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAdminAuth();
        require_once 'views/CRUD/users/usersCreate.php';
    }

    public function store()
    {
        $this->verifyAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. SANITIZACIÓN
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'] ?? '';
            $rol = filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_SPECIAL_CHARS);
            $level = filter_input(INPUT_POST, 'level', FILTER_VALIDATE_INT);
            $theme = filter_input(INPUT_POST, 'theme', FILTER_SANITIZE_SPECIAL_CHARS);
            $state = isset($_POST['state']) ? 1 : 0;

            // --- VALIDACIONES CON SESSION ERROR ---

            // 1. Campos obligatorios
            if (empty($id) || empty($password)) {
                $_SESSION['error'] = "MANDATORY FIELDS: Operative ID and Password are required.";
                // Redirigimos de vuelta al formulario de creación
                header("Location: index.php?action=users_create");
                exit;
            }
            // Validate ID format: allow only letters, numbers, hyphen and underscore
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
                $_SESSION['error'] = 'ID contains invalid characters.';
                header('Location: index.php?action=users_create');
                exit();
            }
            if ($level === false || $level < 0 || $level > 5 || $level > 10) {
                $_SESSION['error'] = "INVALID FORMAT: The clearance level must be an integer between 0 and 5.";
                header("Location: index.php?action=users_create");
                exit;
            }

            // 2. Formato de Email
            if (!$email) {
                $_SESSION['error'] = "INVALID FORMAT: The provided email address is not valid.";
                header("Location: index.php?action=users_create");
                exit;
            }

            $user = new User($id, $name, $lastname, $email, $password, $level, $rol, $theme);
            $user->setstate($state);

            try {
                $this->repository->create($user);

                // ÉXITO: Mantenemos el JS para cerrar la ventana popup
                echo "<script>
                        alert('Personnel registered successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
                // Error específico de duplicados (SQLState 23000)
                if ($e->getCode() == '23000') {
                    $_SESSION['error'] = "DUPLICATE ENTRY: The ID '$id' is already in use.";
                } else {
                    $_SESSION['error'] = "DATABASE ERROR: " . $e->getMessage();
                }
                header("Location: index.php?action=users_create");
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = "SYSTEM ERROR: " . $e->getMessage();
                header("Location: index.php?action=users_create");
                exit;
            }
            exit;
        }
    }

    public function edit($id)
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAdminAuth();

        $id = htmlspecialchars($id);

        $user = $this->repository->getById($id);
        if ($user) {
            require_once 'views/CRUD/users/usersEdit.php';
        } else {
            // Si no encuentra el usuario, cerramos la ventana
            echo "<script>alert('User not found.'); window.close();</script>";
        }
    }

    public function update()
    {
        $this->verifyAdminAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. SANITIZACIÓN
            $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);

            // Validar Email
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

            // Contraseña (vacía significa "no cambiar")
            $password = $_POST['password'] ?? '';

            $rol = filter_input(INPUT_POST, 'rol', FILTER_SANITIZE_SPECIAL_CHARS);
            $level = filter_input(INPUT_POST, 'level', FILTER_VALIDATE_INT);
            $theme = filter_input(INPUT_POST, 'theme', FILTER_SANITIZE_SPECIAL_CHARS);
            $state = isset($_POST['state']) ? 1 : 0;


            // 1. Validar Email
            if (!$email) {
                $_SESSION['error'] = "INVALID FORMAT: The provided email address is not valid.";
                // IMPORTANTE: Debemos devolver el ID para saber a quién estábamos editando
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit;
            }
            if ($level === false || $level < 0 || $level > 5 || $level > 10) {
                $_SESSION['error'] = "INVALID FORMAT: The clearance level must be an integer between 0 and 5.";
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit;
            }
            // Validate ID format: allow only letters, numbers, hyphen and underscore
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
                $_SESSION['error'] = 'ID contains invalid characters.';
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit();
            }

            // 2. Crear objeto usuario
            $user = new User($id, $name, $lastname, $email, $password, $level, $rol, $theme);
            $user->setstate($state);

            try {
                // Intentar actualizar en BD
                $this->repository->update($user);

                // ÉXITO: Mostramos alerta y cerramos ventana (comportamiento JS)
                echo "<script>
                        alert('Personnel file updated successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (PDOException $e) {
                // Capturar errores SQL (ej: si cambias el email a uno que ya existe)
                $_SESSION['error'] = "DATABASE ERROR: " . $e->getMessage();
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit;
            } catch (Exception $e) {
                // Errores genéricos
                $_SESSION['error'] = "UPDATE FAILED: " . $e->getMessage();
                header("Location: index.php?action=users_edit&id=" . urlencode($id));
                exit;
            }
            exit;
        }
    }

    public function delete()
    {
        $this->verifyAdminAuth();

        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($id === $_SESSION['user_id']) {
            $_SESSION['error'] = "PROTOCOL VIOLATION: You cannot expunge your own record.";
        } else if ($id) {
            try {
                $this->repository->delete($id);
                // Opcional: Mensaje de éxito en sesión si quieres mostrarlo en el index
                // $_SESSION['success'] = "User deleted successfully."; 
            } catch (Exception $e) {
                $_SESSION['error'] = "DELETE FAILED: " . $e->getMessage();
            }
        }

        header('Location: index.php?action=users_index');
        exit;
    }

    private function verifyAdminAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 5) {
            $_SESSION['error'] = "ACCESS DENIED: Clearance Level 5 required.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
