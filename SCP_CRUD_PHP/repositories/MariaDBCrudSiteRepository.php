<?php

require_once 'interfaces/ISiteRepository.php';
require_once 'models/Site.php';

class MariaDBCrudSiteRepository implements ISiteRepository
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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

    public function create(Site $site)
    {
        // I do not include 'id' because it is AUTO_INCREMENT
        $sql = "INSERT INTO sitio (name_sitio, ubicacion, id_administrador) 
                VALUES (:name, :loc, :admin)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':name'  => $site->getNameSitio(),
            ':loc'   => $site->getUbicacion(),
            ':admin' => $site->getIdAdministrador()
        ]);
    }

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

    public function delete($id)
    {
        $sql = "DELETE FROM sitio WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * I map a DB row to a Site object.
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
