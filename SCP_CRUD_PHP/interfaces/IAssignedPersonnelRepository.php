<?php

/**
 * Interface IAssignedPersonnelRepository
 */
interface IAssignedPersonnelRepository
{
    public function getAll();

    // I need both IDs to find a specific row
    public function getByIds($userId, $scpId);

    public function create(AssignedPersonnel $assignment);

    public function update(AssignedPersonnel $assignment);

    // I need both IDs to delete a specific row
    public function delete($userId, $scpId);
}
