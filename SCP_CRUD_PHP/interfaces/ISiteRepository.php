<?php

/**
 * Interface ISiteRepository
 * Defines the contract for managing Containment Site data.
 */
interface ISiteRepository
{
    /**
     * Retrieves all registered containment sites.
     */
    public function getAll();

    /**
     * Finds a specific site by its numeric ID.
     */
    public function getById($id);

    /**
     * Establishes a new Site record.
     */
    public function create(Site $site);

    /**
     * Updates site details (Name, Location, Director).
     */
    public function update(Site $site);

    /**
     * Decommissions (deletes) a site.
     */
    public function delete($id);
}
