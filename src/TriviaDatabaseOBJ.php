<?php
namespace clearwebconcepts;

use PDO;

class TriviaDatabaseOBJ
{
    private int $id;
    private int $userId;
    private bool $hidden;
    private string $question;
    private string $ans1;
    private string $ans2;
    private string $ans3;
    private string $ans4;
    private string $correct;
    private string $category;
    private string $dateAdded;

    private static string $table = "brainwaveblitz";
    private array $params;

    private PDO $pdo;

    public function __construct(PDO $pdo, array $args = [])
    {
        $this->pdo = $pdo;
        $this->params = [];

        foreach ($args as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
                $this->params[$key] = $value;
            }
        }
    }

    public function fetchQuestions(string $category = 'lego'): array
    {
        $sql = "SELECT id, user_id, hidden, question, ans1, ans2, ans3, ans4, category  
           FROM " . self::$table . "  
           WHERE category = :category";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['category' => $category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchAllQuestions(): array
    {
        $sql = "SELECT * FROM " . self::$table . " ORDER BY date_added DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchCorrectAnswer(int $id): array
    {
        $sql = "SELECT id, correct FROM " . self::$table . " WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(): bool
    {
        $columns = implode(', ', array_keys(array_filter($this->params, function ($key) {
            return $key !== 'id';
        }, ARRAY_FILTER_USE_KEY)));
        $placeholders = implode(', ', array_map(function ($key) {
            return ":$key";
        }, array_keys(array_filter($this->params, function ($key) {
            return $key !== 'id';
        }, ARRAY_FILTER_USE_KEY))));
        $sql = "INSERT INTO " . self::$table . " ($columns) VALUES ($placeholders)";

        $stmt = $this->pdo->prepare($sql);
        foreach (array_filter($this->params, function ($key) {
            return $key !== 'id';
        }, ARRAY_FILTER_USE_KEY) as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        return $stmt->execute();
    }

    public function update(): bool
    {
        $columns = implode(', ', array_map(function ($key) {
            return "$key = :$key";
        }, array_keys(array_filter($this->params, function ($key) {
            return $key !== 'id';
        }, ARRAY_FILTER_USE_KEY))));
        $sql = "UPDATE " . self::$table . " SET $columns WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        foreach ($this->params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        return $stmt->execute();
    }
}

