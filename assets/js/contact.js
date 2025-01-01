'use strict';

const sendUrl = 'send_email.php';
const submit = document.querySelector('#submitForm');
const radioBtn = document.querySelector('#message-type');
const buttons = document.getElementsByName("reason");
const message = document.querySelector('#message');
const messageSuccess = document.querySelector('#messageSuccess');

let name = document.querySelector('#name');
let email = document.querySelector('#email');
let phone = document.querySelector('#phone');
let website = document.querySelector('#web');
let notice = document.querySelector('.notice');
let sendEmail = {};
let sendStatus = {
    name: false,
    email: false,
    comments: false
};
sendEmail.reason = 'message';
//sendEmail.token = document.querySelector('#token').value;

let comments = document.querySelector("textarea");
let output = document.querySelector("#length");


name.addEventListener('input', () => {
    const value = name.value.trim();

    if (value) {
        name.style.borderColor = 'green';
        sendEmail.name = name.value;
        sendStatus.name = true;
    } else {
        name.style.borderColor = "red";
        name.value = "";
        name.placeholder = "Name Required";
        name.focus();

    }

});

const emailIsValid = (email) => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
};

email.addEventListener('change', () => {
    let status = emailIsValid(email.value);
    //console.log('Email Address', email.value, 'Status', status);
    if (!status) {
        email.value = "";
        email.placeholder = "Email Address is Invalid!";
        email.style.borderColor = "red";
        email.focus();
    } else {
        email.style.borderColor = 'green';
        sendEmail.email = email.value;
        sendStatus.email = true;
    }
});


/*
 * Selection Element
 */
buttons.forEach((value, index) => {
    //console.log(value, index);
    buttons[index].addEventListener('change', (e) => {
        sendEmail.reason = e.target.value;
        //console.log('Reason:', sendEmail.reason);
    });
});


comments.addEventListener("input", () => {
    // noinspection JSValidateTypes
    output.textContent = comments.value.length;
    const value = comments.value.trim();

    if (value) {
        comments.style.borderColor = 'green';
        sendEmail.comments = comments.value;
        sendStatus.comments = true;
    } else {
        comments.style.borderColor = "red";
        comments.placeholder = "Message Required!";
        comments.focus();
    }
});

function handleSaveErrors(response) {
    if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
    }

    return response.text().then((text) => {
        console.log("Raw response text:", text);
        return JSON.parse(text);
    });
}



/* Success function utilizing FETCH */
const sendUISuccess = function (result) {
    //console.log('Result', result);
    if (result) {
        //d.querySelector('#recaptcha').style.display = "none";
        submit.style.display = "none";

        // Change graphic again when successful send
        document.querySelector('.pen').setAttribute('src', 'assets/images/img-end-002.webp');
        // Show the success message
        //document.getElementById('successMessage').style.display = "block";
        // Disable form elements
        document.querySelectorAll('form > *').forEach(function (a) {
            a.disabled = true;
        });
    }
};


/* If Database Table fails to update data in mysql table */
const sendUIError = function (error) {
    console.log("Database Table did not load", error);

    // Change graphic to indicate an error
    document.querySelector('.pen').setAttribute('src', 'assets/images/error.png');
};


const saveRequest = (sendUrl, succeed, fail) => {
    //console.log('sendEmail', sendEmail);
    fetch(sendUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(sendEmail)
    })
        .then((response) => {
            //console.log('Response:', response); // Add this line to log the response object
            return handleSaveErrors(response);
        })

        .then((data) => {
            //console.log('Data:', data); // Add this line to log the data object
            if (data.status === 'success') {
                succeed(data);
            } else {
                fail(data.message);
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            fail(error);
        });
};


submit.addEventListener('click', (e) => {

    e.preventDefault();

    sendEmail.phone = phone.value;
    sendEmail.website = website.value;
    sendEmail.response = submit.getAttribute('data-response');
    if (email.value === '') {
        email.placeholder = "Email Address is Invalid!";
        email.style.borderColor = "red";
        email.focus();
    }
    if (sendStatus.name && sendStatus.email && sendStatus.comments) {
        submit.style.display = "none";
        notice.style.display = "grid";

        // Change graphic to indicate sending in progress
        document.querySelector('.pen').setAttribute('src', 'assets/images/img-middle-002.webp');

        message.style.display = "flex";
        saveRequest(sendUrl, sendUISuccess, sendUIError);
    } else {
        notice.style.display = "block";
        notice.textContent = "Name, Email, and Message Required!";
    }
}, false);


