<?php

/**
 * Los navegadores se pierden en el modelo MVC, por ende es necesario especificarles la ruta en general. 
 */
define('BASE_URL', 'http://localhost/SCP_CRUD_PHP/');

require_once 'controllers/AuthController.php';
//Agregar todos los controllers

require_once 'repositories/MariaDBILoginUserRepository.php';
require_once 'repositories/MariaDBICrudRepository.php';
//Agregar todo los repositorios

/** //! Porque esta esto aca??
 * require_once 'models/User.php'; 
 * require_once 'config/Database.php';
 * */
require_once 'config/SessionManager.php';



SessionManager::startSession(); // Inicia sesión y configura seguridad
$csrf_token = SessionManager::generateCSRFToken();

// Crear el repository (acceso a datos)
$userRepository = new MariaDBILoginUserRepository();

// Inyectar el repository en el controlador
$controller = new AuthController($userRepository);
//Crear cada controller y agregar su repository

// Router - Determinar qué acción ejecutar
if (!isset($_REQUEST['action'])) {
    $controller->login();
} else {
    switch ($_REQUEST['action']) {
        // -------- AuthController -------
        case 'login':
            $controller->login();
            break;
        case 'authenticate':
            $controller->authenticate();
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
        case 'dashboard':
            $controller->dashboard();
            break;
        // -------- UserController -------
        case 'users':
            $homeController->users();
            break;
        // --------- SitesController -------
        case 'sites':
            $homeController->sites();
            break;
        // -------- AnomaliesController------
        case 'anomalies':
            $homeController->anomalies();
            break;
        // ------- EXEmpleadosController --------
        case 'exempleados':
            $ExEmpleadosController->exempleados();
            break;
        // ------- WikiSCPController --------    
        case 'scpwiki':
            $homeController->scpwiki();
            break;

        default:
            $controller->login();
            break;
    }
}
