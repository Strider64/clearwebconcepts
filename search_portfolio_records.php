<?php

// Send a response to the client with the content type set to application/json.
header('Content-Type: application/json');

// Include the configuration file and autoload file from the composer.
require_once __DIR__ . '/../config/clearwebconfig.php';
require_once "vendor/autoload.php";

// Import the ErrorHandler and Database classes from the PhotoTech namespace.
use clearwebconcepts\{
    ErrorHandler,
    Database,
    Links,
    ImageContentManager as Portfolio,
    LoginRepository as Login
};

// Instantiate an ErrorHandler object.
$errorHandler = new ErrorHandler();

// Set a custom exception handler function.
set_exception_handler([$errorHandler, 'handleException']);

// Create a new Database object and establish a PDO connection.
$database = new Database();
$pdo = $database->createPDO();

// Instantiate a Login object.
$login = new Login($pdo);

// Main try-catch block.
try {
    // Get the request body and decode it as JSON.
    $request = json_decode(file_get_contents('php://input'), true);

    // Extract the search term and heading from the request, if they exist.
    $searchTerm = $request['searchTerm'] ?? null;
    $heading = $request['heading'] ?? null;

    // If a search term was provided, use a full-text search on the 'content' field.
    // Before this can work, you'll need to make sure your content column is indexed for full-text searching.
    // You can do this with the following SQL command:
    // Example:
    // ALTER TABLE portfolio ADD FULLTEXT(content);
    if($searchTerm !== null) {
        $sql = "SELECT * FROM portfolio WHERE MATCH(content) AGAINST(:searchTerm IN NATURAL LANGUAGE MODE) LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':searchTerm', $searchTerm);

        // If a heading was provided, search for exact matches on the 'heading' field.
    } else if($heading !== null) {
        $sql = "SELECT * FROM portfolio WHERE heading = :heading LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':heading', $heading);

        // If neither a search term nor a heading was provided, throw an exception.
    } else {
        throw new Exception("No valid search term or heading provided");
    }

    // Execute the prepared statement.
    $stmt->execute();

    // Fetch the results and handle them as needed.
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // If results were found, return them to the client as JSON.
    if ($results) {
        echo json_encode($results);
    } else {
        echo json_encode(['message' => 'No results found.']);
    }

// Catch any exceptions that occur during database interaction.
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
