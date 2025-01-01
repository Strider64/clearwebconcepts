<?php

namespace clearwebconcepts;

use JetBrains\PhpStorm\NoReturn;
use PDO;


class LoginRepository implements LoginRepositoryInterface
{
    use CheckStatus;
    private string $table = 'admins'; // Replace with your actual table name

    protected PDO $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function verify_credentials($username, $password): bool
    {
        $sql = "SELECT id, password FROM admins WHERE username =:username LIMIT 1";
        $user = $this->retrieve_credentials($sql, $username);
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(); // prevent session fixation attacks
            $_SESSION['user_id'] = $user['id'];
            return true;
        }

        return false;
    }


    protected function retrieve_credentials(string $sql, string $username): ?array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false ? $result : null;
    }

    public function store_token_in_database($user_id, $token): bool
    {
        $sql = "UPDATE " . $this->table . " SET token=:token WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['token' => $token, 'id' => $user_id]);
        return true;
    }



    // Logout Method
    #[NoReturn] public function logoff(): void
    {
        // Delete the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Remove the token from the database (assuming you are storing it in the 'token' column of the 'admins' table)
        $sql = "UPDATE " . $this->table . " SET token=NULL WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $_SESSION['user_id']]);
        // Unset all the session variables
        $_SESSION = [];
        // Destroy the session
        session_destroy();

        // Redirect the user to the home page
        header('Location: index.php');
        exit;
    }


}