<?php

/**
 * Modelo User - Representa un usuario en la base de datos
 * Contiene solo propiedades y getters/setters, no lógica de BD
 */
class User
{
    private $id;
    private $nombre;
    private $apellido;
    private $email;
    private $password;
    private $level;
    private $rol;
    private $theme;
    private $intentosFallidos;
    private $activo;
    private $fechaCreacion;


    /**
     * Constructor
     */
    public function __construct($id = null, $nombre = '', $apellido = '', $email = '', $password = '', $level = null, $rol = '', $theme = 'gears')
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->password = $password;
        $this->level = $level;
        $this->rol = $rol;
        $this->theme = $theme;
        $this->intentosFallidos = 0;
        $this->activo = false;
        $this->fechaCreacion = date('Y-m-d H:i:s');
    }

    // ==================== GETTERS ====================

    public function getId()
    {
        return $this->id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getApellido()
    {
        return $this->apellido;
    }

    public function getNombreCompleto()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }
    public function getLevel()
    {
        return $this->level;
    }

    public function getRol()
    {
        return $this->rol;
    }

    public function getTheme()
    {
        return $this->theme;
    }

    public function getIntentosFallidos()
    {
        return $this->intentosFallidos;
    }

    public function isActivo()
    {
        return $this->activo;
    }

    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    // ==================== SETTERS ====================

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function setApellido($apellido)
    {
        $this->apellido = $apellido;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setRol($rol)
    {
        $this->rol = $rol;
    }
    public function setLevel($level)
    {
        $this->level = $level;
    }

    public function setTheme($theme)
    {
        $this->theme = $theme;
    }

    public function setIntentosFallidos($intentos)
    {
        $this->intentosFallidos = $intentos;
    }

    public function setActivo($activo)
    {
        $this->activo = $activo;
    }

    public function setFechaCreacion($fecha)
    {
        $this->fechaCreacion = $fecha;
    }


    // ==================== MÉTODOS DE CLASE ====================

    /**
     * Verificar si la contraseña coincide (considerando hash)
     * @param string $passwordIngresada La contraseña a verificar
     * @return bool true si coincide
     */
    public function verificarPassword($passwordIngresada)
    {
        return password_verify($passwordIngresada, $this->password);
    }
}
