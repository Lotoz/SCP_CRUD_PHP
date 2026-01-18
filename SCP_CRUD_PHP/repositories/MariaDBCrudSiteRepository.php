<?php

require_once 'interfaces/ISiteRepository.php';
require_once 'models/Site.php';

/**
 * MariaDBCrudSiteRepository
 * * Concrete implementation for managing Containment Sites.
 * * Connects to the 'sitio' table in the database.
 */
class MariaDBCrudSiteRepository implements ISiteRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves all containment sites, ordered by ID.
     * @return array List of Site objects.
     */
    public function getAll()
    {
        $sql = "SELECT * FROM sitio ORDER BY id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $sites = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sites[] = $this->toObject($row);
        }
        return $sites;
    }

    /**
     * Finds a specific site by its unique ID.
     * @param int $id
     * @return Site|null
     */
    public function getById($id)
    {
        $sql = "SELECT * FROM sitio WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->toObject($row);
        }
        return null;
    }

    /**
     * Registers a new Containment Site.
     * * NOTE: The 'id' column is excluded here as it is handled by the database's AUTO_INCREMENT.
     * @param Site $site
     * @return bool
     */
    public function create(Site $site)
    {
        $sql = "INSERT INTO sitio (name_sitio, ubicacion, id_administrador) 
                VALUES (:name, :loc, :admin)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name'  => $site->getNameSitio(),
            ':loc'   => $site->getUbicacion(),
            ':admin' => $site->getIdAdministrador()
        ]);
    }

    /**
     * Updates an existing Site's details.
     * @param Site $site
     * @return bool
     */
    public function update(Site $site)
    {
        $sql = "UPDATE sitio SET 
                name_sitio = :name, 
                ubicacion = :loc, 
                id_administrador = :admin
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name'  => $site->getNameSitio(),
            ':loc'   => $site->getUbicacion(),
            ':admin' => $site->getIdAdministrador(),
            ':id'    => $site->getId()
        ]);
    }

    /**
     * Deletes a site record.
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $sql = "DELETE FROM sitio WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Helper to map database rows to Site objects.
     */
    private function toObject($row)
    {
        return new Site(
            $row['id'],
            $row['name_sitio'],
            $row['ubicacion'],
            $row['id_administrador']
        );
    }
}
