<?php

require_once 'interfaces/ITaskRepository.php';
require_once 'models/Task.php';

/**
 * MariaDBCrudTaskRepository
 * * Concrete implementation for Personal Task Management.
 * * Handles the CRUD operations for the 'tasks' table, including completion status and due dates.
 */
class MariaDBCrudTaskRepository implements ITaskRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves all tasks in the system (Admin view).
     * Ordered by ID DESC to show the most recent tasks first.
     */
    public function getAll()
    {
        $sql = "SELECT * FROM tasks ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = $this->toObject($row);
        }

        return $tasks;
    }

    /**
     * Retrieves all tasks assigned to a specific user.
     * @param string $userId
     * @return array
     */
    public function getByUserId($userId)
    {
        $sql = "SELECT * FROM tasks WHERE id_usuario = :id_user ORDER BY id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_user' => $userId]);

        $tasks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tasks[] = $this->toObject($row);
        }

        return $tasks;
    }

    /**
     * Retrieves only the pending (incomplete) tasks for a user.
     * * Optimized for the Dashboard view to reduce clutter.
     * @param string $userId
     * @return array
     */
    public function getNotCompletedTasks($userId)
    {
        $sql = "SELECT * FROM tasks WHERE completado = 0 AND id_usuario = :id_user ORDER BY id DESC";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_user' => $userId]);

            $tasks = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = $this->toObject($row);
            }
            return $tasks;
        } catch (PDOException $e) {
            // Fail gracefully by returning an empty list rather than crashing the dashboard
            return [];
        }
    }

    /**
     * Finds a specific task by its unique numeric ID.
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM tasks WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return $this->toObject($row);
        }

        return null;
    }

    /**
     * Creates a new task record.
     * * UPDATED: Now supports the optional 'due_date' field.
     * @param Task $task
     * @return bool
     */
    public function create(Task $task)
    {
        $sql = "INSERT INTO tasks (description, completado, id_usuario, due_date) 
                VALUES (:desc, :comp, :user, :date)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':desc' => $task->getDescription(),
            ':comp' => $task->getCompletado(),
            ':user' => $task->getIdUsuario(),
            ':date' => $task->getDueDate() // Null values are handled automatically by PDO
        ]);
    }

    /**
     * Updates an existing task's description, status, or due date.
     * @param Task $task
     * @return bool
     */
    public function update(Task $task)
    {
        $sql = "UPDATE tasks SET 
                description = :desc, 
                completado = :comp, 
                due_date = :date 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':desc' => $task->getDescription(),
            ':comp' => $task->getCompletado(),
            ':date' => $task->getDueDate(),
            ':id'   => $task->getId()
        ]);
    }

    /**
     * Deletes a task by ID.
     */
    public function delete($id)
    {
        $sql = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ===== Private Helper Methods =====

    /**
     * Maps a database row (assoc array) to a Task object instance.
     * Handles potential nulls for the 'due_date' column safely.
     */
    private function toObject($row)
    {
        return new Task(
            $row['id'],
            $row['description'],
            $row['completado'],
            $row['id_usuario'],
            $row['due_date'] ?? null
        );
    }
}
