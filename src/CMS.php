<?php /** @noinspection ALL */

namespace clearwebconcepts;

use Exception;
use JetBrains\PhpStorm\Pure;
use DateTime;
use DateTimeZone;
use PDO;

class CMS implements CMSInterface
{
    protected string $table = "cms"; // Table Name:
    protected array $db_columns = ['id', 'category', 'user_id', 'thumb_path', 'image_path', 'Model', 'ExposureTime', 'Aperture', 'ISO', 'FocalLength', 'author', 'heading', 'content', 'data_updated', 'date_added'];
    public $id;
    public $user_id;
    public $page;
    public $category;
    public $thumb_path;
    public $image_path;
    public $Model;
    public $ExposureTime;
    public $Aperture;
    public $ISO;
    public $FocalLength;
    public $author;
    public $heading;
    public $content;
    public $date_updated;
    public $date_added;

    protected PDO $pdo;

    /*
 * Construct the data for the CMS
 */
    public function __construct(PDO $pdo, array $args = [])
    {
        $this->pdo = $pdo;

        // Caution: allows private/protected properties to be set
        foreach ($args as $k => $v) {
            if (property_exists($this, $k)) {
                $v = $this->filterwords($v);
                $this->$k = $v;
                $this->params[$k] = $v;
                $this->objects[] = $v;
            }
        }
    } // End of construct method:

    /*
     * Create a short description of content and place a link button that I call 'more' at the end of the
     * shorten content.
     */
    #[Pure] public function intro($content = "", $count = 100): string
    {
        return substr($content, 0, $count) . "...";
    }

    public function setImagePath($image_path) {
        $this->image_path = $image_path;

    }


    public function countAllPage($category = 'home')
    {
        $sql = "SELECT count(id) FROM " . $this->table . " WHERE category=:category";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(['category' => $category ]);
        return $stmt->fetchColumn();

    }

    public function page($perPage, $offset, $page = "index", $category = "home"): array
    {
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE page =:page AND category =:category ORDER BY id DESC, date_added DESC LIMIT :perPage OFFSET :blogOffset';
        $stmt = $this->pdo->prepare($sql); // Prepare the query:
        $stmt->execute(['page' => $page, 'perPage' => $perPage, 'category' => $category, 'blogOffset' => $offset]); // Execute the query with the supplied data:
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    protected function filterwords($text) {
        $filterWords = array('fuck', 'shit', 'ass', 'asshole', 'motherfucker');
        $filterCount = sizeof($filterWords);
        for ($i = 0; $i < $filterCount; $i++) {
            $text = preg_replace_callback('/\b' . $filterWords[$i] . '\b/i', function($matches){return str_repeat('*', strlen($matches[0]));}, $text);
        }
        return $text;
    }


} // End of class: