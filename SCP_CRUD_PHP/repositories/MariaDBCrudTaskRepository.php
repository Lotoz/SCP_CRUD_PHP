<?php

require_once 'interfaces/ITaskRepository.php';
require_once 'models/Task.php';

class MariaDBCrudTaskRepository implements ITaskRepository
{
    private $pdo;

    /**
     * Constructor expects a PDO connection instance.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Get all tasks (Generic admin view).
     * @return array Array of Task objects.
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
     * Get all tasks assigned to a specific user.
     * Used for the main Dashboard.
     * @param mixed $userId
     * @return array Array of Task objects.
     */
    public function getByUserId($userId)
    {
        // Using prepared statements to prevent SQL Injection
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
     * Get all tasks that are not completed (completado = 0).
     */
    public function getNotCompletedTasks($userId)
    {
        // 1. CORRECCIÓN SQL: Verificamos el nombre de la columna (usualmente 'id_usuario')
        $sql = "SELECT * FROM tasks WHERE completado = 0 AND id_usuario = :id_user ORDER BY id DESC";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id_user' => $userId]);

            $tasks = [];
            
            // 2. CORRECCIÓN BUCLE: Asignamos el fetch DENTRO de la condición del while
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = $this->toObject($row);
            }

            // Retornamos el array (vacío o lleno). 
            // NO agregamos strings de error aquí, eso lo maneja la Vista.
            return $tasks;

        } catch (PDOException $e) {
            // Loguear error si es necesario
            return [];
        }
    }

    /**
     * Find a specific task by its ID.
     * @param int $id
     * @return Task|null Returns the Task object or null if not found.
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
     * Create a new task in the database.
     * @param Task $task
     * @return bool True on success, false on failure.
     */
    public function create(Task $task)
    {
        $sql = "INSERT INTO tasks (description, completado, id_usuario) VALUES (:desc, :comp, :user)";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':desc' => $task->getDescription(),
            ':comp' => $task->getCompletado(), // Returns 0 or 1
            ':user' => $task->getIdUsuario()
        ]);
    }

    /**
     * Update an existing task.
     * @param Task $task
     * @return bool True on success, false on failure.
     */
    public function update(Task $task)
    {
        $sql = "UPDATE tasks SET description = :desc, completado = :comp WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':desc' => $task->getDescription(),
            ':comp' => $task->getCompletado(),
            ':id'   => $task->getId()
        ]);
    }

    /**
     * Delete a task by its ID.
     * @param int $id
     * @return bool True on success, false on failure.
     */
    public function delete($id)
    {
        $sql = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ===== Private Helper Methods =====

    /**
     * Maps a database row (array) to a Task object instance.
     * Keeps code DRY (Don't Repeat Yourself).
     * @param array $row
     * @return Task
     */
    private function toObject($row)
    {
        return new Task(
            $row['id'],
            $row['description'],
            $row['completado'],
            $row['id_usuario']
        );
    }
}
