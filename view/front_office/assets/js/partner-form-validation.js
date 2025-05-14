/**
 * Front-office Partner Form Validation
 * Provides real-time validation and error messages for the partner form
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Partner form validation script loaded');
    
    // Get the partner form
    const partnerForm = document.getElementById('partnerForm');
    
    if (partnerForm) {
        console.log('Partner form found, setting up validation');
        
        // Form submission handler
        partnerForm.addEventListener('submit', function(event) {
            // Prevent default form submission
            event.preventDefault();
            
            // Reset validation states
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            // Validate all required fields
            let isValid = true;
            let errorMessages = [];
            
            // Name validation
            const nameInput = document.getElementById('name');
            if (!nameInput.value.trim()) {
                showError(nameInput, 'Please enter your name');
                errorMessages.push('Name is required');
                isValid = false;
            }
            
            // Company validation
            const companyInput = document.getElementById('company');
            if (!companyInput.value.trim()) {
                showError(companyInput, 'Please enter your company name');
                errorMessages.push('Company name is required');
                isValid = false;
            }
            
            // Email validation
            const emailInput = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailInput.value.trim() || !emailRegex.test(emailInput.value.trim())) {
                showError(emailInput, 'Please enter a valid email address');
                errorMessages.push('Valid email address is required');
                isValid = false;
            }
            
            // Phone validation
            const phoneInput = document.getElementById('phone');
            if (!phoneInput.value.trim()) {
                showError(phoneInput, 'Please enter your phone number');
                errorMessages.push('Phone number is required');
                isValid = false;
            }
            
            // Partner type validation
            const partnerTypeInput = document.getElementById('partnerType');
            if (!partnerTypeInput.value) {
                showError(partnerTypeInput, 'Please select a partnership type');
                errorMessages.push('Partnership type is required');
                isValid = false;
            }
            
            // Contract template validation
            const contractTemplateInput = document.getElementById('contract_template_id');
            if (contractTemplateInput && !contractTemplateInput.value) {
                showError(contractTemplateInput, 'Please select a contract template');
                errorMessages.push('Contract template is required');
                isValid = false;
            }
            
            // Partnership value validation
            const partnershipValueInput = document.getElementById('partnershipValue');
            if (!partnershipValueInput.value || isNaN(partnershipValueInput.value) || parseFloat(partnershipValueInput.value) <= 0) {
                showError(partnershipValueInput, 'Please enter a valid partnership value');
                errorMessages.push('Valid partnership value is required');
                isValid = false;
            }
            
            // Contract start date validation
            const contractStartInput = document.getElementById('contractStart');
            if (!contractStartInput.value) {
                showError(contractStartInput, 'Please select a start date');
                errorMessages.push('Contract start date is required');
                isValid = false;
            }
            
            // Contract end date validation
            const contractEndInput = document.getElementById('contractEnd');
            if (!contractEndInput.value) {
                showError(contractEndInput, 'Please select an end date');
                errorMessages.push('Contract end date is required');
                isValid = false;
            }
            
            // Terms agreement validation
            const termsAgreeInput = document.getElementById('termsAgree');
            if (!termsAgreeInput.checked) {
                showError(termsAgreeInput, 'You must agree to the terms and conditions');
                errorMessages.push('Agreement to terms and conditions is required');
                isValid = false;
            }
            
            // If validation fails, show error message
            if (!isValid) {
                // Create error alert
                showErrorAlert(errorMessages);
                return false;
            }
            
            // If validation passes, submit the form
            console.log('Form validation passed, submitting...');
            partnerForm.submit();
        });
        
        // Helper function to show error for a field
        function showError(input, message) {
            input.classList.add('is-invalid');
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = message;
            input.parentNode.appendChild(errorDiv);
        }
        
        // Helper function to show error alert
        function showErrorAlert(messages) {
            // Remove any existing error alert
            const existingAlert = document.getElementById('validationErrorAlert');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            // Create new error alert
            const errorAlert = document.createElement('div');
            errorAlert.id = 'validationErrorAlert';
            errorAlert.className = 'alert alert-danger';
            errorAlert.style.marginBottom = '20px';
            errorAlert.style.animation = 'shake 0.5s';
            
            // Create error message content
            let errorContent = '<strong><i class="fas fa-exclamation-circle"></i> Please correct the following errors:</strong>';
            errorContent += '<ul class="mb-0 mt-2">';
            messages.forEach(message => {
                errorContent += `<li>${message}</li>`;
            });
            errorContent += '</ul>';
            
            errorAlert.innerHTML = errorContent;
            
            // Add shake animation style
            if (!document.getElementById('shakeAnimationStyle')) {
                const styleElement = document.createElement('style');
                styleElement.id = 'shakeAnimationStyle';
                styleElement.textContent = `
                    @keyframes shake {
                        0%, 100% { transform: translateX(0); }
                        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                        20%, 40%, 60%, 80% { transform: translateX(5px); }
                    }
                `;
                document.head.appendChild(styleElement);
            }
            
            // Insert at the top of the form
            partnerForm.insertBefore(errorAlert, partnerForm.firstChild);
            
            // Scroll to the error message
            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});
