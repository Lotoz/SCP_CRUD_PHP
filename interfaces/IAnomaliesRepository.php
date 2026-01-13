<?php

/**
 * 
 */
interface IAnomaliesRepository
{

    /**
     * Mostrar base de datos
     * hace una consulta a la base de datos y trae todos sus datos
     */
    public function allData();

    /**
     * Crear nuevo item
     */
    public function createAnomalies(Anomalies $anomalie);
    /**
     * Editar object
     */
    public function editAnomalies($id);
    /**
     * Eliminar un objeto
     */
    public function deleteAnomalies($id);
    /**
     * Busca un objeto especifico
     */
    public function search($id);
}
