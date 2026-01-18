<?php

require_once 'interfaces/ILoginUserRepository.php';
require_once 'models/User.php';
require_once 'config/Database.php';

/**
 * MariaDBILoginUserRepository
 * * Concrete implementation of ILoginUserRepository for MariaDB.
 * * Dedicated to authentication operations: fetching credentials, registering users,
 * and managing login attempts/locking.
 */
class MariaDBILoginUserRepository implements ILoginUserRepository
{
    private $conn;
    private $table = 'users';

    /**
     * Constructor - Initializes the DB connection via Dependency Injection.
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->conn = $pdo;
    }

    /**
     * Retrieves a user by their email address.
     * Used primarily during registration checks to prevent duplicate emails.
     * @param string $email
     * @return User|null
     * @throws Exception on DB error.
     */
    public function getByEmail($email)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $this->mapRowToUser($row);
            }
            return null;
        } catch (PDOException $e) {
            throw new Exception("Error finding user by email: " . $e->getMessage());
        }
    }

    /**
     * Retrieves a user by their unique ID (username/badge number).
     * Primary method for the login process.
     * @param string $id
     * @return User|null
     * @throws Exception on DB error.
     */
    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                return $this->mapRowToUser($row);
            }

            return null;
        } catch (PDOException $e) {
            throw new Exception("Error finding user by ID: " . $e->getMessage());
        }
    }

    /**
     * Registers a new user in the database.
     * * SECURITY: Automatically hashes the password using BCRYPT before storage.
     * @param User $user
     * @return bool True on success.
     * @throws Exception on DB error.
     */
    public function save(User $user)
    {
        $query = "INSERT INTO " . $this->table . " 
              (id, name, lastname, email, password, rol, theme, tryAttempts, state, creationDate) 
              VALUES 
              (:id, :name, :lastname, :email, :password, :rol, :theme, :tryAttempts, :state, :creationDate)";

        try {
            $stmt = $this->conn->prepare($query);

            // Hash the password securely
            $hashedPassword = password_hash($user->getPassword(), PASSWORD_BCRYPT);

            // Bind parameters
            $stmt->bindParam(':id', $user->getId(), PDO::PARAM_STR);
            $stmt->bindParam(':name', $user->getname(), PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $user->getlastname(), PDO::PARAM_STR);
            $stmt->bindParam(':email', $user->getEmail(), PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':rol', $user->getRol(), PDO::PARAM_STR);
            $stmt->bindParam(':theme', $user->getTheme(), PDO::PARAM_STR);
            $stmt->bindParam(':tryAttempts', $user->gettryAttempts(), PDO::PARAM_INT);
            $stmt->bindParam(':state', $user->isstate(), PDO::PARAM_BOOL);
            $stmt->bindParam(':creationDate', $user->getcreationDate(), PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error saving user: " . $e->getMessage());
        }
    }

    /**
     * Updates the counter for failed login attempts.
     * Used by AuthController to track potential brute-force attacks.
     * @param string $id User ID.
     * @param int $attempts New count.
     * @return bool
     */
    public function updateAttempts($id, $attempts)
    {
        $query = "UPDATE " . $this->table . " SET tryAttempts = :attempts WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':attempts', $attempts, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating attempts: " . $e->getMessage());
        }
    }

    /**
     * Locks the user account (sets state = 0).
     * Triggered when failed attempts exceed the security threshold.
     * @param string $id
     * @return bool
     */
    public function updateState($id)
    {
        $query = "UPDATE " . $this->table . " SET state = 0 WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error locking user account: " . $e->getMessage());
        }
    }

    /**
     * Resets failed login attempts to 0.
     * Called upon successful login.
     * @param string $id
     * @return bool
     */
    public function resetAttempts($id)
    {
        $query = "UPDATE " . $this->table . " 
              SET tryAttempts = 0
              WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error resetting attempts: " . $e->getMessage());
        }
    }

    // ==================== PRIVATE HELPER METHODS ====================

    /**
     * Maps a database row (array) to a User object.
     * @param array $row
     * @return User
     */
    private function mapRowToUser($row)
    {
        $user = new User(
            $row['id'],
            $row['name'],
            $row['lastname'],
            $row['email'],
            $row['password'], // Password hash is needed here for verification later
            $row['level'],
            $row['rol'],
            $row['theme']
        );

        $user->settryAttempts($row['tryAttempts']);
        $user->setstate((bool)$row['state']);
        $user->setcreationDate($row['creationDate']);

        return $user;
    }
}
