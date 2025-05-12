/* Written & debugged by: Tech Ngoun Leang, Samady Sok, Longwei Ngor
Tested by: Tech Ngoun Leang */
$(document).ready(function() {
    $(document).ready(function() {
        // Slider functionality for menu sections
        function showSlide($slides, index) {
            $slides.removeClass('active').fadeOut(500);
            $($slides[index]).addClass('active').fadeIn(500);
        }
    
        function initializeSlider(sliderContainer, prevButton, nextButton) {
            const $slides = $(sliderContainer).find('.menu-slide');
            let currentIndex = 0;
            const totalSlides = $slides.length;
    
            showSlide($slides, currentIndex);
    
            $(nextButton).click(function() {
                currentIndex = (currentIndex + 1) % totalSlides;
                showSlide($slides, currentIndex);
            });
    
            $(prevButton).click(function() {
                currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
                showSlide($slides, currentIndex);
            });
        }
    
        // Function to open an image in the center of the screen with page navigation
        function openModal(slides, startIndex) {
            const $modal = $('<div>').css({
                position: 'fixed',
                top: '50%',
                left: '50%',
                transform: 'translate(-50%, -50%)',
                background: 'rgba(0, 0, 0, 0.8)',
                padding: '20px',
                zIndex: 1000,
                borderRadius: '10px',
                display: 'block',
                textAlign: 'center',
                width: '90vw', // Modal takes 90% of the viewport width
                height: '90vh', // Modal takes 90% of the viewport height
            });
    
            const $img = $('<img>').css({
                maxWidth: '100%',
                maxHeight: '80vh',
                display: 'block',
                margin: '0 auto',
                transition: 'transform 0.3s ease', // Smooth transition for zoom effect
                cursor: 'zoom-in' // Change cursor to zoom-in
            }).attr('src', $(slides[startIndex]).find('img').attr('src'));
    
            const $prevButton = $('<button>').text('←').css({
                position: 'absolute',
                top: '50%',
                left: '10px',
                fontSize: '2rem',
                color: 'white',
                background: 'none',
                border: 'none',
                cursor: 'pointer',
                zIndex: 1100
            });
    
            const $nextButton = $('<button>').text('→').css({
                position: 'absolute',
                top: '50%',
                right: '10px',
                fontSize: '2rem',
                color: 'white',
                background: 'none',
                border: 'none',
                cursor: 'pointer',
                zIndex: 1100
            });
    
            // Close button (X)
            const $closeButton = $('<button>').text('X').css({
                position: 'absolute',
                top: '10px',
                right: '10px',
                fontSize: '2rem',
                color: 'white',
                background: 'none',
                border: 'none',
                cursor: 'pointer',
                zIndex: 1100
            });
    
            let currentIndex = startIndex;
            let zoomedIn = false; // Track zoom state
    
            // Update the image when navigating
            function updateImage() {
                const src = $(slides[currentIndex]).find('img').attr('src');
                $img.attr('src', src);
            }
    
            // Navigate images
            $prevButton.click(function(event) {
                currentIndex = (currentIndex - 1 + slides.length) % slides.length;
                updateImage();
                event.stopPropagation();  // Prevent closing modal when clicking on prev button
            });
    
            $nextButton.click(function(event) {
                currentIndex = (currentIndex + 1) % slides.length;
                updateImage();
                event.stopPropagation();  // Prevent closing modal when clicking on next button
            });
    
            // Close the modal when clicking outside the modal or on the X button
            $modal.click(function(event) {
                if (!$(event.target).closest('button, img').length) {
                    $modal.remove();
                }
            });
    
            $closeButton.click(function(event) {
                $modal.remove();  // Close modal when clicking on the close (X) button
                event.stopPropagation();  // Prevent closing modal when clicking on close button
            });
    
            // Double-click to zoom in/out on the clicked area
            $img.dblclick(function(event) {
                if (zoomedIn) {
                    $img.css('transform', 'scale(1)'); // Zoom out
                    zoomedIn = false;
                    $img.css('cursor', 'zoom-in'); // Reset cursor
                } else {
                    // Calculate where the user double-clicked relative to the image's position
                    const offsetX = event.offsetX / $img.width();
                    const offsetY = event.offsetY / $img.height();
    
                    // Zoom in on the clicked area
                    $img.css({
                        transform: 'scale(2)', // Zoom in (scale 2x)
                        transformOrigin: `${offsetX * 100}% ${offsetY * 100}%` // Set zoom origin to clicked point
                    });
                    zoomedIn = true;
                    $img.css('cursor', 'zoom-out'); // Change cursor to zoom-out
                }
                event.stopPropagation();  // Prevent closing modal when double-clicking on the image
            });
    
            // Append modal elements to body
            $modal.append($img).append($prevButton).append($nextButton).append($closeButton);
            $('body').append($modal);
        }
    
        // Initialize sliders for each menu
        initializeSlider('#menupages .left-container .menu-slider', '#prev-left', '#next-left');
        initializeSlider('#menupages .middle-container .menu-slider', '#prev-middle', '#next-middle');
        initializeSlider('#menupages .right-container .menu-slider', '#prev-right', '#next-right');  // Add the drinks menu slider initialization
    
        // Set up double-click event for enlarging images in modal for left and middle containers
        $('#menupages .left-container .menu-slider .menu-slide').dblclick(function() {
            const slides = $('#menupages .left-container .menu-slider .menu-slide');
            const index = slides.index(this);
            openModal(slides, index);
        });
    
        $('#menupages .middle-container .menu-slider .menu-slide').dblclick(function() {
            const slides = $('#menupages .middle-container .menu-slider .menu-slide');
            const index = slides.index(this);
            openModal(slides, index);
        });
    
        // Set up double-click event for enlarging images in modal for drinks menu (right container)
        $('#menupages .right-container .menu-slider .menu-slide').dblclick(function() {
            const slides = $('#menupages .right-container .menu-slider .menu-slide');
            const index = slides.index(this);
            openModal(slides, index);
        });
    });

        // Function to create a modal for a single image with zoom and pan functionality
        function createModalForImage(imageSrc) {
            const $modal = $('<div>').css({
                position: 'fixed',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                background: 'rgba(0, 0, 0, 0.9)',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                zIndex: 1000,
            });
    
            const $img = $('<img>').attr('src', imageSrc).css({
                maxWidth: '100%',
                maxHeight: '100%',
                cursor: 'grab',
                transition: 'transform 0.3s ease', // Smooth zoom effect
            });
    
            const $closeButton = $('<button>').text('X').css({
                position: 'absolute',
                top: '10px',
                right: '10px',
                fontSize: '2rem',
                color: 'white',
                background: 'none',
                border: 'none',
                cursor: 'pointer',
                zIndex: 1100,
            });
    
            let isZoomed = false;
            let isDragging = false;
            let lastX = 0, lastY = 0;
    
            // Handle double-click for zooming in/out
            $img.dblclick(function (event) {
                if (isZoomed) {
                    $img.css({
                        transform: 'scale(1)',
                        cursor: 'grab',
                    });
                    isZoomed = false;
                } else {
                    // Calculate the origin for zooming
                    const offsetX = event.offsetX / $img.width();
                    const offsetY = event.offsetY / $img.height();
                    $img.css({
                        transform: 'scale(2)',
                        transformOrigin: `${offsetX * 100}% ${offsetY * 100}%`,
                        cursor: 'grab',
                    });
                    isZoomed = true;
                }
            });
    
            // Handle mouse dragging
            $img.on('mousedown', function (event) {
                if (isZoomed) {
                    isDragging = true;
                    lastX = event.clientX;
                    lastY = event.clientY;
                    $img.css('cursor', 'grabbing');
                }
            });
    
            $(document).on('mousemove', function (event) {
                if (isDragging) {
                    const dx = event.clientX - lastX;
                    const dy = event.clientY - lastY;
                    const currentTransform = $img.css('transform');
                    const match = currentTransform.match(/matrix\(([^)]+)\)/);
                    let [translateX, translateY] = [0, 0];
    
                    if (match) {
                        const values = match[1].split(', ');
                        translateX = parseFloat(values[4]);
                        translateY = parseFloat(values[5]);
                    }
    
                    $img.css({
                        transform: `translate(${translateX + dx}px, ${translateY + dy}px) scale(2)`,
                    });
    
                    lastX = event.clientX;
                    lastY = event.clientY;
                }
            });
    
            $(document).on('mouseup', function () {
                if (isDragging) {
                    isDragging = false;
                    $img.css('cursor', 'grab');
                }
            });
    
            // Close the modal when clicking outside the image or on the close button
            $modal.click(function (event) {
                if (!$(event.target).closest('img, button').length) {
                    $modal.remove();
                }
            });
    
            $closeButton.click(function () {
                $modal.remove();
            });
    
            $modal.append($img).append($closeButton);
            $('body').append($modal);
        }
    
        // Apply double-click event for the drink menu image
        $('.right-container img').dblclick(function () {
            const imageSrc = $(this).attr('src');
            createModalForImage(imageSrc);
        });
    
    // Constants for product prices
    const productPrices = {
        "Chocolate Cake": 12.99,
        "Wheat Bread": 3.49,
        "Croissant": 2.99,
        "Cherry Cheesecake": 5.99,
        "Ham Sandwich": 4.49,
        "Macarons": 6.99,
        "Tarts": 3.99,
        "Pancake": 7.99
    };

    // Order Form Functionality
    function setupOrderForm() {
        // Add new product field functionality
        $(document).on("click", ".add-item", function() {
            const newItem = `
                <div class="order-item">
                    <div class="product-quantity-container">
                        <div class="product-container">
                            <label for="product" class="required-field">Select a Product:</label>
                            <select class="form-control" name="product[]" required>
                                <option value="" disabled selected>Select a product</option>
                                ${Object.keys(productPrices).map(product => `<option value="${product}">${product}</option>`).join('')}
                            </select>
                            <div class="error-message" style="display:none;">Please select a product.</div>
                        </div>
                        <div class="quantity-container">
                            <label for="quantity" class="required-field">Quantity:</label>
                            <input type="number" class="form-control" name="quantity[]" min="1" required>
                            <div class="error-message" style="display:none;">Please enter a valid quantity.</div>
                        </div>
                    </div>
                    <div class="order-item-buttons">
                        <button type="button" class="add-item">Add Product</button>
                        <button type="button" class="remove-item">Remove Product</button>
                    </div>
                </div>
            `;
            $("#order-items").append(newItem);
        });
    
        // Remove product field functionality
        $(document).on("click", ".remove-item", function() {
            if ($(".order-item").length > 1) {
                $(this).closest(".order-item").remove();
            } else {
                alert("You must have at least one product and quantity.");
            }
        });
    
        // Form validation
        $("#myForm").on("submit", function(event) {
            event.preventDefault();
            let isValid = true;
            let totalPrice = 0;
    
            // Validate name
            const name = $("#name").val().trim();
            if (name === "") {
                showError("#name", "#nameError");
                isValid = false;
            } else {
                hideError("#name", "#nameError");
            }
    
            // Validate email
            const email = $("#email").val().trim();
            const emailRegex = /.+@.+\..+/;
            if (!email || !emailRegex.test(email)) {
                showError("#email", "#emailError");
                isValid = false;
            } else {
                hideError("#email", "#emailError");
            }
    
            // Validate phone number
            const phone = $("#phone").val().trim();
            const phoneRegex = /^\d{10}$/;
            if (!phone || !phoneRegex.test(phone)) {
                showError("#phone", "#phoneError");
                isValid = false;
            } else {
                hideError("#phone", "#phoneError");
            }
    
            // Validate product and quantity for each item
            $(".order-item").each(function() {
                const product = $(this).find("select[name='product[]']").val();
                const quantity = $(this).find("input[name='quantity[]']").val();
    
                // Validate product
                if (!product) {
                    showErrorMessage($(this).find(".product-container"), "Please select a product.");
                    isValid = false;
                } else {
                    hideErrorMessage($(this).find(".product-container"));
                }
    
                // Validate quantity
                if (!quantity || parseInt(quantity) <= 0) {
                    showErrorMessage($(this).find(".quantity-container"), "Please enter a valid quantity.");
                    isValid = false;
                } else {
                    hideErrorMessage($(this).find(".quantity-container"));
                }
    
                // Calculate total price for this item
                if (product && quantity) {
                    const productPrice = productPrices[product];
                    totalPrice += productPrice * quantity;
                }
            });
    
            // If form is valid, submit the form
            if (isValid) {
                submitForm(totalPrice);
            } else {
                $("#formResponse")
                    .removeClass("success")
                    .addClass("error")
                    .text("Please correct the errors above.")
                    .fadeIn();
            }
        });
    
        function showError(selector, errorSelector) {
            $(errorSelector).fadeIn();
            shakeField(selector);
            $(selector).css("border", "1px solid red");
        }
    
        function hideError(selector, errorSelector) {
            $(errorSelector).fadeOut();
            $(selector).css("border", "");
        }
    
        function showErrorMessage(container, message) {
            container.find(".error-message").text(message).fadeIn();
            shakeField(container.find("select, input"));
            container.find("select, input").css("border", "1px solid red");
        }
    
        function hideErrorMessage(container) {
            container.find(".error-message").fadeOut();
            container.find("select, input").css("border", "");
        }
    
        function submitForm(totalPrice) {
            const formData = $("#myForm").serialize();
            console.log("Serialized data:", formData);
    
            setTimeout(function() {
                const name = $('#name').val().trim();
                const responseMessage = `Thank you for your order${name ? `, ${name}` : ''}! Your order has been received.`;
    
                // Display success message
                $("#formResponse")
                    .removeClass("error")
                    .addClass("success")
                    .text(responseMessage)
                    .fadeIn();
    
                // Create and display the receipt
                let receiptHtml = `<h2 style="margin-bottom: 15px;">Order Receipt</h2><ul style="list-style-type: none; padding-left: 0; margin-bottom: 20px;">`;
                $(".order-item").each(function() {
                    const product = $(this).find("select[name='product[]']").val();
                    const quantity = $(this).find("input[name='quantity[]']").val();
                    if (product && quantity) {
                        const itemTotal = productPrices[product] * quantity;
                        receiptHtml += `<li style="margin-bottom: 10px;">${quantity} x ${product} - $${itemTotal.toFixed(2)}</li>`;
                    }
                });
    
                // Add the total price
                receiptHtml += `<li style="margin-top: 15px;"><strong>Total Price: $${totalPrice.toFixed(2)}</strong></li></ul>`;
                $("#formResponse").append(receiptHtml);
    
                // Reset the form
                $("#myForm")[0].reset();
            }, 200);
        }
    
        function shakeField(field) {
            $(field).css("animation", "shake 0.3s ease-in-out");
            setTimeout(() => $(field).css("animation", ""), 300);
        }
    }
    
    setupOrderForm();

// Function to update order summary
function updateOrderSummary() {
    let summaryHtml = "";
    let totalPrice = 0; // Initialize the total price to 0
    
    $(".order-item").each(function() {
        const product = $(this).find("select[name='product[]']").val();
        const quantity = $(this).find("input[name='quantity[]']").val();
        
        // Ensure product and quantity are selected/valid
        if (product && quantity) {
            // Retrieve the price for the selected product
            const price = productPrices[product];
            
            // Calculate the total price for the item (quantity * price)
            const itemTotal = (price * quantity).toFixed(2);
            totalPrice += parseFloat(itemTotal); // Add to total price
            
            // Add the item and its total price to the summary
            summaryHtml += `<p>${quantity} x ${product} - $${itemTotal}</p>`;
        }
    });

    // Add a dividing line and the total price to the summary
    summaryHtml += `<hr><p><strong>Total: $${totalPrice.toFixed(2)}</strong></p>`;

    // Update order summary section with item details and total price
    $("#order-summary").html(summaryHtml);
}

// Toggle order summary visibility
$("#toggle-summary").click(function() {
    $("#order-summary").toggle(); // Show/hide the summary
    updateOrderSummary(); // Update the order summary when toggled
});

// Call the update function whenever items are added or removed
$(document).on("change", ".order-item select, .order-item input", updateOrderSummary);


// Animations
    function setupAnimations() {
        // Hover effects
        $(".logo img").hover(
            function() {
                $(this).css("transform", "scale(1.1)");
            },
            function() {
                $(this).css("transform", "scale(1)");
            }
        );

        $(".join-button").hover(
            function() {
                $(this).css({
                    transform: "scale(1.15)",
                    background: "#2d3333",
                    color: "#fff"
                });
            },
            function() {
                $(this).css({
                    transform: "scale(1)",
                    background: "#83B6B9",
                    color: "#ffff"
                });
            }
        );

        $("#five-select .selection img").hover(
            function() {
                $(this).css({
                    transform: "scale(1.3)",
                    transition: "transform 0.2s ease-in-out"
                });
            },
            function() {
                $(this).css("transform", "scale(1)");
            }
        );

        // Scroll-triggered animations
        function applyScrollEffect($elements) {
            $(window).on("scroll", function() {
                $elements.each(function() {
                    const rect = this.getBoundingClientRect();
                    if (rect.top < window.innerHeight && rect.bottom >= 0) {
                        $(this).css({
                            transition: "opacity 0.6s ease-in-out, transform 0.6s ease-in-out",
                            opacity: "1",
                            transform: "translateY(0)"
                        });
                    } else {
                        $(this).css({
                            opacity: "0",
                            transform: "translateY(50px)"
                        });
                    }
                });
            });
        }

        applyScrollEffect($('.testimonial'));
        applyScrollEffect($('.award'));

        // Dropdown navigation highlighting
        const menuLinks = $(".dropdown-content a");
        const menuMap = {
            pastries: $(".left-container"),
            signatures: $(".middle-container"),
            drinks: $(".right-container")
        };

        menuLinks.on("click", function() {
            $.each(menuMap, function(_, $container) {
                $container.css({
                    border: "none",
                    backgroundColor: ""
                });
            });

            const targetContainer = menuMap[this.id];
            if (targetContainer) {
                targetContainer.css({
                    border: "3px solid #007bff",
                    backgroundColor: "#e8f4ff"
                });
            }
        });
    }

    setupAnimations();
    
});

