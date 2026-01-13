<?php

/**
 * Modelo User - Representa un usuario en la base de datos
 * Contiene solo propiedades y getters/setters, no lógica de BD
 */
class User
{
    private $id;
    private $name;
    private $lastname;
    private $email;
    private $password;
    private $level;
    private $rol;
    private $theme;
    private $tryAttempts;
    private $state;
    private $creationDate;


    /**
     * Constructor
     */
    public function __construct($id = null, $name = '', $lastname = '', $email = '', $password = '', $level = null, $rol = '', $theme = 'gears')
    {
        $this->id = $id;
        $this->name = $name;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
        $this->level = $level;
        $this->rol = $rol;
        $this->theme = $theme;
        $this->tryAttempts = 0;
        $this->state = false;
        $this->creationDate = date('Y-m-d H:i:s');
    }

    // ==================== GETTERS ====================

    public function getId()
    {
        return $this->id;
    }

    public function getname()
    {
        return $this->name;
    }

    public function getlastname()
    {
        return $this->lastname;
    }

    public function getFullName()
    {
        return $this->name . ' ' . $this->lastname;
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

    public function gettryAttempts()
    {
        return $this->tryAttempts;
    }

    public function isstate()
    {
        return $this->state;
    }

    public function getcreationDate()
    {
        return $this->creationDate;
    }

    // ==================== SETTERS ====================

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setname($name)
    {
        $this->name = $name;
    }

    public function setlastname($lastname)
    {
        $this->lastname = $lastname;
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

    public function settryAttempts($intentos)
    {
        $this->tryAttempts  = $intentos;
    }

    public function setstate($state)
    {
        $this->state = $state;
    }

    public function setcreationDate($fecha)
    {
        $this->creationDate = $fecha;
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
    /**
     * Descriptador de password, para mostar la password sin hash en el crud
     */
    public function desencriptar($password) {}
}
