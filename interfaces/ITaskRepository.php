<?php

interface ITaskRepository
{
    /**
     * Get all tasks (Generic - useful for Admin).
     * @return array Array of Task objects.
     */
    public function getAll();

    /**
     * Get all tasks assigned to a specific user.
     * Essential for the User Dashboard.
     * @param mixed $userId
     * @return array Array of Task objects.
     */
    public function getByUserId($userId);

    /**
     * Find a specific task by its ID.
     * @param int $id
     * @return Task|null Returns the Task object or null if not found.
     */
    public function getById($id);

    /**
     * Get all tasks that are not completed.
     * @return array Array of Task objects.
     */
    public function getNotCompletedTasks($userId);
    /**
     * Create a new task in the database.
     * @param Task $task
     * @return bool True on success, false on failure.
     */
    public function create(Task $task);

    /**
     * Update an existing task.
     * We pass the whole Task object containing the new data.
     * @param Task $task
     * @return bool True on success, false on failure.
     */
    public function update(Task $task);

    /**
     * Delete a task by its ID.
     * @param int $id
     * @return bool True on success, false on failure.
     */
    public function delete($id);
}
