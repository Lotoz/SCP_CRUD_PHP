<?php

require_once 'models/User.php';
require_once 'interfaces/IUserRepository.php';
require_once 'config/Database.php';

/**
 * AuthController - Controlador de autenticación
 * Responsable de:
 *  - Recibir peticiones HTTP
 *  - Llamar al repositorio para acceso a datos
 *  - Manejar la lógica de sesiones
 *  - Renderizar las vistas
 */
class AuthController
{
    private $userRepository;

    /**
     * Constructor con inyección de dependencias
     * @param IUserRepository $userRepository Repository de usuarios
     */
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Mostrar formulario de login
     */
    public function login()
    {
        include 'views/login.php';
    }

    /**
     * Procesar autenticación del usuario
     * Se ejecuta cuando el usuario envía el formulario de login
     */
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=login');
            exit();
        }


        $id = isset($_POST['user']) ? trim($_POST['user']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        if (empty($id) || empty($password)) {
            $_SESSION['error'] = 'Debe ingresar usuario y contraseña';
            header('Location: index.php?action=login');
            exit();
        }

        try {
            $user = $this->userRepository->getById($id);
            if (!$user) {
                $_SESSION['error'] = 'Credenciales inválidas';
                sleep(1); // Pequeña pausa para evitar ataques de fuerza bruta rápidos
                header('Location: index.php?action=login');
                exit();
            }

            if (!$user->isActivo()) {
                $_SESSION['error'] = 'Usuario inactivo. Contacte al administrador';
                header('Location: index.php?action=login');
                exit();
            }

            if (!$user->verificarPassword($password)) {
                $intentos = $user->getIntentosFallidos() + 1;
                $this->userRepository->updateAttempts($id, $intentos);
                if ($intentos >= 5) {
                    $_SESSION['error'] = 'Cuenta bloqueada temporalmente por intentos fallidos.';
                    $this->userRepository->updateState($id);
                } else {
                    $_SESSION['error'] = 'Credenciales inválidas';
                }

                header('Location: index.php?action=login');
                exit();
            }

            if ($user->getIntentosFallidos() > 0) {
                $this->userRepository->resetAttempts($id);
            }

            session_regenerate_id(true);

            $_SESSION['idusuario'] = $user->getId();
            $_SESSION['email'] = $user->getEmail();
            $_SESSION['nombre'] = $user->getNombreCompleto();
            $_SESSION['level'] = $user->getLevel();
            $_SESSION['rol'] = $user->getRol();
            $_SESSION['theme'] = $user->getTheme() . '.css';

            header('Location: index.php?action=dashboard');
            exit();
        } catch (Exception $e) {
            error_log("Error Login: " . $e->getMessage());
            $_SESSION['error'] = 'Ocurrió un error en el sistema. Intente más tarde.';
            header('Location: index.php?action=login');
            exit();
        }
    }
    /**
     * Mostrar dashboard (página principal)
     * Solo accesible si el usuario está autenticado
     */
    public function dashboard()
    {
        if (!isset($_SESSION['idusuario'])) {
            header('Location: index.php?action=login');
            exit();
        }
        include 'views/dashboard.php';
    }

    /**
     * Cerrar sesión del usuario
     */
    public function logout()
    {
        SessionManager::logout();
        header('Location: index.php?action=login');
        exit();
    }

    /**
     * Mostrar formulario de registro
     */
    public function register()
    {
        include 'views/register.php';
    }

    /**
     * Procesar el registro de un nuevo usuario
     */
    public function registerProcess()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=register');
            exit();
        }


        $id       = $this->sanitizeInput($_POST['id'] ?? '');
        $nombre   = $this->sanitizeInput($_POST['nombre'] ?? '');
        $apellido = $this->sanitizeInput($_POST['apellido'] ?? '');

        $emailRaw = $_POST['email'] ?? '';
        $email    = filter_var($emailRaw, FILTER_SANITIZE_EMAIL);

        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $rol = $_POST['rol'] ?? 'scienct';

        if (empty($id) || empty($nombre) || empty($apellido) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: index.php?action=register');
            exit();
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $id)) {
            $_SESSION['error'] = 'El ID contiene caracteres inválidos o espacios. Use solo letras, números, "-" y "_".';
            header('Location: index.php?action=register');
            exit();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Formato de correo electrónico inválido.';
            header('Location: index.php?action=register');
            exit();
        }

        // Validar Contraseñas
        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Las credenciales de seguridad (contraseñas) no coinciden.';
            header('Location: index.php?action=register');
            exit();
        }

        if (strlen($password) < 8) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 8 caracteres.';
            header('Location: index.php?action=register');
            exit();
        }

        try {

            if ($this->userRepository->getById($id)) {
                $_SESSION['error'] = 'Operative ID ya registrado. Solicite uno diferente.';
                header('Location: index.php?action=register');
                exit();
            }

            if ($this->userRepository->getByEmail($email)) {
                $_SESSION['error'] = 'Este correo electrónico ya existe en la base de datos.';
                header('Location: index.php?action=register');
                exit();
            }

            $user = new User($id, $nombre, $apellido, $email, $password, 0, $rol, 'gears'); //0 -> user no habilitado, se debera habilitar

            if ($this->userRepository->save($user)) {
                $_SESSION['success'] = 'Operativo registrado correctamente. Proceda a identificarse.';
                header('Location: index.php?action=login');
                exit();
            } else {
                $_SESSION['error'] = 'Error crítico en el sistema de registro.';
                header('Location: index.php?action=register');
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error del sistema: Contacte al administrador.';
            error_log($e->getMessage());
            header('Location: index.php?action=register');
            exit();
        }
    }

    /**
     * Función auxiliar para limpiar entradas de texto
     * Elimina espacios extra, barras invertidas y convierte caracteres especiales en entidades HTML
     */
    private function sanitizeInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
}
