<?php

class Task
{

    // ===== Attributes =====
    private $id;
    private $description;
    private $completado;
    private $id_usuario;
    /**
     * Constructor of the class
     */
    public function __construct($id, $description, $completado, $id_usuario)
    {
        $this->id = $id;
        $this->description = $description;
        $this->completado = $completado;
        $this->id_usuario = $id_usuario;
    }
}
