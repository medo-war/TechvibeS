const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.getElementById('container');

signUpButton.addEventListener('click', () => {
	container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
	container.classList.remove("right-panel-active");
});
document.addEventListener('DOMContentLoaded', function() {
    const profileImagePreview = document.getElementById('profileImagePreview');
    const fileInput = document.querySelector('.file-input');
    const chooseFileBtn = document.querySelector('.choose-file-btn');
    const uploadContent = document.querySelector('.upload-content');

    // Gestion du clic sur le bouton
    chooseFileBtn.addEventListener('click', function(e) {
        e.stopPropagation(); // Empêche la propagation au parent
        fileInput.click();
    });

    // Gestion du changement de fichier
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                // Crée une image si elle n'existe pas déjà
                let img = profileImagePreview.querySelector('img');
                if (!img) {
                    img = document.createElement('img');
                    profileImagePreview.prepend(img);
                }
                
                img.src = e.target.result;
                // Cache le contenu d'upload
                uploadContent.style.display = 'none';
            }
            
            reader.readAsDataURL(file);
        }
    });

    // Gestion du drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        profileImagePreview.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        profileImagePreview.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        profileImagePreview.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        profileImagePreview.classList.add('highlight');
    }

    function unhighlight() {
        profileImagePreview.classList.remove('highlight');
    }

    profileImagePreview.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        fileInput.files = files;
        
        // Déclenche l'événement change
        const event = new Event('change');
        fileInput.dispatchEvent(event);
    }
});
document.addEventListener('DOMContentLoaded', function() {
    const profileImagePreview = document.getElementById('profileImagePreview');
    const previewImage = document.getElementById('previewImage');
    const fileInput = document.getElementById('profile-picture');
    const chooseFileBtn = document.querySelector('.choose-file-btn');

    // Gestion du clic sur le bouton CHOOSE FILE
    chooseFileBtn.addEventListener('click', function(e) {
        e.preventDefault();
        fileInput.click();
    });

    // Gestion du changement de fichier
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                profileImagePreview.classList.add('has-image');
            }
            
            reader.readAsDataURL(file);
        }
    });

    // Gestion du drag and drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        profileImagePreview.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        profileImagePreview.addEventListener(eventName, function() {
            this.style.borderColor = '#ff6b6b';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        profileImagePreview.addEventListener(eventName, function() {
            this.style.borderColor = '#ccc';
        }, false);
    });

    profileImagePreview.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            fileInput.files = files;
            const event = new Event('change');
            fileInput.dispatchEvent(event);
        }
    });
});