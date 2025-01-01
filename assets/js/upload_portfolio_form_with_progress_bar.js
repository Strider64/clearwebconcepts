document.getElementById('data_entry_form').addEventListener('submit', function (event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    // Get references to the progress bar elements
    const progressBarContainer = document.getElementById('progress_bar_container');
    const progressBarTitle = document.getElementById('progress_bar_title');
    const progressBar = document.getElementById('progress_bar');

    // Show the progress bar container, title, and progress bar
    progressBarContainer.style.display = 'flex';
    progressBarTitle.style.display = 'block';
    progressBar.style.display = 'block';

    // Fetch doesn't expose the `progress` event directly. To handle it, you need to create a custom request function.
    function fetchWithProgress(url, options) {
        const controller = new AbortController();
        const signal = controller.signal;
        const xhr = new XMLHttpRequest();
        // Add the signal to the options object
        options.signal = signal;
        return new Promise((resolve, reject) => {
            xhr.open(options.method || 'get', url, true);

            xhr.upload.addEventListener('progress', options.onProgress || (() => {
            }));

            xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(xhr.responseText);
                } else {
                    reject(new Error(xhr.statusText));
                }
            });

            xhr.addEventListener('error', () => reject(new Error(xhr.statusText)));
            xhr.addEventListener('abort', () => reject(new Error('Request aborted')));

            xhr.send(options.body);
        });
    }

    // Submit the form data using Fetch API
    fetchWithProgress('http://localhost:8888/clearwebconcepts/new_portfolio.php', {
        method: 'POST',
        body: formData,
        onProgress: function (event) {
            if (event.lengthComputable) {
                const percentage = (event.loaded / event.total) * 100;
                progressBar.style.width = percentage + '%';
            }
        },
    })
        .then(responseText => {
            console.log('Upload complete:', responseText);

            // Hide the progress bar container, title, and progress bar
            progressBarContainer.style.display = 'none';
            progressBarTitle.style.display = 'none';
            progressBar.style.display = 'none';

            // Reset the progress bar
            progressBar.style.width = '0%';

            // Handle the server response here (e.g., display a success message or redirect the user)
            // Redirect to a new page after 5 seconds

            window.location.href = "http://localhost:8888/clearwebconcepts/";


        })
        .catch(error => {
            console.error('An error occurred during the upload:', error.message);

            // Handle the error here (e.g., display an error message)
        });
});