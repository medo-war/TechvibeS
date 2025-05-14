/**
 * Enhanced Partner Form Validation
 * Provides real-time validation and error messages for the partner form
 */
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form validation with real-time feedback
    const partnerForm = document.getElementById('partnerForm');
    
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
                    showError(field, 'Please enter your name');
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
                    showError(field, 'Please enter your company name');
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
                    showError(field, 'Please enter your email address');
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
                    showError(field, 'Please enter your phone number');
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
                    const startDate = new Date(field.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (startDate < today) {
                        showError(field, 'Start date cannot be in the past');
                        isValid = false;
                    } else {
                        field.classList.add('is-valid');
                        
                        // If end date is set, validate it as well
                        const endDateField = document.getElementById('contractEnd');
                        if (endDateField && endDateField.value) {
                            validateField(endDateField);
                        }
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
    
    if (partnerForm) {
        partnerForm.addEventListener('submit', function(event) {
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
            
            // Validate terms agreement
            const termsAgree = document.getElementById('termsAgree');
            if (termsAgree && !termsAgree.checked) {
                isValid = false;
                showError(termsAgree, 'You must agree to the terms and conditions');
            }
            
            // If all validations pass, submit the form
            if (isValid) {
                // Remove any existing error alert
                const existingAlert = document.getElementById('validationErrorAlert');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                // Show loading indicator
                const submitBtn = partnerForm.querySelector('button[type="submit"]');
                const originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';
                submitBtn.disabled = true;
                
                // Submit the form
                partnerForm.submit();
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
                    partnerForm.insertBefore(errorAlert, partnerForm.firstChild);
                    
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
            
            // Prevent form submission if validation fails
            if (!isValid) {
                event.preventDefault();
            }
        });
    }
    
    // Contract template view button
    const viewFullContractBtn = document.getElementById('viewFullContractBtn');
    const contractTemplateSelect = document.getElementById('contract_template_id');
    
    if (viewFullContractBtn && contractTemplateSelect) {
        viewFullContractBtn.addEventListener('click', function() {
            const selectedOption = contractTemplateSelect.options[contractTemplateSelect.selectedIndex];
            
            if (selectedOption && selectedOption.value) {
                // Get contract data from the selected option's data attributes
                const contractName = selectedOption.text;
                const contractDuration = selectedOption.dataset.duration || '12';
                const contractPriceMin = selectedOption.dataset.priceMin || '0';
                const contractPriceMax = selectedOption.dataset.priceMax || '0';
                const contractDescription = selectedOption.dataset.description || 'No description available.';
                const contractBenefits = selectedOption.dataset.benefits || 'No benefits specified.';
                const contractTerms = selectedOption.dataset.terms || 'No terms specified.';
                
                // Populate the modal
                document.getElementById('contract-modal-name').textContent = contractName;
                document.getElementById('contract-modal-duration').textContent = contractDuration + ' months';
                
                // Set price range or single price
                if (contractPriceMin && contractPriceMax && contractPriceMin !== contractPriceMax) {
                    document.getElementById('contract-modal-value').textContent = 
                        parseFloat(contractPriceMin).toFixed(2) + ' - ' + parseFloat(contractPriceMax).toFixed(2);
                } else {
                    document.getElementById('contract-modal-value').textContent = parseFloat(contractPriceMin).toFixed(2);
                }
                
                // Set contract content
                document.getElementById('contract-modal-description').textContent = contractDescription;
                document.getElementById('contract-modal-benefits').textContent = contractBenefits;
                document.getElementById('contract-modal-terms').textContent = contractTerms;
                
                // Show the modal
                const contractModal = new bootstrap.Modal(document.getElementById('contractViewModal'));
                contractModal.show();
            } else {
                alert('Please select a contract template first.');
            }
        });
    }
    
    // Contract template change handler
    if (contractTemplateSelect) {
        contractTemplateSelect.addEventListener('change', function() {
            const contractDetails = document.getElementById('contractDetails');
            const templateName = document.getElementById('templateName');
            const templateDescription = document.getElementById('templateDescription');
            const templateBenefits = document.getElementById('templateBenefits');
            const templateTerms = document.getElementById('templateTerms');
            const templatePriceMin = document.getElementById('templatePriceMin');
            const templatePriceMax = document.getElementById('templatePriceMax');
            const partnershipValueInput = document.getElementById('partnershipValue');
            const contractStartInput = document.getElementById('contractStart');
            const contractEndInput = document.getElementById('contractEnd');
            
            if (this.value) {
                const selectedOption = this.options[this.selectedIndex];
                const duration = selectedOption.dataset.duration;
                const priceMin = selectedOption.dataset.priceMin;
                const priceMax = selectedOption.dataset.priceMax;
                const description = selectedOption.dataset.description;
                const benefits = selectedOption.dataset.benefits;
                const terms = selectedOption.dataset.terms;
                
                // Show contract details
                templateName.textContent = selectedOption.text;
                templateDescription.textContent = description;
                templateBenefits.textContent = benefits;
                templateTerms.textContent = terms;
                templatePriceMin.textContent = parseFloat(priceMin).toFixed(2);
                templatePriceMax.textContent = parseFloat(priceMax).toFixed(2);
                contractDetails.style.display = 'block';
                
                // Set suggested partnership value (average of min and max)
                const suggestedValue = ((parseFloat(priceMin) + parseFloat(priceMax)) / 2).toFixed(2);
                partnershipValueInput.value = suggestedValue;
                
                // Set contract dates based on duration
                if (contractStartInput && contractEndInput && duration) {
                    const today = new Date();
                    const endDate = new Date();
                    endDate.setMonth(today.getMonth() + parseInt(duration));
                    
                    contractStartInput.value = today.toISOString().split('T')[0];
                    contractEndInput.value = endDate.toISOString().split('T')[0];
                }
            } else {
                // Hide contract details if no template selected
                contractDetails.style.display = 'none';
            }
        });
    }
    
    // Print contract functionality
    const frontPrintContractBtn = document.getElementById('frontPrintContractBtn');
    if (frontPrintContractBtn) {
        frontPrintContractBtn.addEventListener('click', function() {
            printFrontContract();
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
    
    // Print contract function
    function printFrontContract() {
        const printContents = document.getElementById('frontContractPrintArea').innerHTML;
        
        // Create a new window with just the contract content
        const printWindow = window.open('', '_blank');
        
        // Add print-friendly styles
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>LiveTheMusic - Partnership Contract</title>
                <style>
                    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
                    
                    body {
                        font-family: 'Poppins', sans-serif;
                        color: #000;
                        background: #fff;
                        padding: 20px;
                        line-height: 1.6;
                    }
                    
                    .contract-header {
                        text-align: center;
                        margin-bottom: 30px;
                        border-bottom: 2px solid #333;
                        padding-bottom: 20px;
                    }
                    
                    .contract-header h3 {
                        color: #333;
                        margin-bottom: 15px;
                        font-size: 24px;
                    }
                    
                    .d-flex {
                        display: flex;
                        justify-content: space-between;
                        flex-wrap: wrap;
                    }
                    
                    .badge {
                        display: inline-block;
                        padding: 5px 10px;
                        background: #f0f0f0;
                        border-radius: 5px;
                        color: #333;
                        font-weight: bold;
                        margin: 5px;
                    }
                    
                    .contract-section {
                        margin-bottom: 30px;
                    }
                    
                    .contract-section h5 {
                        color: #333;
                        border-bottom: 1px solid #ccc;
                        padding-bottom: 5px;
                        font-size: 18px;
                    }
                    
                    .p-3 {
                        padding: 15px;
                        background: #f9f9f9;
                        border-radius: 5px;
                        border: 1px solid #ddd;
                    }
                    
                    .mb-4 {
                        margin-bottom: 20px;
                    }
                    
                    .mb-0 {
                        margin-bottom: 0;
                    }
                </style>
            </head>
            <body>
                <div class="contract-print">
                    ${printContents}
                </div>
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 500);
                    };
                </script>
            </body>
            </html>
        `);
    }
});
