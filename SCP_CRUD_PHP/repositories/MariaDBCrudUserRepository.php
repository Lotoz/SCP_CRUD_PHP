<?php

require_once 'interfaces/IUserRepository.php';
require_once 'models/User.php';

class MariaDBCrudUserRepository implements IUserRepository
{
    private $pdo;

    /**
     * I inject the connection in the constructor.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM users ORDER BY creationDate DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->mapRowToUser($row);
        }
        return $users;
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return $this->mapRowToUser($row);
        }
        return null;
    }

    public function create(User $user)
    {
        $sql = "INSERT INTO users (id, name, lastname, email, password, rol, level, theme, tryAttempts, state, creationDate) 
                VALUES (:id, :name, :lastname, :email, :password, :rol, :level, :theme, 0, :state, NOW())";

        $stmt = $this->pdo->prepare($sql);

        // I hash the password before saving
        $hashedPassword = password_hash($user->getPassword(), PASSWORD_BCRYPT);

        return $stmt->execute([
            ':id'       => $user->getId(),
            ':name'     => $user->getname(),
            ':lastname' => $user->getlastname(),
            ':email'    => $user->getEmail(),
            ':password' => $hashedPassword,
            ':rol'      => $user->getRol(),
            ':level'    => $user->getLevel(),
            ':theme'    => $user->getTheme(),
            ':state'    => $user->isstate() ? 1 : 0
        ]);
    }

    public function update(User $user)
    {
        // I check if the password needs to be updated or kept
        // If the object has a password (not empty), I update it. If not, I keep the old one.
        $passwordSql = "";
        $params = [
            ':name'     => $user->getname(),
            ':lastname' => $user->getlastname(),
            ':email'    => $user->getEmail(),
            ':rol'      => $user->getRol(),
            ':level'    => $user->getLevel(),
            ':theme'    => $user->getTheme(),
            ':state'    => $user->isstate() ? 1 : 0,
            ':id'       => $user->getId()
        ];

        if (!empty($user->getPassword())) {
            $passwordSql = ", password = :password";
            $params[':password'] = password_hash($user->getPassword(), PASSWORD_BCRYPT);
        }

        $sql = "UPDATE users SET 
                name = :name, 
                lastname = :lastname, 
                email = :email, 
                rol = :rol, 
                level = :level, 
                theme = :theme, 
                state = :state 
                $passwordSql
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function updateState($id, $state)
    {
        $sql = "UPDATE users SET state = :state, tryAttempts = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':state' => $state, ':id' => $id]);
    }

    public function delete($id)
    {
        // Triggers in DB will handle the backup to 'ex_empleados'
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    private function mapRowToUser($row)
    {
        $user = new User(
            $row['id'],
            $row['name'],
            $row['lastname'],
            $row['email'],
            '', // We don't return the password hash for security reasons in listing
            $row['level'],
            $row['rol'],
            $row['theme']
        );
        $user->settryAttempts($row['tryAttempts']);
        $user->setstate($row['state']);
        $user->setcreationDate($row['creationDate']);
        return $user;
    }
}
