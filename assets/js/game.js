// Import the API class from the api.js module
import API from './api.js';

/**
 * Game Class
 *
 * Handles game logic and interactions.
 */
class Game {
    /**
     * Constructor
     *
     * Initializes the Game class by setting initial game state and creating an instance of the API class.
     */
    constructor() {
        // Initialize game state variables
        this.index = 0; // Current question index
        this.triviaData = []; // Array of trivia questions and answers
        this.score = 0; // Player's current score
        this.choice = 0; // Player's current answer choice

        // Create an instance of the API class for making API requests
        this.api = new API();
    }

    /**
     * Start Game
     *
     * Initializes the game with the provided trivia data and displays the first question.
     *
     * @param {array} data - The trivia data to use for the game.
     */
    startGame(data) {
        // Set the trivia data for the game
        this.triviaData = data;
        this.clearResultMessage();
        // Reset game state variables
        this.index = 0;
        this.score = 0;
        this.choice = 0;

        // Display the first question
        this.displayQuestion();

        // Hide the next button initially
        document.querySelector("#next").style.display = "none";
    }

    /**
     * Display Question
     *
     * Displays the current question and its possible answers.
     */
    displayQuestion() {
        // Get the current question from the trivia data
        const currentQuestion = this.triviaData[this.index];

        // Set the current question's ID as a data attribute on the #currentQuestion element
        document.querySelector("#currentQuestion").setAttribute("data-record", currentQuestion.id);

        // Display the current question number
        document.querySelector("#currentQuestion").textContent = (this.index + 1).toString();

        // Display the current question text
        document.querySelector("#question").textContent = currentQuestion.question;

        // Display the possible answers for the current question
        this.displayAnswers(currentQuestion);
    }

    /**
     * Display Answers
     *
     * Displays the possible answers for the given question.
     *
     * @param {object} question - The question object containing the possible answers.
     */
    displayAnswers(question) {
        // Get all answer button elements
        const answerButtons = document.querySelectorAll(".buttonStyle");

        // Iterate over each answer button
        answerButtons.forEach((button, index) => {
            // Remove any existing event listener for the button
            const previousPickAnswer = button.__pickAnswer__;
            if (previousPickAnswer) {
                button.removeEventListener("click", previousPickAnswer);
            }

            // Create a new event listener for the button
            const newPickAnswer = this.pickAnswer(index + 1);
            button.addEventListener("click", newPickAnswer, false);
            button.__pickAnswer__ = newPickAnswer;

            // Get the answer text for the current button
            let answerText = [question.ans1, question.ans2, question.ans3, question.ans4][index];

            // Display the answer text on the button
            if (answerText) {
                button.textContent = ` ${answerText}`;
                button.style.display = "block"; // Show the button
                button.style.pointerEvents = "auto"; // Enable the button
            } else {
                button.textContent = "";
                button.style.display = "none"; // Hide the button
                button.style.pointerEvents = "none"; // Disable the button
            }
        });
    }

    /**
     * Check Answer Against Table
     *
     * Checks the player's answer against the correct answer and updates the game state accordingly.
     *
     * @param {object} data - The correct answer data from the API.
     */
    checkAnswerAgainstTable(data) {
        // Get the correct answer from the data
        const correctAnswer = data.correct;

        // Increment the question index
        this.index++;
        setTimeout(() => {
            document.querySelector("#result").classList.remove("hidden");
        }, 100); // Delay the removal of the hidden class by 100ms
        // Check if the player's answer is correct
        if (correctAnswer === this.choice) {
            // Display a success message

            document.querySelector("#result").textContent = "The answer was indeed number " + correctAnswer + "!";
            document.querySelector("#result").classList.add("success");
            document.querySelector("#result").classList.remove("error");

            // Increment the player's score
            this.score++;
            document.querySelector("#score").textContent = `${this.score}`;
        } else {
            // Display an error message
            document.querySelector("#result").textContent = "Incorrect. The correct answer was: " + correctAnswer;
            document.querySelector("#result").classList.add("error");
            document.querySelector("#result").classList.remove("success");
        }
    }

    /**
     * Pick Answer
     *
     * Creates an event listener for the given answer index.
     *
     * @param {number} answerIndex - The index of the answer to create an event listener for.
     * @returns {function} The event listener function.
     */
    pickAnswer(answerIndex) {
        return () => {
            // Set the player's answer choice
            this.choice = answerIndex;

            // Check the answer
            this.checkAnswer();

            // Show the next button if there are more questions
            if (this.index < this.triviaData.length - 1) {
                document.querySelector("#next").style.display = "block"; // Show the next button
                document.querySelector("#next").addEventListener("click", () => {
                    console.log('Index before nextQuestion:', this.index);
                    this.nextQuestion();
                    console.log('Index after nextQuestion:', this.index);
                    document.querySelector("#next").style.display = "none"; // Hide the next button
                }, { once: true }); // Ensure the event listener is only triggered once per click
            }
        };
    }

    /**
     * Check Answer
     *
     * Checks the player's answer by making an API request to the server.
     */
    checkAnswer() {
        // Get all answer button elements
        const answerButtons = document.querySelectorAll(".buttonStyle");

        // Disable the answer buttons
        this.disableButtons(answerButtons);

        // Get the current question's ID
        const id = document.querySelector("#currentQuestion").getAttribute("data-record");

        // Make an API request to check the answer
        this.api.fetchCorrectAnswer(id)
            .then(data => this.checkAnswerAgainstTable(data))
            .catch(error => console.error(error));
    }

    /**
     * Disable Buttons
     *
     * Disables the given buttons.
     *
     * @param {array} buttons - The buttons to disable.
     */
    disableButtons(buttons) {
        // Iterate over each button and set its disabled property to true
        buttons.forEach(button => {
            button.disabled = true;
        });
    }

    /**
     * Enable Buttons
     *
     * Enables the given buttons.
     *
     * @param {array} buttons - The buttons to enable.
     */
    enableButtons(buttons) {
        // Iterate over each button and set its disabled property to false
        buttons.forEach(button => {
            button.disabled = false;
        });
    }

    /**
     * Reset Game
     *
     * Resets the game state to its initial values.
     */
    resetGame() {
        // Reset game state variables
        this.choice = 0;
        this.score = 0;
        document.querySelector("#result").textContent = "";
        document.querySelector("#score").textContent = `${this.score}`;
    }

    /**
     * Next Question
     *
     * Proceeds to the next question in the game.
     */
    nextQuestion() {
        // Clear the result message
        this.clearResultMessage();

        // Check if there are more questions
        if (this.index < this.triviaData.length) {
            // Display the next question
            this.displayQuestion();
        } else {
            // Log a message indicating the game is over
            console.log("Game over");
        }
    }

    /**
     * Clear Result Message
     *
     * Clears the result message displayed on the screen.
     */
    clearResultMessage() {
        // Clear the result message
        document.querySelector("#result").classList.add("hidden");
    }
}

// Export the Game class as the default export
export default Game;
