<?php

require_once 'interfaces/IAnomaliesRepository.php';
require_once 'models/Anomalies.php';

class MariaDBCrudAnomaliesRepository implements IAnomaliesRepository
{
    private $pdo;

    /**
     * I initialize the repository with the database connection.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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

    public function update(Anomalies $anomalies)
    {
        $sql = "UPDATE anomalies SET 
                nickname = :nick, 
                class = :class, 
                contencion = :cont, 
                description = :desc, 
                doc_extensa = :doc, 
                img_url = :img, 
                id_sitio = :site 
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nick'  => $anomalies->getNickname(),
            ':class' => $anomalies->getClass(),
            ':cont'  => $anomalies->getContencion(),
            ':desc'  => $anomalies->getDescription(),
            ':doc'   => $anomalies->getDocExtensa(),
            ':img'   => $anomalies->getImgUrl(),
            ':site'  => $anomalies->getIdSitio(),
            ':id'    => $anomalies->getId()
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM anomalies WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * I map a database row to an Anomalies object.
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
