<?php

/**
 * Anomalies model class representing an anomaly entity.
 * This class corresponds to a row in the 'anomalies' table and encapsulates anomaly data.
 */
class Anomalies
{
    // --- Properties ---
    private $id;          // Unique identifier for the anomaly, e.g., "SCP-173"
    private $nickname;
    private $class;       // Classification level, e.g., Euclid, Keter
    private $contencion;  // Containment procedures text
    private $description; // Description of the anomaly
    private $doc_extensa; // Path to extended documentation file
    private $img_url;     // Path to associated image
    private $id_sitio;    // Foreign key to the site ID

    /**
     * Constructor for the Anomalies class.
     */
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

    // ==================== GETTER METHODS ====================

    /**
     * Retrieves the unique identifier of the anomaly.
     * @return string The anomaly ID, such as "SCP-001"
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the nickname of the anomaly.
     * @return string
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Gets the classification level of the anomaly.
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Gets the containment procedures for the anomaly.
     * @return string
     */
    public function getContencion()
    {
        return $this->contencion;
    }

    /**
     * Gets the description of the anomaly.
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Gets the path to the extended documentation file.
     * @return string
     */
    public function getDocExtensa()
    {
        return $this->doc_extensa;
    }

    /**
     * Gets the path to the associated image.
     * @return string
     */
    public function getImgUrl()
    {
        return $this->img_url;
    }

    /**
     * Gets the foreign key to the site ID.
     * @return int
     */
    public function getIdSitio()
    {
        return $this->id_sitio;
    }

    // ==================== SETTER METHODS ====================

    /**
     * Sets the unique identifier of the anomaly.
     * @param string $id The anomaly ID
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Sets the nickname of the anomaly.
     * @param string $nickname
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    }

    /**
     * Sets the classification level of the anomaly.
     * @param string $class
     */
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

    // ==================== UTILITY METHODS ====================

    /**
     * Checks if the anomaly has an associated image.
     * @return bool True if an image URL is set, false otherwise
     */
    public function hasImage()
    {
        return !empty($this->img_url);
    }
}
