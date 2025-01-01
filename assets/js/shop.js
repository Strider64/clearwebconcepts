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
                .then((response) => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then((data) => {
                    if (data.error) {
                        document.getElementById('error').textContent = data.error;
                    } else {
                        console.log('Redirecting to form-url:', data.form_url);
                        window.location.href = data.form_url; // Redirect to the form URL provided by the gateway
                    }
                })
                .catch((error) => {
                    console.error('Error occurred during payment submission:', error);
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

async function fetchProducts(page, perPage) {
    try {
        console.log('Fetching products for page', page, 'per page', perPage);
        const response = await fetch(`fetch_products.php?page=${page}&per_page=${perPage}&t=${new Date().getTime()}`);

        if (!response.ok) {
            throw new Error('Network response was not ok ' + response.statusText);
        }
        const data = await response.json();
        console.log(`Page ${page} data fetched:`, data);

        const products = data.products;
        const productList = document.getElementById('product-list');
        productList.innerHTML = '';

        products.forEach(product => {
            console.log('Adding product:', product);
            const productElement = document.createElement('div');
            productElement.classList.add('product');

            const img = document.createElement('img');
            img.src = product.larger_image;
            img.alt = `${product.title} Image`;
            img.addEventListener('click', () => showModal(product.larger_image));
            productElement.appendChild(img);

            const productInfo = document.createElement('div');
            productInfo.classList.add('product-info');

            const title = document.createElement('h3');
            title.textContent = product.title;
            productInfo.appendChild(title);

            const description = document.createElement('p');
            description.textContent = product.description;
            productInfo.appendChild(description);

            const price = document.createElement('p');
            price.classList.add('price');
            price.textContent = `$${product.price}`;
            productInfo.appendChild(price);

            const btnContainer = document.createElement('div');
            btnContainer.classList.add('btn-container');

            const btn = document.createElement('a');
            btn.href = "#";
            btn.classList.add('btn');
            btn.textContent = 'Select Print';
            btn.setAttribute('data-id', product.id);
            console.log('Select print data-id', product.id);
            btnContainer.appendChild(btn);

            const policiesLink = document.createElement('a');
            policiesLink.href = "polices.php";
            policiesLink.classList.add('policies-link');
            policiesLink.textContent = 'Policies';
            btnContainer.appendChild(policiesLink);

            productInfo.appendChild(btnContainer);
            productElement.appendChild(productInfo);
            productList.appendChild(productElement);
        });

        createPagination(data.total, data.page, data.per_page);
        setupBuyNowButtons();
        setupImageClick();
        return Promise.resolve();
    } catch (error) {
        console.error('Error fetching products:', error);
        return Promise.reject(error);
    }
}

function setupBuyNowButtons() {
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            console.log('Buy Product button clicked. Product ID:', button.getAttribute('data-id'));

            const productId = button.getAttribute('data-id');
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
                    updateSidebarWithProduct(product);
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
        let response = await fetch(`fetch_product.php?id=${productId}&t=${new Date().getTime()}`);

        if (response.redirected) {
            console.warn('Request was redirected, retrying...');
            response = await fetch(response.url);
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


function updateSidebarWithProduct(product) {
    console.log('Updating sidebar with product:', product);

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
    document.getElementById('product-page').style.display = 'flex';
    document.getElementById('sidebar-content').classList.remove('active');
    document.getElementById('footer').style.display = 'block';
    document.getElementById('paymentForm').reset();
    document.getElementById('error').textContent = '';
}

function createPagination(totalItems, currentPage, itemsPerPage) {
    const paginationContainer = document.getElementById('pagination');
    paginationContainer.innerHTML = '';

    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const maxPagesToShow = 4;
    const halfMaxPages = Math.floor(maxPagesToShow / 2);

    let startPage, endPage;

    if (totalPages <= maxPagesToShow) {
        startPage = 1;
        endPage = totalPages;
    } else {
        if (currentPage <= halfMaxPages) {
            startPage = 1;
            endPage = maxPagesToShow;
        } else if (currentPage + halfMaxPages >= totalPages) {
            startPage = totalPages - maxPagesToShow + 1;
            endPage = totalPages;
        } else {
            startPage = currentPage - halfMaxPages;
            endPage = currentPage + halfMaxPages;
        }
    }

    console.log(`Pagination range: startPage=${startPage}, endPage=${endPage}`);

    if (startPage > 1) {
        addPageButton(paginationContainer, 1, currentPage);
        if (startPage > 2) {
            addEllipsis(paginationContainer, 'before start page');
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        addPageButton(paginationContainer, i, currentPage);
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            addEllipsis(paginationContainer, 'after end page');
        }
        addPageButton(paginationContainer, totalPages, currentPage);
    }
}

function addPageButton(container, page, currentPage) {
    const pageButton = document.createElement('button');
    pageButton.textContent = page;
    if (page === currentPage) {
        pageButton.disabled = true;
    }
    pageButton.addEventListener('click', () => {
        fetchProducts(page, 2).then(() => {
            setupBuyNowButtons();
            setupImageClick();
        });
    });
    container.appendChild(pageButton);
}

function addEllipsis(container, position) {
    const ellipsis = document.createElement('span');
    ellipsis.textContent = '...';
    ellipsis.style.margin = '0 5px';
    ellipsis.style.color = '#333';
    ellipsis.style.display = 'inline-block';
    container.appendChild(ellipsis);
}
