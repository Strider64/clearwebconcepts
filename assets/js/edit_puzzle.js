'use strict';
//edit_puzzle.js
(function () {
    document.addEventListener("DOMContentLoaded", function () {
        const searchForm = document.getElementById("searchForm");
        const editForm = document.getElementById("data_entry_form");
        const id = document.getElementById("id");
        const image_for_edit_record = document.getElementById("image_for_edited_record");
        const category = document.getElementById("category");
        const difficulty_level = document.querySelector('#difficulty_level');
        const description = document.getElementById('description');
        const resultInput = document.getElementById("searchTerm");
        const title = document.getElementById('title');


        async function displayRecord(searchTerm = null) {
            const requestData = {};
            if(searchTerm !== null) requestData.searchTerm = searchTerm;

            try {
                const response = await fetch("search_puzzle_records.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(requestData),
                });

                const data = await response.json();
                console.log(data); // Add this line
                if (data.message) {
                    resultInput.value = '';
                    resultInput.placeholder = data.message;
                } else if (data.error) {
                    console.error(data.error);
                } else {
                    const row = data[0];
                    console.log('row', row);
                    id.value = row.id;
                    image_for_edit_record.src = row.image_path;
                    image_for_edit_record.alt = "Puzzle Image";
                    // Set the value for the category
                    for (let option of category.options) {
                        if (option.value === row.category) {
                            option.selected = true;
                            break;
                        }
                    }

                    // Set the value for the difficulty level
                    for (let option of difficulty_level.options) {
                        if (option.value === row.difficulty_level) {
                            option.selected = true;
                            break;
                        }
                    }
                    title.value = row.title;
                    description.textContent = row.description;
                }
            } catch (error) {
                console.error("Error:", error);
            }
        }

        searchForm.addEventListener("submit", function (event) {
            // Prevent the default form submit behavior
            event.preventDefault();

            // Get the value of the search term input field and the select box
            const searchTermInput = document.getElementById("searchTerm").value;


            // Use the input value if it's not empty, otherwise use the select value
            const searchTerm = searchTermInput !== "" ? searchTermInput : null;


            // Call the displayRecord function with the search term and selected heading
            displayRecord(searchTerm);
        });


        // Add an event listener to the edit form's submit event
        editForm.addEventListener("submit", async function (event) {
            // Prevent the default form submit behavior
            event.preventDefault();
            event.stopImmediatePropagation();
            // Create a FormData object from the edit form
            const formData = new FormData(editForm);
            console.log("form data", formData);
            // Send a POST request to the edit_update_blog.php endpoint with the form data
            const response = await fetch("update_puzzle_records.php", {
                method: "POST",
                body: formData,
            });

            // Check if the request was successful
            if (response.ok) {
                const result = await response.json();
                console.log(result);
                // If the response has a "success" property and its value is true, clear the form
                if (result.success) {
                    resultInput.value = '';          // Clear the current value of the search input field
                    resultInput.placeholder = "New Search"; // Set the placeholder to `New Search`
                    image_for_edit_record.src = "";
                    image_for_edit_record.alt = "";
                    document.getElementById("description").textContent = "";
                    document.getElementById("difficulty_level").selectedIndex = 0; // set to the first option
                    document.getElementById("category").selectedIndex = 0; // set to the first option

                    editForm.reset(); // Resetting the edit form
                    searchForm.reset(); // Resetting the search form
                }

            } else {
                console.error(
                    "Error submitting the form:",
                    response.status,
                    response.statusText
                );
                // Handle error response
            }
        });


    });
})();