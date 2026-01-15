<?php

require_once 'models/Task.php';
require_once 'interfaces/ITaskRepository.php';

class TaskController
{
    private $repository;

    public function __construct(ITaskRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index()
    {
        // No verificamos "AdminAuth" porque cualquier usuario puede tener tareas
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $tasks = $this->repository->getByUserId($userId);
        $csrf_token = SessionManager::generateCSRFToken();
        // RUTA CORREGIDA
        require_once 'views/CRUD/task/task.php';
    }

    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();

        // RUTA CORREGIDA
        require_once 'views/CRUD/task/taskCreate.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // CSRF handled by middleware

            $description = trim($_POST['description']);
            $userId = $_SESSION['user_id'];

            if (empty($description)) {
                echo "<script>alert('Description required'); window.history.back();</script>";
                exit;
            }

            $task = new Task(null, $description, 0, $userId);

            try {
                $this->repository->create($task);
                echo "<script>
                        alert('Task assigned.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    // --- NUEVO: EDITAR ---
    public function edit($id)
    {
        /**if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }'**/
        $csrf_token = SessionManager::generateCSRFToken();
        $task = $this->repository->getById($id);

        if ($task) {
            // Verificar propiedad (opcional pero recomendado)
            if ($task->getIdUsuario() !== $_SESSION['user_id'] && $_SESSION['level'] < 5) {
                echo "<script>alert('Unauthorized access to this task.'); window.close();</script>";
                exit;
            }
            require_once 'views/CRUD/task/taskEdit.php';
        } else {
            echo "<script>alert('Task not found.'); window.close();</script>";
        }
    }

    // --- NUEVO: ACTUALIZAR ---
    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $description = trim($_POST['description']);
            // Checkbox envía '1' si está marcado, nada si no.
            $completado = isset($_POST['completado']) ? 1 : 0;

            // Mantenemos el usuario original (o el de sesión)
            $userId = $_POST['id_usuario'];

            $task = new Task($id, $description, $completado, $userId);

            try {
                $this->repository->update($task);
                echo "<script>
                        alert('Task updated.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    // --- DELETE SEGURO ---
    public function delete()
    {

        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        $id = $_POST['id'] ?? null;

        if ($id) {
            $this->repository->delete($id);
        }

        header('Location: index.php?action=task_index');
        exit;
    }
}
