/* Written & debugged by: Tech Ngoun Leang, Samady Sok, Longwei Ngor
Tested by: Tech Ngoun Leang */
/**
 * Cresences Bakery
 * Main JavaScript functionality for the bakery storefront
 */

$(document).ready(function() {
    // Add animation to selection images
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

    // Add animation to social media icons
    $(".logo").hover(
        function() {
            $(this).css("transform", "scale(1.15)");
        },
        function() {
            $(this).css("transform", "scale(1)");
        }
    );

    // Initialize chatbot functionality
    initChatbot();
});

/**
 * Chatbot functionality for product recommendations
 */
function initChatbot() {
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

        // Add bot response with a slight delay to seem more natural
        setTimeout(function() {
            addMessageToChat(responseMessage, 'bot');
        }, 500);
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
}

/**
 * Google Maps initialization
 */
function initMap() {
    // Coordinates for the bakery location
    const location = {
        lat: 11.615726863286849,
        lng: 104.90259794080816
    };

    // Create the map instance
    const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 15,
        center: location,
        styles: [
            {
                "featureType": "poi.business",
                "elementType": "labels",
                "stylers": [
                    { "visibility": "on" }
                ]
            }
        ]
    });

    // Add a marker at the bakery location
    const marker = new google.maps.Marker({
        position: location,
        map: map,
        title: "Cresences Bakery",
        animation: google.maps.Animation.DROP,
    });

    // Add info window for the marker
    const infoWindow = new google.maps.InfoWindow({
        content: '<div style="padding: 10px;"><strong>Cresences Bakery</strong><br>Where Every Crumb Tells a Story<br>Open Mon-Fri: 8AM-5PM, Weekends: 8AM-4PM</div>'
    });

    // Show info window when marker is clicked
    marker.addListener('click', function() {
        infoWindow.open(map, marker);
    });
}