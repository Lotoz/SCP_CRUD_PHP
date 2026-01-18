<?php

/**
 * Interface IUserRepository
 * Defines the contract for the Administrative CRUD of Personnel.
 * (Distinct from ILoginUserRepository which handles Auth).
 */
interface IUserRepository
{
    /**
     * Lists all registered personnel.
     */
    public function getAll();

    /**
     * Retrieves a user profile by their ID.
     */
    public function getById($id);

    /**
     * Registers new personnel.
     */
    public function create(User $user);

    /**
     * Updates personnel profile data (Name, Role, Level, etc.).
     */
    public function update(User $user);

    /**
     * Removes a user from the active roster.
     */
    public function delete($id);

    /**
     * Toggles the account status (Active/Locked).
     * Used for banning/unbanning without altering other profile data.
     * @param string $id User ID.
     * @param int $state 1 for Active, 0 for Locked.
     */
    public function updateState($id, $state);
}
