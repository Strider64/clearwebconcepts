document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed');

    const perPage = 2;
    fetchProducts(1, perPage).then(() => {
        console.log('Products loaded.');
        setupBuyNowButtons(); // Initialize the correct "Buy Now" functionality
        setupImageClick();
    });

    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function (event) {
            event.preventDefault();
            console.log('Payment form submitted');

            const billingData = {
                first_name: document.getElementById('billing-first-name').value,
                last_name: document.getElementById('billing-last-name').value,
                address: document.getElementById('billing-address').value,
                city: document.getElementById('billing-city').value,
                state: document.getElementById('billing-state').value,
                zip: document.getElementById('billing-zip').value,
                country: document.getElementById('billing-country').value,
                email: document.getElementById('billing-email').value,
            };

            console.log('Billing Data:', billingData);

            fetch('payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'step_one',
                    billingData: billingData,
                }),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.error) {
                        document.getElementById('error').textContent = data.error;
                    } else {
                        console.log('Redirecting to form-url:', data.form_url);
                        window.location.href = data.form_url;
                    }
                })
                .catch((error) => {
                    document.getElementById('error').textContent = 'An error occurred: ' + error.message;
                });
        });
    } else {
        console.error('Payment form not found');
    }

    document.getElementById('cancel-btn').addEventListener('click', () => {
        resetPage();
    });

    document.getElementById('sidebar-image').addEventListener('click', () => {
        resetPage();
    });
});

function setupBuyNowButtons() {
    // Remove existing buttons to clear any old event listeners
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        const newButton = button.cloneNode(true);
        button.parentNode.replaceChild(newButton, button);
    });

    // Set up new event listeners for the cloned buttons
    const newButtons = document.querySelectorAll('.btn');
    console.log('Setting up Buy Now buttons. Number of buttons:', newButtons.length);

    newButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            console.log('Buy Product button clicked. Product ID:', button.getAttribute('data-id'));

            const productId = button.getAttribute('data-id');
            console.log('product id', productId);
            if (!productId) {
                console.error('Product ID not found');
                return;
            }

            fetchProductById(productId)
                .then(product => {
                    if (!product) {
                        console.error('Product not found');
                        return;
                    }

                    // Update the sidebar with product information
                    const sidebarContent = document.getElementById('sidebar-content');
                    const paymentForm = document.getElementById('payment-form');
                    const sidebarImage = document.getElementById('sidebar-image');
                    const amountField = document.getElementById('amount');

                    sidebarImage.src = product.larger_image;

                    if (amountField) {
                        amountField.value = product.price;
                        console.log('Price set in the form:', product.price);
                    } else {
                        console.error('Amount field not found');
                    }

                    sidebarContent.classList.add('active');
                    paymentForm.style.display = 'block';
                    document.getElementById('product-page').style.display = 'none';
                    document.getElementById('footer').style.display = 'none';
                    sidebarContent.scrollIntoView({ behavior: 'smooth' });

                    console.log('Sidebar content updated with specific product info');
                })
                .catch(error => {
                    console.error('Error fetching product details:', error);
                });
        });
    });
}

async function fetchProductById(productId) {
    try {
        console.log('Fetching product with ID:', productId);
        let response = await fetch(`fetch_product.php?id=${productId}`);

        // Check if response is redirected (HTTP status 301 or 302)
        if (response.redirected) {
            console.warn('Request was redirected, following redirect...');
            window.location.href = response.url; // Follow the redirect URL
            return null; // Stop further execution
        }

        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }

        const product = await response.json();

        if (product.error) {
            throw new Error(product.error);
        }

        console.log('Product fetched:', product);
        return product;
    } catch (error) {
        console.error('Error fetching product by ID:', error);
        return null;
    }
}

function setupImageClick() {
    const images = document.querySelectorAll('.product img');
    images.forEach(image => {
        image.addEventListener('click', () => {
            showModal(image.src);
        });
    });
}

function showModal(imageSrc) {
    const modal = document.getElementById('myModal');
    const modalImg = document.getElementById('modalImage');
    modal.style.display = "block";
    modalImg.src = imageSrc;

    const span = modal.querySelector('.close');
    span.onclick = function() {
        modal.style.display = "none";
    }

    modal.onclick = function(event) {
        modal.style.display = "none";
    }
}

function resetPage() {
    document.getElementById('product-page').style.display = 'flex'; // Reset the product page display to flex
    document.getElementById('sidebar-content').classList.remove('active');
    document.getElementById('footer').style.display = 'block';
    document.getElementById('paymentForm').reset();
    document.getElementById('error').textContent = '';
}
