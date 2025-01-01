// Define the UI class to handle user interface updates and interactions
class UI {
    // Constructor method to initialize the UI class
    constructor() {
        // Select the category select element from the DOM
        this.categorySelect = document.querySelector("#category");

        // Select the main game container from the DOM
        this.mainGame = document.querySelector("#mainGame");

        // Select the brainwave header element from the DOM
        this.brainwaveheader = document.querySelector(".image-header");

        // Select the next button element from the DOM
        this.nextButton = document.querySelector("#next");

        // Select all answer button elements from the DOM
        this.answerButtons = document.querySelectorAll(".buttonStyle");
    }

    /**
     * Enable Answer Buttons
     *
     * Enables all answer buttons to allow user input.
     */
    enableButtons() {
        // Iterate over each answer button and set its disabled property to false
        this.answerButtons.forEach(button => {
            button.disabled = false;
        });
    }

    /**
     * Disable Answer Buttons
     *
     * Disables all answer buttons to prevent user input.
     */
    disableButtons() {
        // Iterate over each answer button and set its disabled property to true
        this.answerButtons.forEach(button => {
            button.disabled = true;
        });
    }

    /**
     * Show Next Button
     *
     * Displays the next button to allow the user to proceed to the next question.
     */
    showNextButton() {
        // Set the display property of the next button to "block" to make it visible
        this.nextButton.style.display = "block";
    }

    /**
     * Hide Next Button
     *
     * Hides the next button to prevent the user from proceeding to the next question.
     */
    hideNextButton() {
        // Set the display property of the next button to "none" to make it invisible
        this.nextButton.style.display = "none";
    }

    /**
     * Display Main Game
     *
     * Displays the main game container and hides the brainwave header.
     */
    displayMainGame() {
        // Set the display property of the main game container to "block" to make it visible
        this.mainGame.style.display = "block";

        // Set the display property of the brainwave header to "none" to make it invisible
        this.brainwaveheader.style.display = "none";
    }

    /**
     * Hide Main Game
     *
     * Hides the main game container.
     */
    hideMainGame() {
        // Set the display property of the main game container to "none" to make it invisible
        this.mainGame.style.display = "none";
    }

    /**
     * Update UI After Next Question
     *
     * Hides the next button and enables the answer buttons after the user proceeds to the next question.
     */
    updateUIAfterNextQuestion() {
        // Hide the next button
        this.hideNextButton();

        // Enable the answer buttons
        this.enableButtons();
    }
}

// Export the UI class as the default export
export default UI;
