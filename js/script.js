/**
 * BOONCHUAY GYM - JAVASCRIPT
 * Handles navigation, form validation, and chatbot UI
 */

// ============================================
// NAVIGATION - Mobile Menu Toggle
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    const navLinks = document.querySelectorAll('.nav-link');

    // Toggle mobile menu
    hamburger.addEventListener('click', function () {
        navMenu.classList.toggle('active');

        // Animate hamburger icon
        const spans = hamburger.querySelectorAll('span');
        spans[0].style.transform = navMenu.classList.contains('active') ? 'rotate(45deg) translate(5px, 5px)' : 'none';
        spans[1].style.opacity = navMenu.classList.contains('active') ? '0' : '1';
        spans[2].style.transform = navMenu.classList.contains('active') ? 'rotate(-45deg) translate(7px, -6px)' : 'none';
    });

    // Close menu when clicking on a link
    navLinks.forEach(link => {
        link.addEventListener('click', function () {
            navMenu.classList.remove('active');
            const spans = hamburger.querySelectorAll('span');
            spans[0].style.transform = 'none';
            spans[1].style.opacity = '1';
            spans[2].style.transform = 'none';
        });
    });

    // Change navbar background on scroll
    window.addEventListener('scroll', function () {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 100) {
            navbar.style.background = 'rgba(10, 10, 10, 1)';
        } else {
            navbar.style.background = 'rgba(10, 10, 10, 0.95)';
        }
    });
});

// ============================================
// CONTACT FORM VALIDATION
// ============================================
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Get form values
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const message = document.getElementById('message').value.trim();

        let isValid = true;

        // Validate name (minimum 3 characters)
        if (name.length < 3) {
            showError('nameError', 'El nombre debe tener al menos 3 caracteres');
            isValid = false;
        }

        // Validate email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showError('emailError', 'Por favor, introduce un email válido');
            isValid = false;
        }

        // Validate phone (Spanish format - basic validation)
        const phoneRegex = /^[0-9]{9,15}$/;
        const cleanPhone = phone.replace(/\s+/g, '');
        if (!phoneRegex.test(cleanPhone)) {
            showError('phoneError', 'Por favor, introduce un teléfono válido (9-15 dígitos)');
            isValid = false;
        }

        // Validate message (minimum 10 characters)
        if (message.length < 10) {
            showError('messageError', 'El mensaje debe tener al menos 10 caracteres');
            isValid = false;
        }

        // If all validations pass, submit the form
        if (isValid) {
            submitForm();
        }
    });
}

/**
 * Show error message for a specific field
 */
function showError(errorId, message) {
    const errorElement = document.getElementById(errorId);
    errorElement.textContent = message;
    errorElement.style.display = 'block';

    // Add red border to input
    const inputId = errorId.replace('Error', '');
    const inputElement = document.getElementById(inputId);
    inputElement.style.borderColor = '#dc143c';
}

/**
 * Clear all error messages
 */
function clearErrors() {
    const errorMessages = document.querySelectorAll('.error-message');
    errorMessages.forEach(error => {
        error.style.display = 'none';
        error.textContent = '';
    });

    // Reset input borders
    const inputs = document.querySelectorAll('.form-group input, .form-group textarea');
    inputs.forEach(input => {
        input.style.borderColor = 'transparent';
    });
}

/**
 * Submit form via AJAX to PHP handler
 */
function submitForm() {
    const formData = new FormData(contactForm);

    // Show loading state (optional - you can add a loading spinner)
    const submitButton = contactForm.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Enviando...';
    submitButton.disabled = true;

    // Send form data to PHP
    fetch('send.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                document.getElementById('formSuccess').style.display = 'block';
                contactForm.reset();

                // Hide success message after 5 seconds
                setTimeout(() => {
                    document.getElementById('formSuccess').style.display = 'none';
                }, 5000);
            } else {
                alert('Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Hubo un error al enviar el mensaje. Por favor, inténtalo de nuevo.');
        })
        .finally(() => {
            // Reset button state
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
}

// ============================================
// CHATBOT UI
// ============================================
const chatbotButton = document.getElementById('chatbotButton');
const chatbotModal = document.getElementById('chatbotModal');
const chatbotClose = document.getElementById('chatbotClose');
const chatbotSend = document.getElementById('chatbotSend');
const chatbotInput = document.getElementById('chatbotInput');
const chatbotBody = document.getElementById('chatbotBody');

// Toggle chatbot modal
chatbotButton.addEventListener('click', function () {
    chatbotModal.classList.toggle('active');
});

// Close chatbot
chatbotClose.addEventListener('click', function () {
    chatbotModal.classList.remove('active');
});

// Send message in chatbot
chatbotSend.addEventListener('click', sendChatMessage);

// Send message on Enter key
chatbotInput.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        sendChatMessage();
    }
});

/**
 * Send chat message to backend
 * Integrates with chatbot.php for database-driven responses
 */
