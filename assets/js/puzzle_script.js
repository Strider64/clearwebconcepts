/*
 * Jigsaw Puzzle 2.0 βeta
 * Created by John Pepp
 * on August 16, 2023
 * Updated by John Pepp
 * on September 23, 2023
 */

// 1. Initialize canvas, context, and audio assets
let canvas = document.getElementById('puzzleCanvas');
let ctx = canvas.getContext('2d');

// Sound Effects
let snapSound = new Audio('assets/audio/audio-snap-001.ogg');
let celebrateSound = new Audio('assets/audio/audio-celebration-001.wav'); // Change Path to the Correct One

let image; // Make image global
let pieces = []; // Array for the pieces of the puzzle
let draggedPiece = null;
let offsetX, offsetY;  // Offset from the top-left corner of the dragged piece
let isSolved = false; // Puzzle set to false which means it isn't solved
let puzzleContainer = document.querySelector('.puzzleImage');
puzzleContainer.style.display = 'none';
let puzzleImage = document.getElementById('puzzleImage');
let imageDescription = document.querySelector('.imageDescription');
let currentTitle = ''; // declare it outside to have it globally accessible
let selectedCategory = '';  // A variable to hold the selected category globally.
let titles_in_selected_category = []; // Global Variable
const PIECE_COUNT = 4;  // Number of puzzle pieces along one dimension
// Hide the alert
let alertOverlay = document.querySelector('.custom-alert-overlay');
let alertBox = document.querySelector('.custom-alert');
alertOverlay.style.display = "none";
alertBox.style.display = "none";

//Scoring System for Game
let totalTries = 0;
let correctTries = 0;

const populateTitles = () => {
    const selectedCategory = document.getElementById('category').value;

    // Clear the session of shown images when the category is changed
    fetch('clear_session.php')
        .then(() => {
            const selectElement = document.getElementById('title');
            fetch(`fetch_titles.php?category=${selectedCategory}`)
                .then(response => response.json())
                .then(titles => {
                    titles_in_selected_category = titles; //
                    //console.log('titles:', titles_in_selected_category, 'category', selectedCategory);
                    selectElement.innerHTML = '';
                    titles.forEach(title => {
                        const optionElement = document.createElement('option');
                        optionElement.value = title;
                        optionElement.textContent = title;
                        selectElement.appendChild(optionElement);
                    });
                    // If there are titles, load the first puzzle of the new category.
                    if(titles.length > 0) {
                        alertOverlay.style.display = "none";  // Hide the alert
                        alertBox.style.display = "none";      // Hide the alert
                        loadNextPuzzle(titles[0], selectedCategory);
                    }
                })
                .catch(error => console.error('Error fetching the titles:', error));
        })
        .catch(error => console.error('Error clearing the session:', error));
};



document.addEventListener('DOMContentLoaded', () => {
    // Populate titles when the page loads
    populateTitles();

    document.getElementById('title').addEventListener('change', (e) => {
        const selectedTitle = e.target.value;
        const selectedCategory = document.getElementById('category').value; // Get selected category here as well
        alertOverlay.style.display = "none";
        alertBox.style.display = "none";
        // Clear the session of shown images when the title is changed
        fetch('clear_session.php')
            .then(() => {
                // Load next puzzle after session is cleared
                loadNextPuzzle(selectedTitle, selectedCategory);
            })
            .catch(error => console.error('Error clearing the session:', error));
    });


    // Also populate titles when the selected category changes
    document.getElementById('category').addEventListener('change', populateTitles);
});


