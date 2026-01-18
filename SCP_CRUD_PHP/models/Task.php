<?php

class Task
{
    // ===== Attributes =====
    private $id;
    private $description;
    private $due_date;
    private $completado;
    private $id_usuario;

    /**
     * Class constructor.
     * @param int|null $id The task ID (null for new records).
     * @param string $description The task description.
     * @param int $completado Status (0 for pending, 1 for completed).
     * @param string $id_usuario The associated user ID.
     * @param string|null $due_date The deadline date (YYYY-MM-DD) or null.
     */
    public function __construct($id, $description, $completado, $id_usuario, $due_date = null)
    {
        $this->id = $id;
        $this->description = $description;
        $this->completado = $completado;
        $this->id_usuario = $id_usuario;
        $this->due_date = empty($due_date) ? null : $due_date;
    }

    // ===== Getters =====

    public function getId()
    {
        return $this->id;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDueDate()
    {
        return $this->due_date;
    }

    public function getCompletado()
    {
        return $this->completado;
    }

    public function isCompleted()
    {
        return $this->completado == 1;
    }

    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    // ===== Setters =====

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setDueDate($due_date)
    {
        $this->due_date = empty($due_date) ? null : $due_date;
    }

    public function setCompletado($completado)
    {
        $this->completado = $completado ? 1 : 0;
    }

    public function setIdUsuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }
}
