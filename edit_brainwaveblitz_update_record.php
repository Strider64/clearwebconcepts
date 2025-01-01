<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

use clearwebconcepts\ErrorHandler;
use clearwebconcepts\Database;
use clearwebconcepts\LoginRepository as Login;

$errorHandler = new ErrorHandler();

$errorHandler = new ErrorHandler();

// Register the exception handler method
set_exception_handler([$errorHandler, 'handleException']);

$database = new Database();
$pdo = $database->createPDO();
$login = new Login($pdo);

// Check if the required form data is provided
if (!array_key_exists('id', $_POST) || !array_key_exists('hidden', $_POST) || !array_key_exists('category', $_POST) || !array_key_exists('question', $_POST) || !array_key_exists('ans1', $_POST) || !array_key_exists('ans2', $_POST) || !array_key_exists('ans3', $_POST) || !array_key_exists('ans4', $_POST) || !array_key_exists('correct', $_POST)) {
    throw new Exception("Missing required form data.");
}


$id = empty($_POST['id']) ? null : $_POST['id'];
$hidden = empty($_POST['hidden']) ? null : $_POST['hidden'];
$category = empty($_POST['category']) ? null : $_POST['category'];
$question = empty($_POST['question']) ? null : $_POST['question'];
$answer1 = empty($_POST['ans1']) ? null : $_POST['ans1'];
$answer2 = empty($_POST['ans2']) ? null : $_POST['ans2'];
$answer3 = empty($_POST['ans3']) ? null : $_POST['ans3'];
$answer4 = empty($_POST['ans4']) ? null : $_POST['ans4'];
$correct = empty($_POST['correct']) ? null : (int) $_POST['correct'];


$sql = "UPDATE brainwaveblitz SET hidden = :hidden, 
                                  category = :category, 
                                  question = :question, 
                                  ans1 = :ans1, 
                                  ans2 = :ans2, 
                                  ans3 = :ans3, 
                                  ans4 = :ans4, 
                                  correct = :correct 
                            WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->bindParam(':hidden', $hidden);
$stmt->bindParam(':category', $category);
$stmt->bindParam(':question', $question);
$stmt->bindParam(':ans1', $answer1);
$stmt->bindParam(':ans2', $answer2);
$stmt->bindParam(':ans3', $answer3);
$stmt->bindParam(':ans4', $answer4);
$stmt->bindParam(':correct', $correct);

// Execute the prepared statement
$stmt->execute();

// Check if the update was successful
if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Record updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No record updated.']);
}



