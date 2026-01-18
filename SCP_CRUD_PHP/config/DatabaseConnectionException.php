<?php

/**
 * DatabaseConnectionException
 *
 * Domain-specific exception used to signal problems creating a database
 * connection. Wrapping PDOException in this class allows higher-level code to
 * catch database connection failures specifically and separate them from other
 * error types (validation, logic errors, etc.).
 *
 * Usage: the Database class throws this exception when it cannot establish a
 * PDO connection; controllers or bootstrap code can catch it to show a friendly
 * error page or trigger alternate behavior.
 */
class DatabaseConnectionException extends Exception
{
    // Return the inner message. Kept as a convenience for compatibility with
    // existing code that expects an errorMessage() method.
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
