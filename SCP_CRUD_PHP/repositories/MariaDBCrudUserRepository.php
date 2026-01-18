<?php

require_once 'interfaces/IUserRepository.php';
require_once 'models/User.php';

/**
 * MariaDBCrudUserRepository
 * * Concrete implementation for Administrative User Management.
 * * Handles the lifecycle of personnel records, including hashing and state management.
 */
class MariaDBCrudUserRepository implements IUserRepository
{
    private $pdo;

    /**
     * Initializes the repository with the database connection.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Retrieves all registered users, ordered by creation date (newest first).
     * @return array List of User objects.
     */
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

    /**
     * Finds a specific user by their ID.
     * @param string $id
     * @return User|null
     */
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

    /**
     * Creates a new user record.
     * * SECURITY: The password is hashed using BCRYPT before storage.
     * * Sets the creation date to current timestamp (NOW()).
     */
    public function create(User $user)
    {
        $sql = "INSERT INTO users (id, name, lastname, email, password, rol, level, theme, tryAttempts, state, creationDate) 
                VALUES (:id, :name, :lastname, :email, :password, :rol, :level, :theme, 0, :state, NOW())";

        $stmt = $this->pdo->prepare($sql);

        // Hash the password for security
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

    /**
     * Updates an existing user's profile.
     * * LOGIC: Checks if the password field is populated. 
     * If yes: It hashes and updates the password.
     * If no: It keeps the existing password hash in the database.
     */
    public function update(User $user)
    {
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

        // Only append password update query if a new password was provided
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

    /**
     * Toggles a user's account state (Active/Locked).
     * Also resets 'tryAttempts' to 0 to unlock the account if it was locked due to failed logins.
     */
    public function updateState($id, $state)
    {
        $sql = "UPDATE users SET state = :state, tryAttempts = 0 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':state' => $state, ':id' => $id]);
    }

    /**
     * Deletes a user record.
     * * NOTE: Database Triggers (BEFORE DELETE) automatically archive this user 
     * into the 'ex_empleados' table for historical records.
     */
    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Helper to map database rows to User objects.
     * * SECURITY: Does NOT return the password hash in the object property 
     * to prevent accidental leakage in views/logs.
     */
    private function mapRowToUser($row)
    {
        $user = new User(
            $row['id'],
            $row['name'],
            $row['lastname'],
            $row['email'],
            '', // Return empty password for security
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
