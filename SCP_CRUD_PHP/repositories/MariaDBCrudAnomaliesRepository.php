<?php

require_once 'interfaces/IAnomaliesRepository.php';
require_once 'models/Anomalies.php';

/**
 * MariaDBCrudAnomaliesRepository
 * * Concrete implementation of the IAnomaliesRepository using PDO (MariaDB/MySQL).
 * Handles raw SQL queries, parameter binding, and object mapping for SCP Anomalies.
 */
class MariaDBCrudAnomaliesRepository implements IAnomaliesRepository
{
    private $pdo;

    /**
     * Initializes the repository with an active database connection.
     * Dependency Injection ensures this class doesn't worry about connecting, just querying.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves all anomalies ordered by ID.
     * @return array List of Anomalies objects.
     */
    public function getAll()
    {
        $sql = "SELECT * FROM anomalies ORDER BY id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $this->toObject($row);
        }
        return $list;
    }

    /**
     * Fetches a single anomaly by its unique ID (e.g., 'SCP-173').
     * @param string $id
     * @return Anomalies|null
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM anomalies WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->toObject($row);
        }
        return null;
    }

    /**
     * Finds all anomalies assigned to a specific Containment Site.
     * @param int $siteId
     * @return array
     */
    public function getBySiteId($siteId)
    {
        $sql = "SELECT * FROM anomalies WHERE id_sitio = :siteId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':siteId' => $siteId]);

        $list = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $list[] = $this->toObject($row);
        }
        return $list;
    }

    /**
     * Inserts a new anomaly record.
     * Uses prepared statements to prevent SQL Injection.
     * @param Anomalies $anomalies
     * @return bool True on success.
     */
    public function create(Anomalies $anomalies)
    {
        $sql = "INSERT INTO anomalies (id, nickname, class, contencion, description, doc_extensa, img_url, id_sitio) 
                VALUES (:id, :nick, :class, :cont, :desc, :doc, :img, :site)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id'    => $anomalies->getId(),
            ':nick'  => $anomalies->getNickname(),
            ':class' => $anomalies->getClass(),
            ':cont'  => $anomalies->getContencion(),
            ':desc'  => $anomalies->getDescription(),
            ':doc'   => $anomalies->getDocExtensa(),
            ':img'   => $anomalies->getImgUrl(),
            ':site'  => $anomalies->getIdSitio()
        ]);
    }

    /**
     * Updates an existing anomaly.
     * * CRITICAL: Supports renaming the ID (Primary Key).
     * We bind ':new_id' to the new value and ':old_id' to the original value
     * to locate the correct row in the database.
     * * @param Anomalies $anomalies The object containing new data.
     * @param string $originalId The ID of the record BEFORE this update.
     */
    public function update(Anomalies $anomalies, $originalId)
    {
        $sql = "UPDATE anomalies SET 
                id = :new_id, 
                nickname = :nick, 
                class = :class, 
                contencion = :cont, 
                description = :desc, 
                doc_extensa = :doc, 
                img_url = :img, 
                id_sitio = :site 
                WHERE id = :old_id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':new_id' => $anomalies->getId(),
            ':nick'   => $anomalies->getNickname(),
            ':class'  => $anomalies->getClass(),
            ':cont'   => $anomalies->getContencion(),
            ':desc'   => $anomalies->getDescription(),
            ':doc'    => $anomalies->getDocExtensa(),
            ':img'    => $anomalies->getImgUrl(),
            ':site'   => $anomalies->getIdSitio(),
            ':old_id' => $originalId
        ]);
    }

    /**
     * Deletes a record by ID.
     */
    public function delete($id)
    {
        $sql = "DELETE FROM anomalies WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Helper method: Maps a raw database array (assoc) to a domain Model object.
     * Keeps the code DRY (Don't Repeat Yourself).
     */
    private function toObject($row)
    {
        return new Anomalies(
            $row['id'],
            $row['nickname'],
            $row['class'],
            $row['contencion'],
            $row['description'],
            $row['doc_extensa'],
            $row['img_url'],
            $row['id_sitio']
        );
    }
}
