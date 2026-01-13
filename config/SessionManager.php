<?php
class SessionManager
{

    // Iniciar sesión segura y configurar cookies
    public static function startSession()
    {
        if (session_status() == PHP_SESSION_NONE) {
            // Configuración de seguridad de cookies antes de iniciar sesión
            session_set_cookie_params([
                'lifetime' => 7200,             // 2 horas de vida límite
                'path' => '/',
                'domain' => 'localhost',        // Ajustar según tu dominio
                'secure' => false,              // Poner en true si usas HTTPS (recomendado en producción)
                'httponly' => true,             // Evita que JavaScript lea la cookie (XSS)
                'samesite' => 'Strict'          // Protección extra contra CSRF
            ]);
            session_start();
        }
    }

    //  Regeneración de ID y Timeout por inactividad
    private static function checkActivity()
    {
        // Si hay una última actividad registrada
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
            // Si pasaron más de 30 minutos (1800 seg), cerramos sesión
            self::logout();
            header("Location: login.php?msg=timeout");
            exit();
        }

        // Regenerar ID de sesión cada 30 minutos para evitar secuestro de sesión
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }

        $_SESSION['last_activity'] = time(); // Actualizar tiempo
    }

    // Generar Token CSRF
    public static function generateCSRFToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Verificar Token CSRF
    public static function verifyCSRFToken($token)
    {
        if (isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token)) {
            return true;
        }
        return false;
    }

    public static function logout()
    {
        // Vaciar array por las dudas
        $_SESSION = [];

        // Borrar cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_unset();
        // Destruir sesión
        session_destroy();
    }
}