function sendChatMessage() {
    const message = chatbotInput.value.trim();

    if (message === '') return;

    // Add user message to chat
    addChatMessage(message, 'user');

    // Clear input
    chatbotInput.value = '';

    // Disable input while waiting for response
    chatbotInput.disabled = true;
    chatbotSend.disabled = true;

    // Show typing indicator
    const typingIndicator = addTypingIndicator();

    // Send message to backend via AJAX
    fetch('chatbot.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            message: message
        })
    })
        .then(response => response.json())
        .then(data => {
            // Remove typing indicator
            typingIndicator.remove();

            if (data.success && data.bot_response) {
                // Add bot response to chat
                addChatMessage(data.bot_response, 'bot');
            } else {
                // Fallback response if backend fails
                addChatMessage('Lo siento, hubo un error. Por favor, inténtalo de nuevo o contacta con nosotros al 931 70 98 45.', 'bot');
            }
        })
        .catch(error => {
            console.error('Chatbot error:', error);
            // Remove typing indicator
            typingIndicator.remove();
            // Show error message
            addChatMessage('Lo siento, no puedo conectar con el servidor. Por favor, inténtalo más tarde.', 'bot');
        })
        .finally(() => {
            // Re-enable input
            chatbotInput.disabled = false;
            chatbotSend.disabled = false;
            chatbotInput.focus();
        });
}

/**
 * Add message to chat body
 */
function addChatMessage(message, sender) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('chatbot-message');
    messageDiv.classList.add(sender === 'user' ? 'user-message' : 'bot-message');

    const messageText = document.createElement('p');
    messageText.textContent = message;
    messageDiv.appendChild(messageText);

    chatbotBody.appendChild(messageDiv);

    // Scroll to bottom
    chatbotBody.scrollTop = chatbotBody.scrollHeight;

    return messageDiv;
}

/**
 * Add typing indicator while waiting for bot response
 */
function addTypingIndicator() {
    const typingDiv = document.createElement('div');
    typingDiv.classList.add('chatbot-message', 'bot-message', 'typing-indicator');
    typingDiv.innerHTML = '<p>...</p>';
    chatbotBody.appendChild(typingDiv);
    chatbotBody.scrollTop = chatbotBody.scrollHeight;
    return typingDiv;
}

// ============================================
// LEGACY FUNCTION (kept for reference)
// This was the old hardcoded response system
// Now replaced with backend integration above
// ============================================
/**
 * Get bot response based on user message (DEPRECATED - now using backend)
 * This function is kept for reference or as fallback
 * The actual responses now come from chatbot.php which queries the FAQ database
 */
function getBotResponseLegacy(message) {
    const lowerMessage = message.toLowerCase();

    // Keyword-based responses (fallback only)
    if (lowerMessage.includes('horario') || lowerMessage.includes('hora')) {
        return 'Nuestro horario es de Lunes a Viernes de 9:00 a 22:00. Los fines de semana estamos cerrados.';
    } else if (lowerMessage.includes('precio') || lowerMessage.includes('cuota') || lowerMessage.includes('coste')) {
        return 'Para información sobre precios y cuotas, por favor contacta con nosotros al 931 70 98 45 o envía un mensaje a través del formulario de contacto.';
    } else if (lowerMessage.includes('ubicación') || lowerMessage.includes('dirección') || lowerMessage.includes('donde')) {
        return 'Estamos ubicados en Carrer d\'Eusebi Güell 14, 08830 Sant Boi de Llobregat.';
    } else if (lowerMessage.includes('disciplina') || lowerMessage.includes('clase')) {
        return 'Ofrecemos clases de Muay Thai, Boxeo, Jeet Kune Do y Kali. ¿En cuál estás interesado?';
    } else if (lowerMessage.includes('muay thai')) {
        return 'El Muay Thai es el arte de las ocho extremidades. Tenemos clases para todos los niveles. ¿Te gustaría probar una clase gratis?';
    } else if (lowerMessage.includes('boxeo')) {
        return 'Nuestras clases de boxeo están diseñadas para mejorar tu técnica, velocidad y condición física. ¿Quieres más información?';
    } else if (lowerMessage.includes('jkd') || lowerMessage.includes('jeet kune do')) {
        return 'El Jeet Kune Do es un sistema de combate directo y eficiente creado por Bruce Lee. Perfecto para defensa personal.';
    } else if (lowerMessage.includes('kali') || lowerMessage.includes('eskrima')) {
        return 'El Kali es un arte marcial filipino que enfatiza el combate con armas y mano vacía. Muy efectivo para coordinación y defensa.';
    } else if (lowerMessage.includes('niño') || lowerMessage.includes('infantil') || lowerMessage.includes('hijo')) {
        return 'Sí, tenemos clases para niños en un ambiente seguro y familiar. Contacta con nosotros para más detalles.';
    } else if (lowerMessage.includes('prueba') || lowerMessage.includes('gratis') || lowerMessage.includes('primera clase')) {
        return '¡Genial! Ofrecemos una clase de prueba gratuita. Rellena el formulario de contacto o llámanos al 931 70 98 45 para reservar tu plaza.';
    } else if (lowerMessage.includes('hola') || lowerMessage.includes('buenos') || lowerMessage.includes('buenas')) {
        return '¡Hola! ¿En qué puedo ayudarte hoy? Puedo informarte sobre horarios, disciplinas, ubicación y más.';
    } else if (lowerMessage.includes('gracias')) {
        return '¡De nada! ¿Hay algo más en lo que pueda ayudarte?';
    } else {
        return 'Gracias por tu mensaje. Para información más específica, por favor contacta con nosotros al 931 70 98 45 o a través del formulario de contacto.';
    }
}

// ============================================
// SMOOTH SCROLL FOR NAVIGATION LINKS
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offsetTop = target.offsetTop - 70; // Account for fixed navbar
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// ============================================
// SCROLL ANIMATIONS (Optional Enhancement)
// ============================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver(function (entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe discipline cards for scroll animation
document.querySelectorAll('.discipline-card').forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(card);
});
