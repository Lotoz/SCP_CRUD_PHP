<?php

require_once 'models/ExEmpleados.php';
require_once 'interfaces/IExEmpleadosRepository.php';

/**
 * ExEmpleadosController
 *
 * Controller for managing historical employee records (ex-employees). This is
 * typically an admin-only area used to review and purge historical data. The
 * controller provides a read-only listing and a permanent delete operation.
 */
class ExEmpleadosController
{
    private $repository;

    public function __construct(IExEmpleadosRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Show the archive/history of ex-employees.
     * A CSRF token is generated for any potential actions initiated from the view.
     */
    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        $exEmpleadosList = $this->repository->getAll();
        require_once 'views/CRUD/ex_empleados/ex_empleados.php';
    }

    /**
     * Permanently delete an historical record (purge).
     * Security: action must come from POST to avoid accidental deletes via GET.
     */
    public function delete()
    {
        $this->verifyAuth();
        // Read id from POST body for safety
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->repository->delete($id);
        }

        header('Location: index.php?action=exempleados_index');
        exit;
    }

    private function verifyAuth()
    {
        // Only admins or users with clearance level 5 may access these archives
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 5) {
            $_SESSION['error'] = "Access Denied: Level 5 Clearance required for Archives.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
