<?php

/**
 * Repository interface that defines data-access methods for users.
 * This interface contains only data-layer operations and no controller logic.
 */
interface IUserRepository
{
    /**
     * Retrieve a user by their email address.
     * @param string $email User email address.
     * @return User|null The User object if found, or null if not found.
     */
    public function getByEmail($email);

    /**
     * Retrieve a user by their unique identifier.
     * @param int $id User ID.
     * @return User|null The User object if found, or null if not found.
     */
    public function getById($id);

    /**
     * Persist a new user to the database.
     * @param User $user The user to save.
     * @return bool True on success, false on failure.
     */
    public function save(User $user);

    /**
     * Update the number of failed login attempts for a user.
     * @param int $id User ID.
     * @param int $attempts Number of failed attempts to set.
     * @return bool True on success, false on failure.
     */
    public function updateAttempts($id, $attempts);

    /**
     * Update the user's account state
     * @param int $id User ID..
     */
    public function updateState($id);

    /**
     * Reset the failed login attempts counter to zero (typically after a successful login).
     * @param int $id User ID.
     * @return bool True on success, false on failure.
     */
    public function resetAttempts($id);
}
