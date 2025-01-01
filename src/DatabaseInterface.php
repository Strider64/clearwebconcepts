<?php
// DatabaseInterface.php
namespace clearwebconcepts;

use PDO;

interface DatabaseInterface {
    public function createPDO(): ?PDO;
}
