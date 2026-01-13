<?php

/**
 * Clase de un sitio
 */
class Site
{
    //Atributos
    private $id;
    private $name_sitio;
    private $ubicacion;
    private $id_administrador;
    public function __construct($id, $name_sitio, $ubicacion, $id_administrador)
    {
        $this->id = $id;
        $this->name_sitio = $name_sitio;
        $this->ubicacion = $ubicacion;
        $this->id_administrador = $id_administrador;
    }

    // ==================== GETTERS ====================

    // ==================== SETTERS ====================
}
