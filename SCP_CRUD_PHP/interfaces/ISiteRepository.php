<?php

/**
 * Interface ISiteRepository
 * I define the contract for accessing Site data.
 */
interface ISiteRepository
{
    /**
     * I retrieve all sites from the database.
     */
    public function getAll();

    /**
     * I find a specific site by its ID.
     */
    public function getById($id);

    /**
     * I create a new site record.
     */
    public function create(Site $site);

    /**
     * I update an existing site.
     */
    public function update(Site $site);

    /**
     * I delete a site by its ID.
     */
    public function delete($id);
}
