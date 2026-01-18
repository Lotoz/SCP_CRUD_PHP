<?php

require_once 'interfaces/IAssignedPersonnelRepository.php';
require_once 'models/AssignedPersonnel.php';

/**
 * MariaDBCrudAssignedPersonnelRepository
 * * Concrete implementation for managing the Many-to-Many relationship 
 * between Personnel and SCPs.
 * * NOTE: This table uses a Composite Primary Key (user_id + scp_id).
 */
class MariaDBCrudAssignedPersonnelRepository implements IAssignedPersonnelRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves all active assignments.
     * @return array List of AssignedPersonnel objects.
     */
    public function getAll()
    {
        // Fetches raw relationship data. Join queries would happen in a Service layer if needed.
        $sql = "SELECT * FROM assigned_personnel ORDER BY scp_id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $this->toObject($row);
        }
        return $list;
    }

    /**
     * Finds a specific assignment using the Composite Key.
     * Requires both the User ID and the SCP ID to identify a unique row.
     * * @param string $userId
     * @param string $scpId
     * @return AssignedPersonnel|null
     */
    public function getByIds($userId, $scpId)
    {
        $sql = "SELECT * FROM assigned_personnel WHERE user_id = :uid AND scp_id = :sid LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':uid' => $userId, ':sid' => $scpId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $this->toObject($row) : null;
    }

    /**
     * Creates a new assignment linking a user to an anomaly with a specific role.
     */
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

    /**
     * Updates an existing assignment.
     * NOTE: We only update the 'role' here. The IDs are the keys; changing them 
     * would technically be a Delete + Create operation.
     */
    public function update(AssignedPersonnel $assignment)
    {
        $sql = "UPDATE assigned_personnel SET role = :role 
                WHERE user_id = :uid AND scp_id = :sid";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':role' => $assignment->getRole(),
            ':uid'  => $assignment->getUserId(),
            ':sid'  => $assignment->getScpId()
        ]);
    }

    /**
     * Removes an assignment record.
     * Requires both IDs to ensure we delete the exact relationship.
     */
    public function delete($userId, $scpId)
    {
        $sql = "DELETE FROM assigned_personnel WHERE user_id = :uid AND scp_id = :sid";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':uid' => $userId, ':sid' => $scpId]);
    }

    /**
     * Helper to map database rows to objects.
     */
    private function toObject($row)
    {
        return new AssignedPersonnel(
            $row['user_id'],
            $row['scp_id'],
            $row['role']
        );
    }
}
