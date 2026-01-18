<?php

require_once 'interfaces/IExEmpleadosRepository.php';
require_once 'models/ExEmpleados.php';

/**
 * MariaDBCrudExEmpleadosRepository
 * * Concrete implementation for accessing the Former Employees archive.
 * * This table is typically populated via Database Triggers upon user deletion.
 * Provides read-only access to history and the ability to permanently expunge records.
 */
class MariaDBCrudExEmpleadosRepository implements IExEmpleadosRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves the full history of former personnel.
     * * Ordered by 'fecha_eliminacion' DESC to show the most recent terminations first.
     * @return array List of ExEmpleados objects.
     */
    public function getAll()
    {
        $sql = "SELECT * FROM ex_empleados ORDER BY fecha_eliminacion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $this->toObject($row);
        }
        return $list;
    }

    /**
     * Permanently deletes a record from the history log.
     * * WARNING: This action removes the only remaining trace of the employee.
     * @param string $id The ID of the former employee.
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM ex_empleados WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Helper to map database rows to ExEmpleados objects.
     */
    private function toObject($row)
    {
        return new ExEmpleados(
            $row['id'],
            $row['name'],
            $row['lastname'],
            $row['rol'],
            $row['level'],
            $row['fecha_eliminacion']
        );
    }
}
