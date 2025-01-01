<?php

namespace clearwebconcepts;

use JetBrains\PhpStorm\NoReturn;
use PDO;

trait CheckStatus
{
    public function check_login_token(): bool
    {
        // Check for the presence of the cookie and the session key
        if (isset($_COOKIE['login_token']) && isset($_SESSION['login_token'])) {
            // Verify the token against the stored value
            if ($_COOKIE['login_token'] === $_SESSION['login_token']) {
                return true;
            }
        }

        return false;
    }

    public function check_security_level(array $required_levels): bool
    {
        // If there's no login token present, or it's invalid, return false
        if (!$this->check_login_token()) {
            return false;
        }

        // Get the login token from the session or cookie
        $login_token = $_SESSION['login_token'];

        // Prepare the SQL statement to retrieve the security level for the given token
        $stmt = $this->pdo->prepare("SELECT security FROM admins WHERE token = :token");
        $stmt->bindParam(':token', $login_token);
        $stmt->execute();

        // Fetch the security level from the database
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if we have a result and the security level is in the array of required levels
        if ($result && in_array($result['security'], $required_levels, true)) {
            return true;
        }

        return false;
    }

    #[NoReturn] public function redirect_based_on_security_level()
    {
        if (!$this->check_login_token()) {
            header('Location: login.php');
            exit();
        }

        // Get the login token from the session or cookie
        $login_token = $_SESSION['login_token'];

        // Prepare the SQL statement to retrieve the security level for the given token
        $stmt = $this->pdo->prepare("SELECT security FROM admins WHERE token = :token");
        $stmt->bindParam(':token', $login_token);
        $stmt->execute();

        // Fetch the security level from the database
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            // Redirect based on security level
            if ($result['security'] === 'sysop') {
                header('Location: dashboard.php');
            } elseif ($result['security'] === 'member') {
                header('Location: member.php');
            } else {
                // If the user is a newbie or any other security level, redirect to login
                header('Location: login.php');
            }
            exit();
        } else {
            // If the token is invalid, redirect to login
            header('Location: login.php');
            exit();
        }
    }
}

