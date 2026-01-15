<?php

/**
 * Custom Exception for Database Connection errors.
 * It extends the standard Exception class.
 */
class DatabaseConnectionException extends Exception
{
    public function errorMessage()
    {
        return $this->getMessage();
    }
}
