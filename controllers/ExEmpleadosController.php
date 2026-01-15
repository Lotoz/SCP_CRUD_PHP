<?php

require_once 'models/ExEmpleados.php';
require_once 'interfaces/IExEmpleadosRepository.php';

class ExEmpleadosController
{
    private $repository;

    public function __construct(IExEmpleadosRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Solo mostramos el historial.
     */
    public function index()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        $this->verifyAuth();
        $exEmpleadosList = $this->repository->getAll();
        require_once 'views/CRUD/ex_empleados/ex_empleados.php';
    }

    /**
     * Eliminar definitivamente del historial (Purgar).
     */
    public function delete()
    {
        $this->verifyAuth();
        // Seguridad: POST
        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->repository->delete($id);
        }

        header('Location: index.php?action=exempleados_index');
        exit;
    }

    private function verifyAuth()
    {
        // Solo Admin o Nivel 5 puede ver archivos históricos
        if (!isset($_SESSION['user_id']) || $_SESSION['level'] < 5) {
            $_SESSION['error'] = "Access Denied: Level 5 Clearance required for Archives.";
            header('Location: index.php?action=dashboard');
            exit;
        }
    }
}
