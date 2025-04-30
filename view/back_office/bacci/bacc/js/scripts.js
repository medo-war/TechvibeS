/*!
    * Start Bootstrap - SB Admin v7.0.7 (https://startbootstrap.com/template/sb-admin)
    * Copyright 2013-2023 Start Bootstrap
    * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin/blob/master/LICENSE)
    */
    // 
// Scripts
// 

window.addEventListener('DOMContentLoaded', event => {

    // Toggle the side navigation
    const sidebarToggle = document.body.querySelector('#sidebarToggle');
    if (sidebarToggle) {
        // Uncomment Below to persist sidebar toggle between refreshes
        // if (localStorage.getItem('sb|sidebar-toggle') === 'true') {
        //     document.body.classList.toggle('sb-sidenav-toggled');
        // }
        sidebarToggle.addEventListener('click', event => {
            event.preventDefault();
            document.body.classList.toggle('sb-sidenav-toggled');
            localStorage.setItem('sb|sidebar-toggle', document.body.classList.contains('sb-sidenav-toggled'));
        });
    }

});
// Edit modal image preview
document.getElementById('editImage').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
      const reader = new FileReader();
      reader.onload = function(event) {
        document.getElementById('currentImagePreview').src = event.target.result;
      };
      reader.readAsDataURL(e.target.files[0]);
    }
  });
  
  // Set current image when modal opens
  editModal.addEventListener('show.bs.modal', function(event) {
    // ... existing code ...
    const currentImage = event.relatedTarget.closest('tr').querySelector('.user-cell img').src;
    document.getElementById('currentImagePreview').src = currentImage;
  });
  // Add form validation
document.getElementById('addForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('addImage');
    if (fileInput.files.length > 0) {
      const file = fileInput.files[0];
      const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
      const maxSize = 2 * 1024 * 1024; // 2MB
      
      if (!validTypes.includes(file.type)) {
        fileInput.classList.add('is-invalid');
        e.preventDefault();
        return;
      }
      
      if (file.size > maxSize) {
        fileInput.classList.add('is-invalid');
        e.preventDefault();
        return;
      }
      
      fileInput.classList.remove('is-invalid');
      fileInput.classList.add('is-valid');
    }
  });
