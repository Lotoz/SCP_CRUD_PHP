<?php

/**
 * Class of the anomalies
 */
class Anomalies
{

    // --- Attributes ---

    private $id;
    private $nickname;
    private $class;
    private $contencion;
    private $description;
    private $doc_extensa;
    private $img_url;
    private $id_sitio;

    // --- Constructor ---
    public function __construct($id, $nickname, $class, $contencion, $descripcion, $doc_extensa, $img_url, $id_sitio)
    {
        $this->id = $id;
        $this->nickname = $nickname;
        $this->class = $class;
        $this->contencion = $contencion;
        $this->descripcion = $descripcion;
        $this->doc_extensa = $doc_extensa;
        $this->img_url = $img_url;
        $this->id_sitio = $id_sitio;
    }

    // ==================== GETTERS ====================

    // ==================== SETTERS ====================


}
