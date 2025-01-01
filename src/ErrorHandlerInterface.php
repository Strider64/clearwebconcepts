<?php
// ErrorHandlerInterface.php
namespace clearwebconcepts;

use Throwable;

interface ErrorHandlerInterface {
    public function handleException(Throwable $e): void;
}

