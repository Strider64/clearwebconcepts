// Define the API class to handle API requests
class API {
    // Constructor method to initialize the API class
    constructor() {}

    /**
     * Fetch trivia questions and answers from the specified URL
     *
     * @param {string} url - The URL to send the GET request to
     * @returns {Promise} A promise that resolves with the fetched data in JSON format
     */
    async fetchTriviaQuestionsAnswers(url) {
        try {
            // Send a GET request to the specified URL
            const response = await fetch(url);

            // Check if the response is OK (200-299)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Parse the response data as JSON
            const data = await response.json();

            // Return the parsed data
            return data;
        } catch (error) {
            // Log any errors that occur during the request
            console.error(error);
        }
    }

    /**
     * Fetch the correct answer for a given question ID
     *
     * @param {number} id - The ID of the question to fetch the correct answer for
     * @returns {Promise} A promise that resolves with the fetched data in JSON format
     */
    async fetchCorrectAnswer(id) {
        try {
            // Send a POST request to the server with the question ID
            const response = await fetch('fetch_correct_answer.php', {
                method: 'POST',
                body: JSON.stringify({ id: id })
            });

            // Check if the response is OK (200-299)
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Parse the response data as JSON
            const data = await response.json();

            // Return the parsed data
            return data;
        } catch (error) {
            // Log any errors that occur during the request
            console.error(error);
        }
    }
}

// Export the API class as the default export
export default API;
