<?php

/**
 * Interface IExEmpleadosRepository
 */
interface IExEmpleadosRepository
{
    public function getAll();
    public function getById($id);
    public function create(ExEmpleados $exEmpleados);
    public function update(ExEmpleados $exEmpleados);
    public function delete($id);
}
