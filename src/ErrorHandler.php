<?php
// ErrorHandler.php
namespace clearwebconcepts;

use PDOException;
use Throwable;
use JsonException;

class ErrorHandler implements ErrorHandlerInterface
{
    public function handleException(Throwable $e): void
    {
        if ($e instanceof PDOException) {
            error_log('PDO Error: ' . $e->getMessage());
        } elseif ($e instanceof JsonException) {
            error_log('JSON Error: ' . $e->getMessage());
        } else {
            error_log('General Error: ' . $e->getMessage());
        }
    }
}
