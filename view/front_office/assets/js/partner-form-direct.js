/**
 * Direct Partner Form Validation
 * Simple and reliable form validation for the partner form
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Partner form direct validation loaded');
    
    // Get the partner form
    const partnerForm = document.getElementById('partnerForm');
    
    if (partnerForm) {
        console.log('Partner form found');
        
        // Add form submission handler
        partnerForm.addEventListener('submit', function(event) {
            // Prevent default form submission
            event.preventDefault();
            
            // Remove any existing error messages
            const existingAlert = document.getElementById('formErrorAlert');
            if (existingAlert) {
                existingAlert.remove();
            }
            
            // Validate form
            const errors = [];
            
            // Required fields to validate
            const requiredFields = [
                { id: 'name', label: 'Name' },
                { id: 'company', label: 'Company' },
                { id: 'email', label: 'Email' },
                { id: 'phone', label: 'Phone' },
                { id: 'partnerType', label: 'Partnership Type' },
                { id: 'partnershipValue', label: 'Partnership Value' },
                { id: 'contractStart', label: 'Contract Start Date' },
                { id: 'contractEnd', label: 'Contract End Date' },
                { id: 'contract_template_id', label: 'Contract Template' }
            ];
            
            // Check each required field
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (element && (element.value === '' || element.value === null)) {
                    errors.push(`${field.label} is required`);
                    element.classList.add('is-invalid');
                } else if (element) {
                    element.classList.remove('is-invalid');
                    element.classList.add('is-valid');
                }
            });
            
            // Validate email format
            const emailInput = document.getElementById('email');
            if (emailInput && emailInput.value !== '') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value)) {
                    errors.push('Please enter a valid email address');
                    emailInput.classList.add('is-invalid');
                }
            }
            
            // Validate partnership value
            const valueInput = document.getElementById('partnershipValue');
            if (valueInput && valueInput.value !== '') {
                if (isNaN(valueInput.value) || parseFloat(valueInput.value) <= 0) {
                    errors.push('Partnership value must be a positive number');
                    valueInput.classList.add('is-invalid');
                }
            }
            
            // Validate contract dates
            const startDateInput = document.getElementById('contractStart');
            const endDateInput = document.getElementById('contractEnd');
            if (startDateInput && endDateInput && 
                startDateInput.value !== '' && endDateInput.value !== '') {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                
                if (endDate <= startDate) {
                    errors.push('Contract end date must be after start date');
                    endDateInput.classList.add('is-invalid');
                }
            }
            
            // Validate terms agreement
            const termsAgree = document.getElementById('termsAgree');
            if (termsAgree && !termsAgree.checked) {
                errors.push('You must agree to the terms and conditions');
                termsAgree.classList.add('is-invalid');
            } else if (termsAgree) {
                termsAgree.classList.remove('is-invalid');
            }
            
            // If there are errors, show error message
            if (errors.length > 0) {
                // Create error alert
                const errorAlert = document.createElement('div');
                errorAlert.id = 'formErrorAlert';
                errorAlert.className = 'alert alert-danger';
                errorAlert.style.marginBottom = '20px';
                
                // Add shake animation
                errorAlert.style.animation = 'shake 0.5s';
                
                // Add shake animation style if it doesn't exist
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
                
                // Create error message content
                let errorContent = '<strong><i class="fas fa-exclamation-circle"></i> Please correct the following errors:</strong>';
                errorContent += '<ul class="mb-0 mt-2">';
                errors.forEach(error => {
                    errorContent += `<li>${error}</li>`;
                });
                errorContent += '</ul>';
                
                errorAlert.innerHTML = errorContent;
                
                // Insert at the top of the form
                partnerForm.insertBefore(errorAlert, partnerForm.firstChild);
                
                // Scroll to the error message
                errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                return false;
            }
            
            // If validation passes, submit the form
            console.log('Form validation passed, submitting form');
            partnerForm.submit();
        });
    }
});
