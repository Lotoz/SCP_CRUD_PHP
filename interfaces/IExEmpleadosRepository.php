<?php

/**
 * Interfaz para la tabla de ex-empleados
 */
interface IExEmpleadosRepository
{

    /**
     * Mostrar base de datos
     * hace una consulta a la base de datos y trae todos sus datos
     */
    public function allData();
    /**
     * Crear nuevo item
     */
    public function createExempleados(ExEmpleados $exEmpleados);
    /**
     * Editar object
     */
    public function editExempleados($id);
    /**
     * Eliminar un objeto
     */
    public function deleteExempleados($id);
}
