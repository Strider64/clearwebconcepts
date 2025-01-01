// Import necessary modules
import Game from "./game.js"; // Game logic module
import UI from "./ui.js"; // User interface module
import API from "./api.js"; // API interaction module

// Create instances of the Game, UI, and API classes
const game = new Game();
const ui = new UI();
const api = new API();

// Event listener for category selection changes
ui.categorySelect.addEventListener("change", () => {
    // Get the selected category value
    const selectedCategory = ui.categorySelect.value;

    // Reset the game state
    game.resetGame();

    // Enable answer buttons
    ui.enableButtons();

    // Check if a category is selected
    if (selectedCategory) {
        // Display the main game elements
        ui.displayMainGame();

        // Fetch trivia questions and answers from the API
        api.fetchTriviaQuestionsAnswers(`fetch_questions.php?category=${selectedCategory}`)
            // Start the game with the fetched data
            .then(data => game.startGame(data))
            // Catch and log any errors
            .catch(error => console.error(error));
    } else {
        // Hide the main game elements if no category is selected
        ui.hideMainGame();
    }
});

// Event listener for the next button click
ui.nextButton.addEventListener("click", () => {
    // Proceed to the next question in the game
    game.nextQuestion();

    // Update the UI after proceeding to the next question
    ui.updateUIAfterNextQuestion();
});
