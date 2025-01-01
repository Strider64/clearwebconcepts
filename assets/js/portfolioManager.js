'use strict';

class ProductManager {
    constructor() {
        // DOM Elements
        this.category = document.querySelector('#category');
        this.container = document.querySelector('.main_manager_container');
        this.sidebar = document.querySelector('.sidebar_pages');
        this.lightbox = document.querySelector('.lightbox');



        // Pagination Configurations
        this.current_page = 1;
        this.per_page = 3;
        this.offset = 0;
        this.total_pages = 0;

        // Data fetched from the database
        this.database_data = {
            'category': 'images',
            'current_page': this.current_page,
            'per_page': this.per_page,
            'total_count': 0,
            'offset': this.offset
        };
        this.pages = [{}];

        // Binding `this` context to methods
        this.categoryUISuccess = this.categoryUISuccess.bind(this);
        this.paginationUISuccess = this.paginationUISuccess.bind(this);
        this.resetLinks = this.resetLinks.bind(this);
    }

    // Handle fetch errors
    handleErrors(response) {
        if (!response.ok) {
            throw (response.status + ' : ' + response.statusText);
        }
        return response;
    }

    // Update the UI upon successfully fetching gallery category data
    async categoryUISuccess(parsedData) {
        this.clearContainer();
        console.log('parsed data', parsedData);

        // Close the lightbox if it's open
        this.exitLightbox();

        parsedData.forEach((slide, index) => {
            this.createSlide(slide, index);
        });

        this.addImageListeners();
    }


    // Clear all child elements of the container
    clearContainer() {
        while (this.container.firstChild) {
            this.container.removeChild(this.container.firstChild);
        }
    }

    // Function to truncate text and add a "More..." link
    addReadMore(paragraph) {
        const fullText = paragraph.textContent;
        const truncatedText = fullText.slice(0, 300) + '...'; // Adjust the number of characters as needed
        const moreLink = document.createElement('span');
        moreLink.textContent = ' More...';
        //moreLink.style.color = 'blue';
        //moreLink.style.cursor = 'pointer';

        paragraph.textContent = truncatedText;
        paragraph.appendChild(moreLink);
        paragraph.dataset.fullText = fullText;

        moreLink.addEventListener('click', function() {
            if (paragraph.textContent.includes('...')) {
                paragraph.textContent = paragraph.dataset.fullText;
                moreLink.textContent = ' Less';
            } else {
                paragraph.textContent = truncatedText;
                paragraph.appendChild(moreLink);
            }
        });
    }

    equalizeArticleHeights() {
        const articles = document.querySelectorAll('.article');
        let maxHeight = 0;

        // Find the tallest article
        articles.forEach(article => {
            if (article.offsetHeight > maxHeight) {
                maxHeight = article.offsetHeight;
            }
        });

        // Set all articles to the height of the tallest one
        articles.forEach(article => {
            article.style.minHeight = `${maxHeight}px`;
        });
    }

    // Create individual gallery slides
    createSlide(slide) {
        const article = this.createElementWithClass('section', 'article');
        this.container.appendChild(article);

        const largeImage = this.createElementWithClass('a', 'largeImage');
        largeImage.href = "#";
        largeImage.setAttribute('data-image', slide.image_path);
        // Updated part: Attach event listener to largeImage
        largeImage.addEventListener('click', (event) => {
            event.preventDefault(); // Prevent default anchor action
            this.handleImageClick(largeImage);
        });
        article.appendChild(largeImage);
        console.log('largeImage', largeImage);

        const image = this.createElement('img', {
            src: slide.thumb_path,
            alt: slide.content,
            loading: 'lazy',
            class: 'imageStyle'
        });
        largeImage.appendChild(image);
        const heading = this.createElementWithClassAndContent('h2', 'articleHeading', slide.heading);
        article.appendChild(heading);
        const paragraph = this.createElementWithClassAndContent('p', 'articleText', slide.content);
        article.appendChild(paragraph);
        // Truncate text and add "More..." link
        this.addReadMore(paragraph);
        this.equalizeArticleHeights();
    }
    // Utility function to create an HTML element with attributes
    createElement(tag, attributes = {}) {
        const element = document.createElement(tag);
        Object.entries(attributes).forEach(([key, value]) => {
            element.setAttribute(key, value);
        });
        return element;
    }
    createElementWithClass(tag, className) {
        const element = this.createElement(tag);
        element.className = className;
        return element;
    }
    createElementWithClassAndContent(tag, className, content) {
        const element = this.createElementWithClass(tag, className);
        element.textContent = content;
        return element;
    }
    // Add click event listeners to images to open in lightbox
    addImageListeners() {
        const images = document.querySelectorAll('img');
        images.forEach(image => {
            image.addEventListener('click', () => this.handleImageClick(image));
        });
    }

