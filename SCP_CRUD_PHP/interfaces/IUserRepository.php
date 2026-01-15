<?php

/**
 * Interface IUserRepository
 * I define the contract for the User Administration CRUD.
 */
interface IUserRepository
{
    public function getAll();
    public function getById($id);
    public function create(User $user);
    public function update(User $user);
    public function delete($id);

    // I added this to handle state changes (Ban/Unban) easily
    public function updateState($id, $state);
}
