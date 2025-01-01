<?php
// Database.php
namespace clearwebconcepts;

use PDO;

class Database implements DatabaseInterface
{
    use CheckStatus;
    use NavigationMenuTrait;
    private string $dsn;
    private string $username;
    private string $password;
    private array $options;

    public function __construct()
    {

        // Set your database credentials and options here
        $this->dsn = 'mysql:host=' . DATABASE_HOST . ';dbname=' . DATABASE_NAME . ';charset=utf8mb4';
        $this->username =  DATABASE_USERNAME;
        $this->password = DATABASE_PASSWORD;
        $this->options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
    }

    public function createPDO(): PDO
    {
        // No try/catch block, relying on the registered exception handler
        return new PDO($this->dsn, $this->username, $this->password, $this->options);
    }
}