// Google Map Location API
function initMap() {
    // Coordinates
    const location = {
        lat: 11.615726863286849,
        lng: 104.90259794080816
    };

    // Create the map instance
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: location,
    });

    // Add a marker at the school location
    const marker = new google.maps.Marker({
        position: location,
        map: map,
        title: "Cresences Bakery",
        animation: google.maps.Animation.DROP,
    });

    // Explicitly using the marker to avoid the unused variable warning
    marker.setMap(map);
    marker.addListener("click", function() {
    alert("You clicked on the marker!");
});

// Chatbot Functionality
document.addEventListener('DOMContentLoaded', function() {
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-button');
    const chatContainer = document.getElementById('chat-container');

    // Predefined product list for recommendations
    const products = [
        { 
            name: "Cherry Cheesecake", 
            description: "Creamy cheesecake topped with fresh cherry compote",
            category: "Signature",
            price: 5.99
        },
        { 
            name: "Chocolate Croissant", 
            description: "Buttery croissant filled with rich dark chocolate",
            category: "Pastry",
            price: 4.25
        },
        { 
            name: "Berry Pancakes", 
            description: "Fluffy pancakes with mixed berry compote and maple syrup",
            category: "Breakfast",
            price: 8.75
        },
        { 
            name: "Cappuccino", 
            description: "Espresso with steamed milk and perfect foam art",
            category: "Drink",
            price: 3.50
        },
        { 
            name: "Chai Tea Latte", 
            description: "Spiced tea with steamed milk and aromatic spices",
            category: "Drink",
            price: 4.00
        }
    ];

    // Simple recommendation engine
    function recommendProduct(message) {
        const lowercaseMessage = message.toLowerCase();

        // Specific category matching
        const categoryMatches = {
            'drink': products.filter(p => p.category === 'Drink'),
            'sweet': products.filter(p => ['Signature', 'Dessert'].includes(p.category)),
            'pastry': products.filter(p => p.category === 'Pastry'),
            'chocolate': products.filter(p => p.name.toLowerCase().includes('chocolate'))
        };

        // Keyword matching
        const keywords = [
            { words: ['coffee', 'caffeine'], match: categoryMatches['drink'] },
            { words: ['sweet', 'dessert', 'cake'], match: categoryMatches['sweet'] },
            { words: ['pastry', 'bread'], match: categoryMatches['pastry'] },
            { words: ['chocolate'], match: categoryMatches['chocolate'] }
        ];

        // Find matching products
        for (let keyword of keywords) {
            if (keyword.words.some(word => lowercaseMessage.includes(word))) {
                return keyword.match.length > 0 
                    ? keyword.match[Math.floor(Math.random() * keyword.match.length)]
                    : products[Math.floor(Math.random() * products.length)];
            }
        }

        // Default recommendation
        return products[Math.floor(Math.random() * products.length)];
    }

    // Send message function
    function sendMessage() {
        const message = chatInput.value.trim();
        
        if (!message) return;

        // Clear input
        chatInput.value = '';

        // Add user message
        addMessageToChat(message, 'user');

        // Get recommendation
        const recommendation = recommendProduct(message);

        // Generate response
        const responseMessage = `I recommend our delightful ${recommendation.name}. 
        ${recommendation.description}. 
        It's priced at $${recommendation.price} and would be perfect for you!`;

        // Add bot response
        addMessageToChat(responseMessage, 'bot');
    }

    // Add message to chat
    function addMessageToChat(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}-message p-2 mb-2 rounded`;
        
        const paragraph = document.createElement('p');
        paragraph.className = 'mb-0';
        paragraph.textContent = message;
        
        messageDiv.appendChild(paragraph);
        chatContainer.appendChild(messageDiv);
        
        // Auto scroll to bottom
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Event listeners
    if (sendButton) {
        sendButton.addEventListener('click', sendMessage);
    }

    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });
    }
});

function validatePassword(password) {
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
    return passwordRegex.test(password);
}

// Use in form validation
$("#myForm").on("submit", function(event) {
    const password = $("#password").val();
    if (!validatePassword(password)) {
        event.preventDefault();
        $("#passwordError").text("Password must be at least 8 characters long and contain uppercase, lowercase, number, and special character").show();
    }
});

}






