/**
 * Bakery Chatbot - Handles interaction with OpenAI API
 */
document.addEventListener('DOMContentLoaded', function() {
    const chatInput = document.getElementById('chat-input');
    const sendButton = document.getElementById('send-button');
    const chatContainer = document.getElementById('chat-container');
    
    // Add event listeners
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
    
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;
        
        // Clear input
        chatInput.value = '';
        
        // Add user message
        addMessage(message, 'user');
        
        // Show typing indicator
        const typingIndicator = addTypingIndicator();
        
        // Get AI response
        getAIResponse(message, typingIndicator);
    }
    
    function addMessage(message, sender) {
        const div = document.createElement('div');
        div.className = `chat-message ${sender}-message`;
        
        const p = document.createElement('p');
        p.className = 'mb-0';
        p.textContent = message;
        
        div.appendChild(p);
        chatContainer.appendChild(div);
        
        // Scroll to bottom
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
    
    function addTypingIndicator() {
        const div = document.createElement('div');
        div.className = 'chat-message bot-message typing-indicator';
        
        for (let i = 0; i < 3; i++) {
            const dot = document.createElement('div');
            dot.className = 'typing-dot';
            div.appendChild(dot);
        }
        
        chatContainer.appendChild(div);
        chatContainer.scrollTop = chatContainer.scrollHeight;
        
        return div;
    }
    
    function typeMessage(message) {
        const div = document.createElement('div');
        div.className = 'chat-message bot-message';
        
        const p = document.createElement('p');
        p.className = 'mb-0';
        div.appendChild(p);
        
        chatContainer.appendChild(div);
        
        // Type message character by character
        let i = 0;
        const interval = setInterval(() => {
            if (i < message.length) {
                p.textContent += message.charAt(i);
                i++;
                chatContainer.scrollTop = chatContainer.scrollHeight;
            } else {
                clearInterval(interval);
            }
        }, 20);
    }
    
    function getAIResponse(message, typingIndicator) {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('/chatbot/recommendation', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                message: message
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('API request failed');
            }
            return response.json();
        })
        .then(data => {
            // Remove typing indicator
            if (typingIndicator && typingIndicator.parentNode === chatContainer) {
                chatContainer.removeChild(typingIndicator);
            }
            
            // Display response
            if (data.success) {
                typeMessage(data.message);
            } else {
                addMessage('Sorry, I encountered an issue. Please try again later.', 'bot');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            
            // Remove typing indicator
            if (typingIndicator && typingIndicator.parentNode === chatContainer) {
                chatContainer.removeChild(typingIndicator);
            }
            
            // Show error message
            addMessage('Sorry, there was a problem connecting to the recommendation service. Please try again later.', 'bot');
        });
    }
});