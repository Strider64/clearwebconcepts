<?php

namespace clearwebconcepts;

interface ImageContentManagerInterface
{
    public function intro($content = "", $count = 100): string;
    public function setImagePath($image_path);
    public function totalCount();
    public function offset($per_page, $current_page): float|int;
    public function countAllPage($category = 'wildlife');
    public function page($perPage, $offset, $page = "index", $category = "wildlife"): array;
    public function create(): bool;
    public function fetch_by_heading();
    public function fetch_by_id();
    public function delete(): bool;
}