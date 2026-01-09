<?php

/**
 * Los navegadores se pierden en el modelo MVC, por ende es necesario especificarles la ruta en general. 
 */
define('BASE_URL', 'http://localhost/SCP_LOGIN_PHP/');

require_once 'controllers/AuthController.php';
require_once 'repositories/MariaDBUserRepository.php';
require_once 'models/User.php';
require_once 'models/SessionManager.php';
require_once 'config/Database.php';


SessionManager::startSession(); // Inicia sesión y configura seguridad
$csrf_token = SessionManager::generateCSRFToken();

// Crear el repository (acceso a datos)
$userRepository = new MariaDBUserRepository();

// Inyectar el repository en el controlador
$controller = new AuthController($userRepository);

// Router - Determinar qué acción ejecutar
if (!isset($_REQUEST['action'])) {
    $controller->login();
} else {
    switch ($_REQUEST['action']) {
        case 'login':
            $controller->login();
            break;
        case 'authenticate':
            $controller->authenticate();
            break;
        case 'dashboard':
            $controller->dashboard();
            break;
        case 'register':
            $controller->register();
            break;
        case 'registerprocess':
            $controller->registerProcess();
            break;
        case 'logout':
            $controller->logout();
            break;
        default:
            $controller->login();
            break;
    }
}
