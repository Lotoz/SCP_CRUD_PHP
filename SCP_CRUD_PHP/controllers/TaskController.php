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
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        // Sanitizamos el ID de sesión por buena práctica, aunque viene del server
        $userId = htmlspecialchars($userId);

        $tasks = $this->repository->getByUserId($userId);
        $csrf_token = SessionManager::generateCSRFToken();

        require_once 'views/CRUD/task/task.php';
    }

    public function create()
    {
        $csrf_token = SessionManager::generateCSRFToken();
        require_once 'views/CRUD/task/taskCreate.php';
    }

    public function store()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Sanitizar Descripción (Evita XSS)
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

            // 2. Sanitizar Fecha (Evita inyección de código en el campo fecha)
            $dueDateInput = filter_input(INPUT_POST, 'due_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $dueDate = !empty($dueDateInput) ? $dueDateInput : null;

            // El User ID viene de la sesión (Seguro), pero la descripción venía del usuario.
            $userId = $_SESSION['user_id'];

            // Validación estricta
            if (empty($description) || $description === false) {
                echo "<script>alert('Error: Description contains invalid characters or is empty.'); window.history.back();</script>";
                exit;
            }

            $task = new Task(null, trim($description), 0, $userId, $dueDate);

            try {
                $this->repository->create($task);
                echo "<script>
                        alert('Task assigned successfully.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                // Usamos htmlspecialchars en el mensaje de error por si la DB devuelve algo raro
                echo "<script>alert('Error: " . addslashes(htmlspecialchars($e->getMessage())) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    public function edit($id)
    {
        // Validar que el ID que viene por GET sea un número entero
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            echo "<script>alert('Invalid Task ID.'); window.close();</script>";
            exit;
        }

        $csrf_token = SessionManager::generateCSRFToken();
        $task = $this->repository->getById($id);

        if ($task) {
            if ($task->getIdUsuario() !== $_SESSION['user_id'] && $_SESSION['level'] < 5) {
                echo "<script>alert('Unauthorized access to this task.'); window.close();</script>";
                exit;
            }
            require_once 'views/CRUD/task/taskEdit.php';
        } else {
            echo "<script>alert('Task not found.'); window.close();</script>";
        }
    }

    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Validar ID numérico
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

            // 2. Sanitizar Descripción
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_SPECIAL_CHARS);

            // 3. Checkbox (es seguro, solo verificamos existencia)
            $completado = isset($_POST['completado']) ? 1 : 0;

            // 4. Sanitizar Fecha
            $dueDateInput = filter_input(INPUT_POST, 'due_date', FILTER_SANITIZE_SPECIAL_CHARS);
            $dueDate = !empty($dueDateInput) ? $dueDateInput : null;

            // 5. Sanitizar ID Usuario (aunque debería ser el de sesión o readonly)
            $userId = filter_input(INPUT_POST, 'id_usuario', FILTER_SANITIZE_SPECIAL_CHARS);

            // Verificación de integridad
            if (!$id || !$description) {
                echo "<script>alert('Error: Invalid data provided.'); window.history.back();</script>";
                exit;
            }

            // SEGURIDAD EXTRA: Asegurar que el usuario no modifique el dueño de la tarea
            // (A menos que sea admin nivel 5, pero por ahora forzamos consistencia)
            if ($userId !== $_SESSION['user_id'] && $_SESSION['level'] < 5) {
                // Si intentan inyectar otro ID de usuario en el POST
                $userId = $_SESSION['user_id'];
            }

            $task = new Task($id, trim($description), $completado, $userId, $dueDate);

            try {
                $this->repository->update($task);
                echo "<script>
                        alert('Task updated.');
                        if(window.opener){ window.opener.location.reload(); }
                        window.close();
                      </script>";
            } catch (Exception $e) {
                echo "<script>alert('Error: " . addslashes(htmlspecialchars($e->getMessage())) . "'); window.history.back();</script>";
            }
            exit;
        }
    }

    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            die("Access Denied");
        }

        // Validar que el ID a borrar sea un número entero
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id) {
            $this->repository->delete($id);
        } else {
            // Opcional: Manejar error si el ID no es válido
        }

        header('Location: index.php?action=task_index');
        exit;
    }
}
