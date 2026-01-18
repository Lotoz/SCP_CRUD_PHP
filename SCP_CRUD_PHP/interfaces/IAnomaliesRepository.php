<?php

/**
 * Interface IAnomaliesRepository
 * Defines the contract for accessing and manipulating SCP Anomaly data.
 */
interface IAnomaliesRepository
{
    /**
     * Retrieves all anomalies from the database.
     * @return array Array of Anomalies objects.
     */
    public function getAll();

    /**
     * Finds a specific anomaly by its SCP ID (e.g., 'SCP-173').
     * @param string $id The unique alphanumeric ID.
     * @return Anomalies|null Returns object if found, null otherwise.
     */
    public function getById($id);

    /**
     * Retrieves all anomalies contained within a specific Site.
     * @param int $siteId
     * @return array List of anomalies.
     */
    public function getBySiteId($siteId);

    /**
     * Persists a new anomaly record in the database.
     * @param Anomalies $anomalies
     * @return bool True on success.
     */
    public function create(Anomalies $anomalies);

    /**
     * Updates an existing anomaly.
     * @param Anomalies $anomaly The object with new data.
     * @param string $originalId The previous ID (required if the SCP-ID itself is being renamed).
     * @return bool
     */
    public function update(Anomalies $anomaly, $originalId);

    /**
     * Deletes an anomaly record.
     * @param string $id
     * @return bool
     */
    public function delete($id);
}
