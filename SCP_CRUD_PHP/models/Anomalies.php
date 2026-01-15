<?php

/**
 * Class of the anomalies (Entity).
 * I represent a row in the 'anomalies' table.
 */
class Anomalies
{
    // --- Attributes ---
    private $id;          // VARCHAR(20) - e.g., "SCP-173"
    private $nickname;
    private $class;       // e.g., Euclid, Keter
    private $contencion;  // Text
    private $description; // Text
    private $doc_extensa; // Path to document file
    private $img_url;     // Path to image
    private $id_sitio;    // INT - Foreign Key

    // --- Constructor ---
    public function __construct($id, $nickname, $class, $contencion, $description, $doc_extensa, $img_url, $id_sitio)
    {
        $this->id = $id;
        $this->nickname = $nickname;
        $this->class = $class;
        $this->contencion = $contencion;
        $this->description = $description;
        $this->doc_extensa = $doc_extensa;
        $this->img_url = $img_url;
        $this->id_sitio = $id_sitio;
    }

    // ==================== GETTERS ====================

    /**
     * I get the anomaly ID (e.g., "SCP-001").
     */
    public function getId()
    {
        return $this->id;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getContencion()
    {
        return $this->contencion;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getDocExtensa()
    {
        return $this->doc_extensa;
    }

    public function getImgUrl()
    {
        return $this->img_url;
    }

    public function getIdSitio()
    {
        return $this->id_sitio;
    }

    // ==================== SETTERS ====================

    /**
     * I set the ID. 
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function setContencion($contencion)
    {
        $this->contencion = $contencion;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setDocExtensa($doc_extensa)
    {
        $this->doc_extensa = $doc_extensa;
    }

    public function setImgUrl($img_url)
    {
        $this->img_url = $img_url;
    }

    public function setIdSitio($id_sitio)
    {
        $this->id_sitio = $id_sitio;
    }

    // ==================== HELPER METHODS ====================

    /**
     * I check if this anomaly has an image assigned.
     * @return bool
     */
    public function hasImage()
    {
        return !empty($this->img_url);
    }
}
