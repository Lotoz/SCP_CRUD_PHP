<?php

require_once 'DatabaseConnectionException.php';

/**
 * Database
 *
 * Responsible for creating and returning a configured PDO connection to the
 * application's MySQL database. This class centralizes connection details
 * (host, database name, credentials) so other parts of the app can simply
 * request a PDO instance. On failure it throws a DatabaseConnectionException
 * that higher layers can catch and handle (for logging, retries, or graceful
 * error pages).
 */
class Database
{
    private $host = "localhost";
    private $db_name = "scp_data";
    private $username = "view";
    private $password = "yX/I!geU1xKbG3F[";
    private $conn;

    /**
     * Get a PDO connection to the database.
     *
     * This method initializes a PDO instance configured to:
     * - throw exceptions on errors (PDO::ERRMODE_EXCEPTION)
     * - use UTF-8 for the connection
     *
     * Returning a single connection from here keeps credentials and connection
     * logic in one place and simplifies testing or swapping the driver later.
     *
     * @return PDO The active PDO connection
     * @throws DatabaseConnectionException If a PDOException occurs while connecting
     */
    public function getConnection()
    {
        $this->conn = null;

        try {
            // Build PDO with the configured parameters
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );

            // Configure PDO to throw exceptions on errors so callers can catch them
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Ensure connection uses UTF-8 encoding
            $this->conn->exec("SET NAMES utf8");
        } catch (PDOException $e) {
            // Wrap and rethrow with a domain-specific exception
            throw new DatabaseConnectionException(
                "Database connection error: " . $e->getMessage()
            );
        }

        return $this->conn;
    }
}
