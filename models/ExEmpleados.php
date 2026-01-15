<?php

/**
 * Class about ex-employers (Entity)
 * I represent a row in the 'ex_empleados' table.
 */
class ExEmpleados
{
    // ==================== Attributes ====================
    private $id; // INT Auto-increment
    private $name;
    private $lastname;
    private $rol;
    private $level;
    private $fecha_eliminacion;

    /**
     * Constructor about class
     */
    public function __construct($id, $name, $lastname, $rol, $level, $fecha_eliminacion)
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->rol = $rol;
        $this->level = $level;
        $this->fecha_eliminacion = $fecha_eliminacion;
    }

    // ==================== GETTERS ====================

    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getLastname()
    {
        return $this->lastname;
    }

    public function getFullName()
    {
        return $this->name . ' ' . $this->lastname;
    }

    public function getRol()
    {
        return $this->rol;
    }
    public function getLevel()
    {
        return $this->level;
    }
    public function getFechaEliminacion()
    {
        return $this->fecha_eliminacion;
    }

    // ==================== SETTERS ====================

    public function setId($id)
    {
        $this->id = $id;
    }
    public function setName($name)
    {
        $this->name = $name;
    }
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }
    public function setRol($rol)
    {
        $this->rol = $rol;
    }
    public function setLevel($level)
    {
        $this->level = $level;
    }
    public function setFechaEliminacion($fecha)
    {
        $this->fecha_eliminacion = $fecha;
    }
}
