// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const navLinks = document.getElementById('navLinks');
    
    if (mobileMenuBtn && navLinks) {
        mobileMenuBtn.addEventListener('click', () => navLinks.classList.toggle('active'));
        navLinks.querySelectorAll('a').forEach(item => {
            item.addEventListener('click', () => navLinks.classList.remove('active'));
        });
    }

    // Smooth scrolling
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;
            const target = document.querySelector(targetId);
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });

    // Form submission
    const form = document.getElementById('enquiryForm');
    const formMsg = document.getElementById('formMessage');
    
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                name: form.querySelector('#name').value.trim(),
                email: form.querySelector('#email').value.trim(),
                phone: form.querySelector('#phone').value.trim(),
                program: form.querySelector('#program').value,
                message: form.querySelector('#message').value.trim(),
                timestamp: new Date().toISOString()
            };
            
            // Basic validation
            if (!formData.name || !formData.email || !formData.phone || !formData.program) {
                showMessage('Please fill in all required fields.', 'error');
                return;
            }
            
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
                showMessage('Please enter a valid email address.', 'error');
                return;
            }
            
            try {
                const response = await fetch('submit-form.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message || 'Thank you for your enquiry! We will contact you soon.', 'success');
                    form.reset();
                } else {
                    showMessage(result.message || 'Error submitting form. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Network error. Please check your connection and try again.', 'error');
            }
        });
    }
    
    function showMessage(msg, type) {
        if (!formMsg) return;
        formMsg.textContent = msg;
        formMsg.className = `form-message ${type}`;
        formMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => formMsg.textContent = '', 5000);
    }
});
