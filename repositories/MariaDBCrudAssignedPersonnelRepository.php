<?php

require_once 'interfaces/IAssignedPersonnelRepository.php';
require_once 'models/AssignedPersonnel.php';

class MariaDBCrudAssignedPersonnelRepository implements IAssignedPersonnelRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        // I join tables to get extra info if needed, but for now I select the raw relation
        $sql = "SELECT * FROM assigned_personnel ORDER BY scp_id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $this->toObject($row);
        }
        return $list;
    }

    public function getByIds($userId, $scpId)
    {
        $sql = "SELECT * FROM assigned_personnel WHERE user_id = :uid AND scp_id = :sid LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId, ':sid' => $scpId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toObject($row) : null;
    }

    public function create(AssignedPersonnel $assignment)
    {
        $sql = "INSERT INTO assigned_personnel (user_id, scp_id, role) VALUES (:uid, :sid, :role)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':uid'  => $assignment->getUserId(),
            ':sid'  => $assignment->getScpId(),
            ':role' => $assignment->getRole()
        ]);
    }

    public function update(AssignedPersonnel $assignment)
    {
        // I can only update the Role, because user_id and scp_id are the keys.
        $sql = "UPDATE assigned_personnel SET role = :role 
                WHERE user_id = :uid AND scp_id = :sid";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':role' => $assignment->getRole(),
            ':uid'  => $assignment->getUserId(),
            ':sid'  => $assignment->getScpId()
        ]);
    }

    public function delete($userId, $scpId)
    {
        $sql = "DELETE FROM assigned_personnel WHERE user_id = :uid AND scp_id = :sid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':uid' => $userId, ':sid' => $scpId]);
    }

    private function toObject($row)
    {
        return new AssignedPersonnel(
            $row['user_id'],
            $row['scp_id'],
            $row['role']
        );
    }
}
