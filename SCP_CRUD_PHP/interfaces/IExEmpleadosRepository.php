<?php

/**
 * Interface IExEmpleadosRepository
 * Read-only access to the historical log of terminated/former personnel.
 * Usually populated via Database Triggers when a User is deleted.
 */
interface IExEmpleadosRepository
{
    /**
     * Retrieves the full history of former employees.
     */
    public function getAll();

    /**
     * Permanently expunges a record from the history log.
     * @param string $id The ID of the former employee.
     */
    public function delete($id);
}
