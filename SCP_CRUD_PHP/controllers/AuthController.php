<?php

require_once 'models/User.php';
require_once 'interfaces/ILoginUserRepository.php';
require_once 'config/Database.php';

/**
 * AuthController - Authentication Controller
 * I am responsible for:
 * - Handling HTTP requests regarding login/register.
 * - Calling the repository for data access.
 * - Managing session logic.
 * - Rendering the auth views.
 */
class AuthController
{
    private $userRepository;
    private $taskRepository;

    public function __construct(ILoginUserRepository $userRepository, ITaskRepository $taskRepository)
    {
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
    }

    public function login()
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?action=dashboard');
            exit();
        }
        $csrf_token = SessionManager::generateCSRFToken();
        include 'views/login.php';
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit();
        }

        // 1. INPUT VALIDATION (No usar $_POST directo)
        // filter_input devuelve null si no existe, false si falla el filtro
        $id = filter_input(INPUT_POST, 'user', FILTER_DEFAULT);
        $password = filter_input(INPUT_POST, 'password', FILTER_DEFAULT); // No sanitizamos passwords

        if (!$id || !$password) {
            $_SESSION['error'] = 'Username and password are required.';
            header('Location: index.php?action=login');
            exit();
        }

        // Trim después de obtener
        $id = trim($id);

        try {
            $user = $this->userRepository->getById($id);

            // Prevención de enumeración de usuarios (Mensaje genérico)
            if (!$user) {
                $_SESSION['error'] = 'Invalid credentials.';
                header('Location: index.php?action=login');
                exit();
            }

            if (!$user->isstate()) {
                $_SESSION['error'] = 'Account locked. Contact Administration.';
                header('Location: index.php?action=login');
                exit();
            }

            if (!$user->verificarPassword($password)) {
                $attempts = $user->getTryAttempts() + 1;
                $this->userRepository->updateAttempts($id, $attempts);

                if ($attempts >= 5) {
                    $this->userRepository->updateState($id); // Bloqueo
                    $_SESSION['error'] = 'Account locked due to excessive login attempts.';
                } else {
                    $_SESSION['error'] = 'Invalid credentials.';
                }
                header('Location: index.php?action=login');
                exit();
            }

            // Login Exitoso
            $this->userRepository->resetAttempts($id);
            session_regenerate_id(true); // Prevenir Session Fixation

            $_SESSION['user_id'] = $user->getId();
            $_SESSION['name']    = $user->getFullName();
            $_SESSION['level']   = $user->getLevel();
            $_SESSION['rol']     = $user->getRol();

            // Validación de Tema para evitar Path Traversal (ej: ../../hack.css)
            $allowedThemes = ['gears', 'unicorn', 'ice', 'admin', 'clef', 'sophie'];
            $theme = $user->getTheme();
            $_SESSION['theme'] = in_array($theme, $allowedThemes) ? $theme . '.css' : 'gears.css';

            header('Location: index.php?action=dashboard');
            exit();
        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            $_SESSION['error'] = 'System Error.';
            header('Location: index.php?action=login');
            exit();
        }
    }

    /**
     * I show the dashboard (main page).
     * Only accessible if the user is authenticated.
     */
    public function dashboard() // O index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];

        // Llamamos al nuevo método del repositorio
        $tasks = $this->taskRepository->getNotCompletedTasks($userId);

        require_once 'views/dashboard.php';
    }

    /**
     * I log the user out and destroy the session.
     */
    public function logout()
    {
        SessionManager::logout();
        header('Location: index.php?action=login');
        exit();
    }

    public function register()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        include 'views/register.php';
    }

    public function registerProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=register');
            exit();
        }

        // INPUT VALIDATION ROBUSTA
        $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $lastname = filter_input(INPUT_POST, 'lastname', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!$id || !$name || !$lastname || !$email || !$password) {
            $_SESSION['error'] = 'All fields are required and must be valid.';
            header('Location: index.php?action=register');
            exit();
        }

        // Validación de formato de ID (Solo letras, numeros, - y _)
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
            $_SESSION['error'] = 'ID contains invalid characters.';
            header('Location: index.php?action=register');
            exit();
        }

        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match.';
            header('Location: index.php?action=register');
            exit();
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'Password too short (min 8 chars).';
            header('Location: index.php?action=register');
            exit();
        }

        try {
            if ($this->userRepository->getById($id)) {
                $_SESSION['error'] = 'ID already taken.';
                header('Location: index.php?action=register');
                exit();
            }

            if ($this->userRepository->getByEmail($email)) {
                $_SESSION['error'] = 'Email already exists.';
                header('Location: index.php?action=register');
                exit();
            }

            // CREAR USUARIO
            // NOTA: Pasamos la password PLANA ($password), porque el Repository::save() 
            // ya tiene la línea: password_hash(..., PASSWORD_BCRYPT);
            $user = new User($id, $name, $lastname, $email, $password, 1, 'cleaner', 'gears');

            if ($this->userRepository->save($user)) {
                $_SESSION['success'] = 'Registered successfully.';
                header('Location: index.php?action=login');
                exit();
            } else {
                $_SESSION['error'] = 'Database error.';
                header('Location: index.php?action=register');
                exit();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $_SESSION['error'] = 'System Error.';
            header('Location: index.php?action=register');
            exit();
        }
    }
}