    // Add click event listener to lightbox to close it
    addLightboxListener() {
        this.lightbox.addEventListener('click', () => {
            this.exitLightbox();
        });
    }


// Updated handleImageClick to include title and content
    handleImageClick(anchor) {
        const largeImageUrl = anchor.getAttribute('data-image'); // Get large image URL
        const title = anchor.getAttribute('data-title'); // Assuming you add this attribute
        const content = anchor.getAttribute('data-content'); // Assuming you add this attribute

        // Clear and set up the lightbox
        this.lightbox.innerHTML = ''; // Clear previous content

        // Create and display the large image
        const largeImage = this.createElement('img', { src: largeImageUrl });
        largeImage.classList.add('lightbox-image');
        this.lightbox.appendChild(largeImage);

        // Create and append the title
        if (title) {
            const imageTitle = this.createElementWithClassAndContent('h2', 'lightbox-title', title);
            this.lightbox.appendChild(imageTitle);
        }

        // Create and append the content
        if (content) {
            const imageContent = this.createElementWithClassAndContent('p', 'lightbox-content', content);
            this.lightbox.appendChild(imageContent);
        }

        // Show the lightbox
        this.lightbox.style.display = 'block';
    }

    // Close the lightbox
    exitLightbox() {
        this.lightbox.style.display = 'none'; // Hide the lightbox
        this.lightbox.innerHTML = ''; // Clear the lightbox content
    }

    // Handle errors when fetching gallery category data fails
    categoryUIError(error) {
        console.log("Database Table did not load", error);
    }

    // Send a request to the server to fetch images
    async createImageRequest(url, succeed, fail) {
        try {
            const response = await fetch(url, {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(this.database_data),
            });

            this.handleErrors(response);

            const data = await response.json();
            succeed(data);
        } catch (error) {
            fail(error);
        }
    }

    // Clear all pagination links
    resetLinks() {
        /* Remove Links For Screen (cleanup) */
        while (this.sidebar.firstChild) {
            this.sidebar.removeChild(this.sidebar.firstChild)
        }
    }

    // Update the UI with the received pagination data
    async paginationUISuccess(parsedData) {
        this.resetLinks();

        this.database_data.offset = await parsedData.offset;
        this.total_pages = Math.ceil(this.database_data.total_count / this.database_data.per_page);

        /* Create the Display Links and add an event listener */
        this.pages = [{}];
        /*
         * Creating the array of page object(s)
         */
        for (let x = 0; x < this.total_pages; x++) {
            this.pages[x] = {page: x + 1};
        }

        this.pages.forEach(link_page => {
            const links = document.createElement('div');
            links.className = 'links';
            this.sidebar.appendChild(links);
            /*
             * Add event listener for the links
             */
            links.addEventListener('click', () => {
                this.database_data.current_page = link_page.page;
                this.createRequest('portfolioPagination.php', this.paginationUISuccess, this.paginationUIError);
            });

            const pageText = document.createElement('p');
            pageText.className = 'linkStyle';
            pageText.id = 'page_' + link_page.page;
            pageText.textContent = link_page.page;
            links.appendChild(pageText);
            if (this.database_data.current_page === link_page.page) {
                links.style.backgroundColor = "#00b28d";
            }
        })

        await this.createImageRequest('portfolioGetImages.php', this.categoryUISuccess, this.categoryUIError);
    }


    // Handle errors when fetching pagination data fails
    paginationUIError(error) {
        console.log("Database Table did not load", error);
    }

    // Send a request to the server
    async createRequest(url, succeed, fail) {
        try {
            const response = await fetch(url, {
                method: 'POST', // or 'PUT'
                body: JSON.stringify(this.database_data),
            });

            this.handleErrors(response);

            const data = await response.json();
            //console.log('count', data);
            succeed(data);
        } catch (error) {
            fail(error);
        }
    }

    // Send a request to get the total number of images in a category
    async updateTotalCountAndPagination() {
        await this.createRequest('getTotalCount.php', this.totalCountUISuccess.bind(this), this.totalCountUIError.bind(this));
    }

    // Update the UI upon successfully fetching the total count
    totalCountUISuccess(parsedData) {
        this.database_data.total_count = parsedData.total_count;
        this.createRequest('portfolioPagination.php', this.paginationUISuccess.bind(this), this.paginationUIError.bind(this));
    }

    // Handle errors when fetching the total count fails
    totalCountUIError(error) {
        console.log("Database Table did not load", error);
    }

    // Add event listeners to DOM elements
    bindEvents() {
        this.category.addEventListener('change', () => {
            this.database_data.current_page = 1;
            this.database_data.category = this.category.value;
            this.updateTotalCountAndPagination();
        });

        document.addEventListener('DOMContentLoaded', () => {
            this.createRequest('portfolioPagination.php', this.paginationUISuccess.bind(this), this.paginationUIError.bind(this));
        });
    }

    // Initialization function
    init() {
        this.addLightboxListener();
        this.updateTotalCountAndPagination();
        this.bindEvents();
    }
}

const productManager = new ProductManager();
productManager.init();
