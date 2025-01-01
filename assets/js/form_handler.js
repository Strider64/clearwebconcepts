class FormHandler {
    constructor(formId, url) {
        this.form = document.getElementById(formId);
        this.fetchRequest = new FetchRequest(url);
        this.addFormSubmitEvent();
    }

    addFormSubmitEvent() {
        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            this.submitForm();
        });
    }

    gatherFormData() {
        return {
            "@context": "http://schema.org",
            "@type": "ProfessionalService",
            "additionalType": "http://www.productontology.org/id/Web_developer",
            "name": document.getElementById('name').value,
            "address": {
                "@type": "PostalAddress",
                "addressLocality": document.getElementById('locality').value,
                "addressRegion": document.getElementById('region').value,
                "postalCode": document.getElementById('postalCode').value,
                "addressCountry": document.getElementById('country').value
            },
            "telephone": this.formatPhoneNumber(document.getElementById('telephone').value),
            "email": document.getElementById('email').value,
            'url': document.getElementById('url').value,
            "openingHours": document.getElementById('openingHours').value,
            "description": document.getElementById('description').value,
            "founder": {
                "@type": "Person",
                "name": document.getElementById('founderName').value
            },
            "sameAs": this.getSameAsArray(document.getElementById('sameAs').value)
        };
    }

    formatPhoneNumber(phoneNumber) {
        // Remove non-numeric characters
        var cleaned = ('' + phoneNumber).replace(/\D/g, '');

        // Assuming a US phone number format without an explicit country code
        // Add '1' for US country code if the length is 10 (standard US number length)
        if (cleaned.length === 10) {
            cleaned = '1' + cleaned;
        }

        // Check if the number is long enough to be valid
        if (cleaned.length !== 11) {
            return phoneNumber;
        }

        // Country code (1), area code, first three digits, last four digits
        var match = cleaned.match(/^(\d{1})(\d{3})(\d{3})(\d{4})$/);

        if (match) {
            return '+' + match[1] + '-' + match[2] + '-' + match[3] + '-' + match[4];
        }

        return phoneNumber; // Return original number if format is not matched
    }


    getSameAsArray(sameAsString) {
        // Split the string into an array by new lines or commas
        // Trim whitespace and filter out any empty strings
        return sameAsString.split(/[\n,]+/).map(s => s.trim()).filter(Boolean);
    }

    submitForm() {
        const formData = this.gatherFormData();
        this.fetchRequest.createRequest('POST', formData,
            (data) => {
                console.log('Success:', data);

                // Clear the form
                this.form.reset();

                // Display a success message
                this.showSuccessMessage("Data saved successfully.");
            },
            (error) => {
                console.error('Error:', error);
                // Optionally, handle the error case, e.g., show an error message
            }
        );
    }

    showSuccessMessage(message) {
        // Find the sidebar element
        const sidebar = document.querySelector('.displaySuccess');

        // Create a message element
        const messageElement = document.createElement('div');
        messageElement.textContent = message;
        messageElement.classList.add('success-message'); // Add a class for styling

        // Append the message to the sidebar
        sidebar.appendChild(messageElement);

        // Optional: Remove the message after some time
        setTimeout(() => {
            messageElement.remove();
        }, 5000); // 5 seconds delay
    }


}