const loadNextPuzzle = (title = '') => {
    let url = 'fetch_image.php';
    // Reset counters
    totalTries = 0;
    correctTries = 0;
    if (selectedCategory) url += `?category=${selectedCategory}`;
    if (title) url += (selectedCategory ? '&' : '?') + `title=${title}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            currentTitle = data.title || '';
            //console.log(data);
            // Extract the image path and description from the JSON response
            const image_path = data.image_path;
            const description = data.description;

            if (data.image_path === 'NO_MORE_IMAGES') {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                // Set font, size, and color
                ctx.font = '30px Arial';
                ctx.fillStyle = 'black';

                // Align the text to be centered
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText("Please Select an Image!", canvas.width / 2, canvas.height / 2);
            }


            image = new Image();
            image.src = image_path;
            //console.log('image.src', image.src);
            imageDescription.textContent = description;
            puzzleContainer.style.display = 'block';
            // Set the src for the img element to display the image on the page
            puzzleImage.src = image_path;
            // Ensure the image is loaded before proceeding
            image.onload = function () {
                // Reset pieces and isSolved flag
                pieces = [];
                isSolved = false;

                let pieceWidth = image.width / PIECE_COUNT;
                let pieceHeight = image.height / PIECE_COUNT;

                const { imageX, imageY } = setupCanvasBackground(image);

                for (let x = 0; x < PIECE_COUNT; x++) {
                    for (let y = 0; y < PIECE_COUNT; y++) {
                        let piece;
                        let isOverlapping;
                        let attempts = 0;
                        do {
                            isOverlapping = false;
                            piece = {
                                x: x * pieceWidth,
                                y: y * pieceHeight,
                                sx: Math.random() * (canvas.width - pieceWidth),
                                sy: Math.random() * (canvas.height - pieceHeight),
                                width: pieceWidth,
                                height: pieceHeight,
                                snapped: false,
                                rotation: 0 // Add a rotation property
                            };

                            if (piece.sx > imageX && piece.sx + piece.width < imageX + image.width &&
                                piece.sy > imageY && piece.sy + piece.height < imageY + image.height) {
                                isOverlapping = true;
                            }
                            for (let placedPiece of pieces) {
                                if (doesOverlap(piece, placedPiece)) {
                                    isOverlapping = true;
                                    break;
                                }
                            }
                            attempts++;
                            if (attempts > MAX_ATTEMPTS) {
                                console.warn("Could not find a non-overlapping position after maximum attempts.");
                                break;
                            }
                        } while (isOverlapping);

                        pieces.push(piece);

                        // Draw the puzzle piece using the generateIrregularPiecePath function
                        ctx.save();
                        ctx.translate(piece.sx, piece.sy);
                        generateIrregularPiecePath(ctx, 0, 0, piece.width, piece.height, piece.rotation);
                        ctx.clip();
                        ctx.drawImage(image, piece.x, piece.y, piece.width, piece.height, 0, 0, piece.width, piece.height);
                        ctx.restore();
                    }
                }

                // Add mouse event listeners
                canvas.addEventListener('mousedown', handleMouseDown);
                canvas.addEventListener('mousemove', handleMouseMove);
                canvas.addEventListener('mouseup', handleMouseUp);
            };



            image.onerror = function () {
                console.error("Error loading the image.");
            };
        })

        .catch(error => {
            console.error("Error fetching the image path:", error);
        });
};

const MAX_ATTEMPTS = 100;  // Maximum attempts for finding non-overlapping positions for puzzle pieces

const isMouseOverPiece = (mouseX, mouseY) => {
    for (let piece of pieces) {
        if (mouseX > piece.sx && mouseX < piece.sx + piece.width &&
            mouseY > piece.sy && mouseY < piece.sy + piece.height) {
            return true;
        }
    }
    return false;
};




// Event listeners for dragging puzzle pieces
// On mouse down, check if any piece is clicked
const handleMouseDown = e => {
    if (isSolved) return;
    totalTries++;
    let mouseX = e.clientX - canvas.getBoundingClientRect().left;
    let mouseY = e.clientY - canvas.getBoundingClientRect().top;

    for (let piece of pieces) {
        if (!piece.snapped && mouseX > piece.sx && mouseX < piece.sx + piece.width &&
            mouseY > piece.sy && mouseY < piece.sy + piece.height) {
            draggedPiece = piece;

            offsetX = mouseX - piece.sx;
            offsetY = mouseY - piece.sy;
            break;
        }
    }
};

function redrawPiece(piece) {
    const { imageX, imageY } = setupCanvasBackground(image);
    for (let p of pieces) {
        if (p !== piece) {
            ctx.save();
            ctx.translate(p.sx, p.sy);
            generateIrregularPiecePath(ctx, 0, 0, p.width, p.height, p.rotation);
            ctx.clip();
            ctx.drawImage(image, p.x, p.y, p.width, p.height, 0, 0, p.width, p.height);
            ctx.restore();
        }
    }
    ctx.save();
    ctx.translate(piece.sx, piece.sy);
    generateIrregularPiecePath(ctx, 0, 0, piece.width, piece.height, piece.rotation);
    ctx.clip();
    ctx.drawImage(image, piece.x, piece.y, piece.width, piece.height, 0, 0, piece.width, piece.height);
    ctx.restore();
}



const handleMouseMove = e => {
    let mouseX = e.clientX - canvas.getBoundingClientRect().left;
    let mouseY = e.clientY - canvas.getBoundingClientRect().top;

    if (!draggedPiece) {
        if (isMouseOverPiece(mouseX, mouseY)) {
            canvas.style.cursor = 'pointer';  // Change cursor to hand
        } else {
            canvas.style.cursor = 'default';  // Change cursor back to arrow
        }
    }

    if (draggedPiece) {
        draggedPiece.sx = mouseX - offsetX;
        draggedPiece.sy = mouseY - offsetY;

        // Update the rotation of the piece
        const dx = mouseX - draggedPiece.sx;
        const dy = mouseY - draggedPiece.sy;
        const angle = Math.atan2(dy, dx) * 180 / Math.PI;
        draggedPiece.rotation = angle;

        redrawPiece(draggedPiece);
    }
};

// On mouse up, release the piece and snap it if close to its correct position
const handleMouseUp = e => {
    if (draggedPiece) {
        const imageX = (canvas.width - image.width) / 2;
        const imageY = (canvas.height - image.height) / 2;
        let targetX = draggedPiece.x + imageX;
        let targetY = draggedPiece.y + imageY;
        let threshold = 20;

        if (Math.abs(draggedPiece.sx - targetX) < threshold && Math.abs(draggedPiece.sy - targetY) < threshold) {
            correctTries++;
            draggedPiece.sx = targetX;
            draggedPiece.sy = targetY;
            draggedPiece.snapped = true;  // Piece is now in its correct position
            snapSound.play();
        }

        redrawCanvas();
        draggedPiece = null;
    }

    if (checkForCompletion()) {
        // Remove mouse events after puzzle is solved
        canvas.removeEventListener('mousedown', handleMouseDown);
        canvas.removeEventListener('mousemove', handleMouseMove);
        canvas.removeEventListener('mouseup', handleMouseUp);
        canvas.style.cursor = 'default';  // Change cursor back to arrow
        celebrateSound.play();
        showAlert('Click to Continue');

    } else {
        // Check for cursor change
        let mouseX = e.clientX - canvas.getBoundingClientRect().left;
        let mouseY = e.clientY - canvas.getBoundingClientRect().top;

        if (isMouseOverPiece(mouseX, mouseY)) {
            canvas.style.cursor = 'pointer';  // Change cursor to hand
        } else {
            canvas.style.cursor = 'default';  // Change cursor back to arrow
        }
    }
};

function calculateScore() {
    if (totalTries === 0) return 0;
    return (correctTries / totalTries) * 100;
}

function showAlert(message) {

    let alertText = document.getElementById('alertText');
    alertBox.style.position = 'absolute'; // Ensure that the position is set to absolute
    document.getElementById('customAlertContent').addEventListener('click', closeAlert);
    // Calculate and add score to the alert message
    let score = calculateScore();
    alertText.textContent = 'Your score: ' + score.toFixed(2) + '% correct tries. ' + message;
    alertOverlay.style.display = "flex";
    alertBox.style.display = "block";
}

function closeAlert() {

    // Remove the solved puzzle title from the titles_in_selected_category array
    titles_in_selected_category = titles_in_selected_category.filter(title => title !== currentTitle);

    // Redraw the title select element with the remaining titles
    const selectElement = document.getElementById('title');
    selectElement.innerHTML = ''; // clear existing options
    titles_in_selected_category.forEach(title => {
        const optionElement = document.createElement('option');
        optionElement.value = title;
        optionElement.textContent = title;
        selectElement.appendChild(optionElement);
    });

    // Load the next puzzle if there are remaining titles, else handle the case where there are no more titles
    if(titles_in_selected_category.length > 0) {
        loadNextPuzzle(titles_in_selected_category[0], selectedCategory);
    } else {
        // Handle the case where there are no more titles in the selected category, e.g., display a message
    }

    // Hide the alert
    alertOverlay.style.display = "none";
    alertBox.style.display = "none";
}




// Puzzle completion check
function checkForCompletion() {
    for (let piece of pieces) {
        if (!piece.snapped) {
            return false;
        }
    }
    isSolved = true;  // Puzzle is completed
    return true;
}

function setupCanvasBackground() {
    // Clear the entire canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    // Create a radial gradient
    let radialGradient = ctx.createRadialGradient(
        canvas.width / 2, canvas.height / 2, 10,
        canvas.width / 2, canvas.height / 2, canvas.width / 2
    );
    radialGradient.addColorStop(0, '#FFFFFF');
    radialGradient.addColorStop(1, '#4b81f8');

    ctx.fillStyle = radialGradient;
    ctx.fillRect(0, 0, canvas.width, canvas.height);


    // Calculate image coordinates and set the background color for the image portion
    const imageX = (canvas.width - image.width) / 2;
    const imageY = (canvas.height - image.height) / 2;

    // Set up drop shadow properties
    ctx.shadowOffsetX = 5; // Horizontal shadow offset, adjust as needed
    ctx.shadowOffsetY = 5; // Vertical shadow offset, adjust as needed
    ctx.shadowBlur = 10;   // Blur level of the shadow, adjust for softer or harder shadow
    ctx.shadowColor = 'rgba(0, 0, 0, 0.5)'; // Shadow color and opacity

    // Draw the image background with the shadow
    ctx.fillStyle = "#FFFFFF";
    ctx.fillRect(imageX, imageY, image.width, image.height);

    // Reset shadow properties to avoid affecting other canvas elements
    ctx.shadowOffsetX = 0;
    ctx.shadowOffsetY = 0;
    ctx.shadowBlur = 0;
    ctx.shadowColor = 'transparent';


    return {imageX, imageY};  // Return these values since they're used in both places.
}

function generateIrregularPiecePath(ctx, x, y, width, height) {
    ctx.beginPath();
    ctx.moveTo(x + Math.random() * 10, y);

    // Top line with slight curves
    ctx.lineTo(x + width / 3, y + Math.random() * 10);
    ctx.quadraticCurveTo(x + width / 2, y - Math.random() * 10, x + 2 * width / 3, y + Math.random() * 10);
    ctx.lineTo(x + width, y + Math.random() * 10);

    // Right line with a bump
    ctx.lineTo(x + width, y + height / 2 - Math.random() * 10);
    ctx.quadraticCurveTo(x + width + Math.random() * 10, y + height / 2, x + width, y + height / 2 + Math.random() * 10);
    ctx.lineTo(x + width, y + height - Math.random() * 10);

    // Bottom line with curves
    ctx.lineTo(x + 2 * width / 3, y + height - Math.random() * 10);
    ctx.quadraticCurveTo(x + width / 2, y + height + Math.random() * 10, x + width / 3, y + height - Math.random() * 10);
    ctx.lineTo(x, y + height - Math.random() * 10);

    // Left line with a slight wave
    ctx.lineTo(x, y + height / 2 + Math.random() * 10);
    ctx.quadraticCurveTo(x - Math.random() * 10, y + height / 2, x, y + height / 2 - Math.random() * 10);
    ctx.closePath();
}


// Redraws the entire canvas
function redrawCanvas() {
    const { imageX, imageY } = setupCanvasBackground();

    for (let piece of pieces) {
        ctx.save();
        ctx.translate(piece.sx, piece.sy);
        ctx.rotate(piece.rotation * Math.PI / 180);
        generateIrregularPiecePath(ctx, -piece.width / 2, -piece.height / 2, piece.width, piece.height);
        ctx.clip();
        ctx.drawImage(image, piece.x, piece.y, piece.width, piece.height, -piece.width / 2, -piece.height / 2, piece.width, piece.height);
        ctx.restore();
    }
}


// Function to check if two pieces overlap
function doesOverlap(piece1, piece2) {
    return piece1.sx < piece2.sx + piece2.width &&
        piece1.sx + piece1.width > piece2.sx &&
        piece1.sy < piece2.sy + piece2.height &&
        piece1.sy + piece1.height > piece2.sy;
}