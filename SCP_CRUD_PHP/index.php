<?php
// I include the configuration and session manager
require_once 'config/SessionManager.php';
require_once 'config/Database.php';


// I include the Models
// (I need these here so that if I unserialize objects from Session, the class is known)
require_once 'models/User.php';
require_once 'models/Task.php';
require_once 'models/Anomalies.php';
require_once 'models/Site.php';
require_once 'models/ExEmpleados.php';
require_once 'models/AssignedPersonnel.php';

// I include the Controllers
require_once 'controllers/AuthController.php';
require_once 'controllers/TaskController.php';
require_once 'controllers/AnomaliesController.php';
require_once 'controllers/UserController.php';
require_once 'controllers/SiteController.php';
require_once 'controllers/ExEmpleadosController.php';
require_once 'controllers/AssignedPersonnelController.php';
require_once 'controllers/WikiController.php';

// I include the Repositories
require_once 'repositories/MariaDBILoginUserRepository.php';
require_once 'repositories/MariaDBCrudTaskRepository.php';
require_once 'repositories/MariaDBCrudAnomaliesRepository.php';
require_once 'repositories/MariaDBCrudUserRepository.php';
require_once 'repositories/MariaDBCrudSiteRepository.php';
require_once 'repositories/MariaDBCrudExEmpleadosRepository.php';
require_once 'repositories/MariaDBCrudAssignedPersonnelRepository.php';

// I start the session and configure security
SessionManager::startSession();
SessionManager::checkActivity();
$csrf_token = SessionManager::generateCSRFToken();

// I initialize the Database Connection
$database = new Database();
$pdo = $database->getConnection();

// =======================
// DEPENDENCY INJECTION
// =======================

// 1. Auth Setup
$userRepository = new MariaDBILoginUserRepository($pdo);
$taskRepository = new MariaDBCrudTaskRepository($pdo);
$authController = new AuthController($userRepository, $taskRepository);

// 2. Task Setup
// I inject the PDO connection into the repository, and the repository into the controller
$taskRepository = new MariaDBCrudTaskRepository($pdo);
$taskController = new TaskController($taskRepository);

// 3. Anomalies Setup
$anomaliesRepository = new MariaDBCrudAnomaliesRepository($pdo);
$anomaliesController = new AnomaliesController($anomaliesRepository);
// 4. User CRUD Setup
$crudUserRepository = new MariaDBCrudUserRepository($pdo);
$userController = new UserController($crudUserRepository);
// 5. Sites CRUD Setup
$siteRepository = new MariaDBCrudSiteRepository($pdo);
$siteController = new SiteController($siteRepository);
// 6. Former Employees (ExEmpleados) CRUD Setup
$exRepository = new MariaDBCrudExEmpleadosRepository($pdo);
$exController = new ExEmpleadosController($exRepository);
// 7. Assigned Personnel CRUD Setup
$assignedRepo = new MariaDBCrudAssignedPersonnelRepository($pdo);
$assignedController = new AssignedPersonnelController($assignedRepo);
// 8. Wiki Setup
$wikiController = new WikiController($anomaliesRepository);



// =======================
// SECURITY MIDDLEWARE (Global)
// =======================

// 1. Global CSRF protection for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!SessionManager::verifyCSRFToken($token)) {
        // Log the attack attempt
        error_log("Possible CSRF attack detected from IP: " . $_SERVER['REMOTE_ADDR']);

        // Halt execution. No one passes without a valid token.
        die("Security Error: Session expired or invalid token. Please reload the page.");
    }
}

// =======================
// ROUTING
// =======================


// I determine which action to execute
if (!isset($_REQUEST['action'])) {
    $authController->login();
} else {
    $action = $_REQUEST['action'];

    switch ($action) {
        // -------- AuthController -------
        case 'login':
            $authController->login();
            break;
        case 'authenticate':
            $authController->authenticate();
            break;
        case 'register':
            $authController->register();
            break;
        case 'registerprocess':
            $authController->registerProcess();
            break;
        case 'logout':
            $authController->logout();
            break;
        case 'dashboard':
            $authController->dashboard();
            break;

        // -------- TaskController (New) -------
        case 'task_index':
            $taskController->index();
            break;
        case 'task_create':
            $taskController->create();
            break;
        case 'task_store':
            $taskController->store();
            break;
        case 'task_edit':
            $id = $_GET['id'] ?? null;
            $taskController->edit($id);
            break;
        case 'task_update':
            $taskController->update();
            break;
        case 'task_delete':
            $taskController->delete();
            break;

        // -------- UserController -------
        case 'users_index':
            $userController->index();
            break;
        case 'users_create':
            $userController->create();
            break;
        case 'users_store':
            $userController->store();
            break;
        case 'users_edit':
            $id = $_GET['id'] ?? null;
            $userController->edit($id);
            break;
        case 'users_update':
            $userController->update();
            break;
        case 'users_delete':
            $userController->delete();
            break;
        // --------- SitesController -------
        case 'sites_index':
            $siteController->index();
            break;
        case 'sites_create':
            $siteController->create();
            break;
        case 'sites_store':
            $siteController->store();
            break;
        case 'sites_edit':
            $id = $_GET['id'] ?? null;
            $siteController->edit($id);
            break;
        case 'sites_update':
            $siteController->update();
            break;
        case 'sites_delete':
            $siteController->delete();
            break;

        // -------- AnomaliesController------
        case 'anomalies_index':
            $anomaliesController->index();
            break;
        case 'anomalies_create':
            $anomaliesController->create();
            break;
        case 'anomalies_store':
            $anomaliesController->store();
            break;
        case 'anomalies_edit':
            $id = $_GET['id'] ?? null;
            $anomaliesController->edit($id);
            break;
        case 'anomalies_update':
            $anomaliesController->update();
            break;
        case 'anomalies_delete':
            $anomaliesController->delete();
            break;

        // ------- EXEmpleadosController --------
        case 'exempleados_index':
            $exController->index();
            break;
        case 'exempleados_index':
            $exController->index();
            break;
        case 'exempleados_delete':
            $exController->delete();
            break;

        // ------- AssignedPersonnelController --------
        case 'assigned_index':
            $assignedController->index();
            break;
        case 'assigned_create':
            $assignedController->create();
            break;
        case 'assigned_store':
            $assignedController->store();
            break;
        case 'assigned_edit':
            $assignedController->edit();
            break;
        case 'assigned_update':
            $assignedController->update();
            break;
        case 'assigned_delete':
            $assignedController->delete();
            break;
        // ------- Wiki Controller --------
        case 'scpwiki':
            $wikiController->index();
            break;
        case 'wiki_show':
            $wikiController->show();
            break;
        default:
            $authController->login();
            break;
    }
}
