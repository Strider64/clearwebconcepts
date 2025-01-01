'use strict';
//edit_blog.js
(function () {
    document.addEventListener("DOMContentLoaded", function () {
        const searchForm = document.getElementById("searchForm");
        const editForm = document.getElementById("data_entry_form");
        const select_hidden = document.querySelector('.select_db');
        const current_id = document.getElementById("current_id");
        const category = document.getElementById("category_selector");
        const question = document.querySelector('#question_style');
        const ans1 = document.getElementById("addAnswer1");
        const ans2 = document.getElementById("addAnswer2");
        const ans3 = document.getElementById("addAnswer3");
        const ans4 = document.getElementById("addAnswer4");
        const correct = document.getElementById('addCorrect');

        const resultInput = document.getElementById("searchTerm");

        const headingDropdown = document.querySelector('select[name="id"]');

        async function displayRecord(searchTerm = null, selectedId = null) {
            const requestData = {};
            if(searchTerm !== null) requestData.searchTerm = searchTerm;
            if(selectedId !== null) requestData.id = selectedId;

            try {
                const response = await fetch("search_brainwaveblitz_records.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(requestData),
                });

                const data = await response.json();
                if (data.message) {
                    resultInput.value = '';
                    resultInput.placeholder = data.message;
                } else if (data.error) {
                    console.error(data.error);
                } else {
                    const row = data[0];
                    current_id.value = row.id;
                    select_hidden.value = row.hidden;
                    select_hidden.textContent = `${row.hidden.charAt(0).toUpperCase()}${row.hidden.slice(1)}`;
                    category.value = row.category;
                    category.textContent = `${row.category.charAt(0).toUpperCase()}${row.category.slice(1)}`;
                    question.textContent = row.question;
                    ans1.value = row.ans1;
                    ans2.value = row.ans2;
                    ans3.value = row.ans3;
                    ans4.value = row.ans4;
                    correct.value = row.correct;
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
            const selectedId = document.querySelector('select[name="id"]').value;
            // Use the input value if it's not empty, otherwise use the select value
            const searchTerm = searchTermInput !== "" ? searchTermInput : null;
            const heading = selectedId !== "" ? selectedId : null;

            // Call the displayRecord function with the search term and selected heading
            displayRecord(searchTerm, selectedId);
        });

        async function fetchBrainWaveBlitzData() {
            // replace this URL with the URL to fetch updated data
            const response = await fetch("fetch_brainwaveblitz.php");

            if (response.ok) {
                const data = await response.json();

                // Get the select element
                const selectBox = document.querySelector('select[name="id"]');

                // Clear current options
                // Clear the dropdown
                selectBox.textContent = '';

                // Create a new option element
                let opt = document.createElement('option');
                opt.value = "";
                opt.disabled = true;
                opt.selected = true;
                opt.textContent = 'Select Question';

                // Append the option to the select dropdown
                selectBox.appendChild(opt);


                // Populate the select element with new options
                data.forEach(record => {
                    const option = document.createElement('option');
                    option.value = record.id;
                    option.text = record.question;
                    selectBox.appendChild(option);
                });
            } else {
                console.error("Error fetching data:", response.status, response.statusText);
            }
        }

        fetchBrainWaveBlitzData();

        // New event listener for the dropdown change
        headingDropdown.addEventListener("change", function() {
            const selectedHeading = headingDropdown.options[headingDropdown.selectedIndex].value;
            displayRecord(null, selectedHeading);
        });

        // Add an event listener to the edit form's submit event
        editForm.addEventListener("submit", async function (event) {
            // Prevent the default form submit behavior
            event.preventDefault();

            // Create a FormData object from the edit form
            const formData = new FormData(editForm);
            // Send a POST request to the edit_update_blog.php endpoint with the form data
            const response = await fetch("edit_brainwaveblitz_update_record.php", {
                method: "POST",
                body: formData,
            });

            // Check if the request was successful
            if (response.ok) {
                const result = await response.json();
                // If the response has a "success" property and its value is true, clear the form
                if (result.success) {
                    resultInput.value = '';          // Clear the current value of the search input field
                    resultInput.placeholder = "New Search"; // Set the placeholder to `New Search`
                    question.textContent = '';  // Clear the question field
                    editForm.reset(); // Resetting the edit form
                    searchForm.reset(); // Resetting the search form

                    // Reset select box to default (first) option
                    const selectBox = document.querySelector('select[name="id"]');
                    selectBox.selectedIndex = 0;
                    fetchBrainWaveBlitzData();
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