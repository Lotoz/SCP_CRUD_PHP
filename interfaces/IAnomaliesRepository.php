<?php

/**
 * Interface IAnomaliesRepository
 * I define the contract for accessing Anomalies data.
 */
interface IAnomaliesRepository
{
    /**
     * I retrieve all anomalies from the database.
     * @return array Array of Anomalies objects.
     */
    public function getAll();

    /**
     * I find a specific anomaly by its ID (VARCHAR).
     * @param string $id
     * @return Anomalies|null
     */
    public function getById($id);

    /**
     * I retrieve all anomalies contained in a specific Site.
     * @param int $siteId
     * @return array
     */
    public function getBySiteId($siteId);

    /**
     * I create a new anomaly record.
     * @param Anomalies $anomalies
     * @return bool
     */
    public function create(Anomalies $anomalies);

    /**
     * I update an existing anomaly.
     * @param Anomalies $anomalies
     * @return bool
     */
    public function update(Anomalies $anomalies);

    /**
     * I delete an anomaly by its ID.
     * @param string $id
     * @return bool
     */
    public function delete($id);
}
