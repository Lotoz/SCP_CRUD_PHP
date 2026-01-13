<?php

/**
 * Interfaz para manejar los sitios
 */
interface ISitesRepository
{

    /**
     * Mostrar base de datos
     * hace una consulta a la base de datos y trae todos sus datos
     */
    public function allData();
    /**
     * Crear nuevo item
     */
    public function createSite(Site $site);
    /**
     * Editar object
     */
    public function editSite($id);
    /**
     * Eliminar un objeto
     */
    public function deletSite($id);
}
