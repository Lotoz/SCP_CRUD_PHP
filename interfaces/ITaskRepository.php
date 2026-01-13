<?php

/**
 * 
 */
interface ITaskRepository
{

    /**
     * Mostrar base de datos
     * hace una consulta a la base de datos y trae todos sus datos
     */
    public function allData();

    /**
     * Crear nuevo item
     */
    public function createTask(Task $task);
    /**
     * Editar object
     */
    public function editTask($id);
    /**
     * Eliminar un objeto
     */
    public function deleteTask($id);
    /**
     * Busca un objeto especifico
     */
    public function search($id);
}
