'use strict';
//edit_blog.js
(function () {
    function resizeTextarea(textarea) {
        textarea.style.height = 'auto'; // Reset the height
        textarea.style.height = textarea.scrollHeight + 'px'; // Set the height to scroll height
    }


    document.addEventListener("DOMContentLoaded", function () {
        const searchForm = document.getElementById("searchForm");
        const editForm = document.getElementById("data_entry_form");
        const idInput = document.getElementById("id");
        const image_for_edit_record = document.getElementById("image_for_edited_record");
        const category = document.getElementById("category");
        const heading = document.querySelector('.heading');
        const content = document.getElementById("content");
        const resultInput = document.getElementById("searchTerm");

        const headingDropdown = document.querySelector('select[name="heading"]');

        async function displayRecord(searchTerm = null, selectedHeading = null) {
            const requestData = {};
            if(searchTerm !== null) requestData.searchTerm = searchTerm;
            if(selectedHeading !== null) requestData.heading = selectedHeading;

            try {
                const response = await fetch("search_cms_records.php", {
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
                    console.log(row);
                    idInput.value = row.id;
                    image_for_edit_record.src = row.thumb_path;
                    image_for_edit_record.alt = row.heading;
                    category.value = row.category;
                    category.textContent = `${row.category.charAt(0).toUpperCase()}${row.category.slice(1)}`;
                    heading.value = row.heading;
                    content.value = row.content;
                    resizeTextarea(content);
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
            const selectedHeading = document.querySelector('select[name="heading"]').value;

            // Use the input value if it's not empty, otherwise use the select value
            const searchTerm = searchTermInput !== "" ? searchTermInput : null;
            const heading = selectedHeading !== "" ? selectedHeading : null;

            // Call the displayRecord function with the search term and selected heading
            displayRecord(searchTerm, heading);
        });


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
            //console.log("form data", formData);
            // Send a POST request to the edit_update_blog.php endpoint with the form data
            const response = await fetch("edit_update_cms.php", {
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
                    editForm.reset(); // Resetting the edit form
                    searchForm.reset(); // Resetting the search form

                    // Reset select box to default (first) option
                    const selectBox = document.querySelector('select[name="heading"]');
                    selectBox.selectedIndex = 0;
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