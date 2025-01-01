<?php
// Links.php
namespace clearwebconcepts;

use JetBrains\PhpStorm\Pure;

class Links
{
    private $current_page;
    private $per_page;
    private $total_count;
    private $total_pages;
    private $category;

    public function __construct($current_page, $per_page, $total_count, $category)
    {
        $this->current_page = $current_page;
        $this->per_page = $per_page;
        $this->total_count = $total_count;
        $this->category = $category;
        $this->total_pages = ceil($this->total_count / $this->per_page);
    }

    public function display_links(): string
    {
        $output = '';

        if ($this->total_pages > 1) {
            $output .= "<ul class='pagination'>";

            if ($this->current_page > 1) {
                $output .= "<li><a href='?category=" . urlencode($this->category) . "&page=1'>&#10094;</a></li>";
                $output .= "<li><a href='?category=" . urlencode($this->category) . "&page=" . ($this->current_page - 1) . "'>&laquo;</a></li>";
            }

            $visible_pages = 2;
            $window_start = max(1, $this->current_page - floor($visible_pages / 2));
            $window_end = min($this->total_pages, $window_start + $visible_pages - 1);

            if ($window_start > 1) {
                $output .= "<li><span>...</span></li>";
            }

            for ($i = $window_start; $i <= $window_end; $i++) {
                if ($i == $this->current_page) {
                    $output .= "<li class='active'><a href='?category=" . urlencode($this->category) . "&page=$i'>{$i}</a></li>";
                } else {
                    $output .= "<li><a href='?category=" . urlencode($this->category) . "&page={$i}'>{$i}</a></li>";
                }
            }

            if ($window_end < $this->total_pages) {
                $output .= "<li><span>...</span></li>";
            }

            if ($this->current_page < $this->total_pages) {
                $output .= "<li><a href='?category=" . urlencode($this->category) . "&page=" . ($this->current_page + 1) . "'>&raquo;</a></li>";
                $output .= "<li><a href='?category=" . urlencode($this->category) . "&page=" . $this->total_pages . "'>&#10095;</a></li>";
            }

            $output .= "</ul>";
        }

        return $output;
    }
}
