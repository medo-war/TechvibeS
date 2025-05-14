/**
 * Partner form validation for back-office
 * Provides real-time validation and error messages
 */
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form validation with real-time feedback
    const partnerEditForm = document.getElementById('partnerEditForm');
    
    // Add real-time validation for key fields
    const validateableFields = ['name', 'company', 'email', 'phone', 'partnerType', 'partnershipValue', 'contractStart', 'contractEnd', 'contract_template_id'];
    
    validateableFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', function() {
                validateField(field);
            });
            
            // For select elements, also validate on change
            if (field.tagName === 'SELECT') {
                field.addEventListener('change', function() {
                    validateField(field);
                });
            }
        }
    });
    
    // Validate a single field
    function validateField(field) {
        // Remove any existing error messages
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }
        field.classList.remove('is-invalid');
        field.classList.remove('is-valid');
        
        let isValid = true;
        const fieldId = field.id;
        
        switch(fieldId) {
            case 'name':
                if (!field.value.trim()) {
                    showError(field, 'Please enter partner name');
                    isValid = false;
                } else if (field.value.trim().length < 2) {
                    showError(field, 'Name must be at least 2 characters');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                }
                break;
                
            case 'company':
                if (!field.value.trim()) {
                    showError(field, 'Please enter company name');
                    isValid = false;
                } else if (field.value.trim().length < 2) {
                    showError(field, 'Company name must be at least 2 characters');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                }
                break;
                
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!field.value.trim()) {
                    showError(field, 'Please enter email address');
                    isValid = false;
                } else if (!emailRegex.test(field.value.trim())) {
                    showError(field, 'Please enter a valid email address');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                }
                break;
                
            case 'phone':
                const phoneRegex = /^[0-9\+\-\s\(\)]{7,20}$/;
                if (!field.value.trim()) {
                    showError(field, 'Please enter phone number');
                    isValid = false;
                } else if (!phoneRegex.test(field.value.trim())) {
                    showError(field, 'Please enter a valid phone number');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                }
                break;
                
            case 'partnerType':
                if (!field.value) {
                    showError(field, 'Please select a partnership type');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                }
                break;
                
            case 'contract_template_id':
                if (!field.value) {
                    showError(field, 'Please select a contract template');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                }
                break;
                
            case 'partnershipValue':
                if (!field.value || isNaN(field.value) || parseFloat(field.value) <= 0) {
                    showError(field, 'Please enter a valid partnership value');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                }
                break;
                
            case 'contractStart':
                if (!field.value) {
                    showError(field, 'Please select a start date');
                    isValid = false;
                } else {
                    field.classList.add('is-valid');
                    
                    // If end date is set, validate it as well
                    const endDateField = document.getElementById('contractEnd');
                    if (endDateField && endDateField.value) {
                        validateField(endDateField);
                    }
                }
                break;
                
            case 'contractEnd':
                if (!field.value) {
                    showError(field, 'Please select an end date');
                    isValid = false;
                } else {
                    const endDate = new Date(field.value);
                    const startDateField = document.getElementById('contractStart');
                    
                    if (startDateField && startDateField.value) {
                        const startDate = new Date(startDateField.value);
                        if (endDate <= startDate) {
                            showError(field, 'End date must be after start date');
                            isValid = false;
                        } else {
                            field.classList.add('is-valid');
                        }
                    } else {
                        field.classList.add('is-valid');
                    }
                }
                break;
        }
        
        return isValid;
    }
    
    if (partnerEditForm) {
        partnerEditForm.addEventListener('submit', function(event) {
            // Prevent default form submission - we'll handle it manually
            event.preventDefault();
            
            // Reset all validation states
            document.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.is-valid').forEach(el => el.classList.remove('is-valid'));
            
            // Validate all fields
            let isValid = true;
            
            // Validate each field using our validateField function
            validateableFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                }
            });
            
            // If all validations pass, submit the form
            if (isValid) {
                // Remove any existing error alert
                const existingAlert = document.getElementById('validationErrorAlert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Call the savePartnerChanges function
                const partnerId = partnerEditForm.querySelector('input[name="partnerId"]').value;
                savePartnerChanges(partnerId);
            } else {
                // Create and show error message at the top of the form
                const existingAlert = document.getElementById('validationErrorAlert');
                if (!existingAlert) {
                    const errorAlert = document.createElement('div');
                    errorAlert.id = 'validationErrorAlert';
                    errorAlert.className = 'alert alert-danger';
                    errorAlert.style.marginBottom = '20px';
                    errorAlert.style.animation = 'shake 0.5s';
                    errorAlert.innerHTML = `
                        <strong><i class="fas fa-exclamation-circle"></i> Please correct the following errors:</strong>
                        <ul class="error-list mb-0 mt-2"></ul>
                    `;
                    
                    // Add a style for the shake animation if it doesn't exist
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
                    partnerEditForm.insertBefore(errorAlert, partnerEditForm.firstChild);
                    
                    // Populate error list
                    const errorList = errorAlert.querySelector('.error-list');
                    document.querySelectorAll('.invalid-feedback').forEach(error => {
                        const field = error.previousElementSibling;
                        const fieldLabel = field.previousElementSibling ? field.previousElementSibling.textContent : 'Field';
                        const li = document.createElement('li');
                        li.textContent = `${fieldLabel}: ${error.textContent}`;
                        errorList.appendChild(li);
                    });
                    
                    // Scroll to the error message
                    errorAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    // Update existing error list
                    const errorList = existingAlert.querySelector('.error-list');
                    errorList.innerHTML = '';
                    document.querySelectorAll('.invalid-feedback').forEach(error => {
                        const field = error.previousElementSibling;
                        const fieldLabel = field.previousElementSibling ? field.previousElementSibling.textContent : 'Field';
                        const li = document.createElement('li');
                        li.textContent = `${fieldLabel}: ${error.textContent}`;
                        errorList.appendChild(li);
                    });
                    
                    // Refresh the animation
                    existingAlert.style.animation = 'none';
                    setTimeout(() => existingAlert.style.animation = 'shake 0.5s', 10);
                    
                    // Scroll to the error message
                    existingAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Also focus the first invalid field
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.focus();
                }
            }
        });
    }
    
    // Helper function to show error messages
    function showError(input, message) {
        input.classList.add('is-invalid');
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        input.parentNode.appendChild(errorDiv);
    }
});
