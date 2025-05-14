document.addEventListener('DOMContentLoaded', function() {
    // Validation function for a single field
    function validateField(field) {
        const errorElement = document.getElementById(`${field.id}-error`);
        let isValid = true;
        let errorMessage = '';

        // Check for empty field
        if (!field.value.trim()) {
            isValid = false;
            errorMessage = 'Ce champ est requis';
        } else {
            // Specific validation for nom_lieux and adresse
            if (field.name === 'nom_lieux' || field.name === 'adresse') {
                if (field.value.trim().length < 3) {
                    isValid = false;
                    errorMessage = 'Minimum 3 caractères';
                }
            }
            // Specific validation for capacite
            if (field.name === 'capacite') {
                const value = parseInt(field.value);
                if (isNaN(value) || value < 0) {
                    isValid = false;
                    errorMessage = 'Capacité positive requise';
                }
            }
        }

        // Update field styling and error message
        if (isValid) {
            field.classList.remove('invalid');
            field.classList.add('valid');
            errorElement.textContent = '';
        } else {
            field.classList.remove('valid');
            field.classList.add('invalid');
            errorElement.textContent = errorMessage;
        }

        return isValid;
    }

    // Validate entire form
    function validateForm(form) {
        let isValid = true;
        const fields = form.querySelectorAll('input:not([type="hidden"])');
        
        fields.forEach(field => {
            if (!validateField(field)) {
                isValid = false;
            }
        });

        return isValid;
    }

    // Prevent form submission if validation fails
    function handleFormSubmission(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default submission
            if (validateForm(this)) {
                sessionStorage.setItem('shouldScroll', 'true');
                this.submit();
            }
        });
    }

    // Real-time validation on input and blur
    function setupRealTimeValidation(inputs) {
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                validateField(this);
            });
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    }

    // Initialize validation for add form
    const addForm = document.getElementById('add-form');
    if (addForm) {
        handleFormSubmission(addForm);
        setupRealTimeValidation(addForm.querySelectorAll('input:not([type="hidden"])'));
    }

    // Initialize validation for edit forms
    const editForms = document.querySelectorAll('.edit-form');
    editForms.forEach(form => {
        handleFormSubmission(form);
        setupRealTimeValidation(form.querySelectorAll('input:not([type="hidden"])'));
    });
});