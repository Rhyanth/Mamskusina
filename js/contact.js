// Contact formulier validatie en verzending
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Haal waarden op
    const name = document.getElementById('name').value;
    const phone = document.getElementById('phone').value;
    const email = document.getElementById('email').value;
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('message').value;
    
    // Email validatie
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showAlert('Voer een geldig e-mailadres in', 'danger');
        return;
    }
    
    // Telefoon validatie (Nederlands)
    const phoneRegex = /^(\+31|0)[0-9]{9}$/;
    const cleanPhone = phone.replace(/[\s-]/g, '');
    if (!phoneRegex.test(cleanPhone)) {
        showAlert('Voer een geldig Nederlands telefoonnummer in (bijv. 06-12345678)', 'danger');
        return;
    }
    
    // Toon loading status
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Versturen...';
    
    // Maak FormData object
    const formData = new FormData();
    formData.append('name', name);
    formData.append('phone', phone);
    formData.append('email', email);
    formData.append('subject', subject);
    formData.append('message', message);
    
    // Verstuur naar PHP backend
    fetch('https://mamskusina.com/wp-content/backend/send-email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name,
            email,
            phone,
            subject,
            message
        })
    })
    .then(response => response.json())
    .then(data => {
        // Reset knop
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        
        if (data.success) {
            showAlert('✅ Bedankt voor je bericht! We nemen zo snel mogelijk contact met je op.', 'success');
            // Reset formulier
            document.getElementById('contactForm').reset();
            
            // Scroll naar melding
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            showAlert('❌ ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        showAlert('❌ Er is iets misgegaan. Probeer het later opnieuw.', 'danger');
    });
});

// Functie om Bootstrap alert te tonen
function showAlert(message, type) {
    // Verwijder oude alerts
    const oldAlerts = document.querySelectorAll('.alert-notification');
    oldAlerts.forEach(alert => alert.remove());
    
    // Maak nieuwe alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show alert-notification`;
    alertDiv.setAttribute('role', 'alert');
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Voeg toe bovenaan de container
    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto-hide na 5 seconden (alleen voor success)
    if (type === 'success') {
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}