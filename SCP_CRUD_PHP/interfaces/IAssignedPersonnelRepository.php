<?php

/**
 * Interface IAssignedPersonnelRepository
 * Manages the Many-to-Many relationship between Users and SCPs.
 * Operations require Composite Keys (User ID + SCP ID).
 */
interface IAssignedPersonnelRepository
{
    /**
     * Retrieves all active assignment records.
     */
    public function getAll();

    /**
     * Finds a specific assignment using the composite primary key.
     * @param string $userId
     * @param string $scpId
     * @return AssignedPersonnel|null
     */
    public function getByIds($userId, $scpId);

    /**
     * Creates a new assignment link (assigns a user to an SCP).
     */
    public function create(AssignedPersonnel $assignment);

    /**
     * Updates the role or details of an existing assignment.
     */
    public function update(AssignedPersonnel $assignment);

    /**
     * Removes an assignment. 
     * Requires both IDs to identify the specific link to break.
     */
    public function delete($userId, $scpId);
}
