<?php

require_once 'interfaces/IExEmpleadosRepository.php';
require_once 'models/ExEmpleados.php';

class MariaDBCrudExEmpleadosRepository implements IExEmpleadosRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        // I order by deletion date descending (newest archives first)
        $sql = "SELECT * FROM ex_empleados ORDER BY fecha_eliminacion DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $this->toObject($row);
        }
        return $list;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM ex_empleados WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->toObject($row) : null;
    }

    public function create(ExEmpleados $ex)
    {
        // I allow manual insertion just in case, trigger usually handles this.
        $sql = "INSERT INTO ex_empleados (name, lastname, rol, level, fecha_eliminacion) 
                VALUES (:name, :lastname, :rol, :level, NOW())";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name'     => $ex->getName(),
            ':lastname' => $ex->getLastname(),
            ':rol'      => $ex->getRol(),
            ':level'    => $ex->getLevel()
        ]);
    }

    public function update(ExEmpleados $ex)
    {
        $sql = "UPDATE ex_empleados SET 
                name = :name, 
                lastname = :lastname, 
                rol = :rol, 
                level = :level
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name'     => $ex->getName(),
            ':lastname' => $ex->getLastname(),
            ':rol'      => $ex->getRol(),
            ':level'    => $ex->getLevel(),
            ':id'       => $ex->getId()
        ]);
    }

    public function delete($id)
    {
        // Use carefully: This deletes the history log.
        $sql = "DELETE FROM ex_empleados WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

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
