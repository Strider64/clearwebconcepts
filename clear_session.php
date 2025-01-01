<?php
session_start();
$_SESSION['shown_images'] = [];
echo json_encode(['status' => 'success']);
