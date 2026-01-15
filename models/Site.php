<?php

/**
 * Class Site (Entity)
 * I represent a row in the 'sitio' table.
 */
class Site
{
    // --- Attributes ---
    private $id;               // INT Auto-Increment
    private $name_sitio;       // VARCHAR
    private $ubicacion;        // TEXT
    private $id_administrador; // VARCHAR (User ID FK)

    /**
     * I construct the Site object.
     */
    public function __construct($id, $name_sitio, $ubicacion, $id_administrador)
    {
        $this->id = $id;
        $this->name_sitio = $name_sitio;
        $this->ubicacion = $ubicacion;
        $this->id_administrador = $id_administrador;
    }

    // ==================== GETTERS ====================

    public function getId()
    {
        return $this->id;
    }

    public function getNameSitio()
    {
        return $this->name_sitio;
    }

    public function getUbicacion()
    {
        return $this->ubicacion;
    }

    public function getIdAdministrador()
    {
        return $this->id_administrador;
    }

    // ==================== SETTERS ====================

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNameSitio($name_sitio)
    {
        $this->name_sitio = $name_sitio;
    }

    public function setUbicacion($ubicacion)
    {
        $this->ubicacion = $ubicacion;
    }

    public function setIdAdministrador($id_administrador)
    {
        $this->id_administrador = $id_administrador;
    }
}
