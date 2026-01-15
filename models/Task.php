<?php

class Task
{
    // ===== Attributes =====
    private $id;
    private $description;
    private $completado;
    private $id_usuario;

    /**
     * Class constructor.
     * @param int $id The task ID 
     * @param string $description The task description.
     * @param int $completado Status (0 for pending, 1 for completed).
     * @param string $id_usuario The associated user ID (Foreign Key).
     */
    public function __construct($id, $description, $completado, $id_usuario)
    {
        $this->id = $id;
        $this->description = $description;
        $this->completado = $completado;
        $this->id_usuario = $id_usuario;
    }

    // ===== Getters =====

    /**
     * Get the task ID.
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the task description.
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the completion status as stored in DB.
     * @return int (0 or 1)
     */
    public function getCompletado()
    {
        return $this->completado;
    }

    /**
     * specific helper for the View or logic checks.
     * Returns true if the task is completed (1), false otherwise.
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completado == 1;
    }

    /**
     * Get the ID of the user who owns this task.
     * @return string
     */
    public function getIdUsuario()
    {
        return $this->id_usuario;
    }

    // ===== Setters =====

    /**
     * Set the task description.
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Set the completion status.
     * Automatically converts boolean or integer input to 0 or 1 for TINYINT compatibility.
     * @param mixed $completado
     */
    public function setCompletado($completado)
    {
        // Force value to be 0 or 1
        $this->completado = $completado ? 1 : 0;
    }

    /**
     * Set the user ID owner.
     * @param string $id_usuario
     */
    public function setIdUsuario($id_usuario)
    {
        $this->id_usuario = $id_usuario;
    }
}
