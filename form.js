document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    const formMessage = document.getElementById('formMessage');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous message
            formMessage.textContent = '';
            formMessage.className = 'form-message';
            
            // Disable submit button
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            
            // Show loading message
            formMessage.textContent = 'Envoi en cours...';
            formMessage.className = 'form-message info';
            
            // Create FormData
            const formData = new FormData(form);
            
            // Send request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'process.php', true);
            
            xhr.onload = function() {
                submitButton.disabled = false;
                
                try {
                    // Try to parse response
                    const response = JSON.parse(xhr.responseText);
                    
                    // Update message
                    formMessage.textContent = response.message;
                    formMessage.className = 'form-message ' + (response.success ? 'success' : 'error');
                    
                    // If successful, reset form
                    if (response.success) {
                        form.reset();
                    }
                } catch (e) {
                    // Log the actual response for debugging
                    console.error('Server response:', xhr.responseText);
                    formMessage.textContent = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
                    formMessage.className = 'form-message error';
                }
            };
            
            xhr.onerror = function() {
                submitButton.disabled = false;
                formMessage.textContent = 'Erreur de connexion. Veuillez réessayer.';
                formMessage.className = 'form-message error';
            };
            
            // Send the form
            xhr.send(formData);
        });
    }
});
