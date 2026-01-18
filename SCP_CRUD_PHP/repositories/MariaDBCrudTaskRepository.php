<?php

require_once 'interfaces/ITaskRepository.php';
require_once 'models/Task.php';

class MariaDBCrudTaskRepository implements ITaskRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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
            return [];
        }
    }

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
     * [ACTUALIZADO]: Incluye due_date
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
            ':date' => $task->getDueDate() // Puede ser null, y está bien
        ]);
    }

    /**
     * Update an existing task.
     * [ACTUALIZADO]: Incluye due_date
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

    public function delete($id)
    {
        $sql = "DELETE FROM tasks WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    // ===== Private Helper Methods =====

    /**
     * Maps a database row (array) to a Task object instance.
     *  Mapea la columna due_date
     */
    private function toObject($row)
    {
        return new Task(
            $row['id'],
            $row['description'],
            $row['completado'],
            $row['id_usuario'],
            // Si la columna no existe en el array (por si acaso), pasamos null
            $row['due_date'] ?? null
        );
    }
}
