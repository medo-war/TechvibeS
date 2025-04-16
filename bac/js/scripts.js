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

function validateForm() {
    const name = document.getElementById('editName').value.trim();
    const username = document.getElementById('editUsername').value.trim();
    const genre = document.getElementById('editGenre').value.trim();
    const country = document.getElementById('editCountry').value.trim();
    
    if (!name || !username || !genre || !country) {
        alert("Veuillez remplir tous les champs obligatoires.");
        return false;
    }

    if (name.length < 2 || username.length < 3) {
        alert("Le nom doit contenir au moins 2 caractères et le nom d'utilisateur au moins 3.");
        return false;
    }

    return true;
}
function fillEditModal(button) {
    document.getElementById('editId').value = button.getAttribute('data-id');
    document.getElementById('editName').value = button.getAttribute('data-name');
    document.getElementById('editUsername').value = button.getAttribute('data-username');
    document.getElementById('editGroup').value = button.getAttribute('data-group');
    document.getElementById('editGenre').value = button.getAttribute('data-genre');
    document.getElementById('editCountry').value = button.getAttribute('data-country');
    document.getElementById('editBio').value = button.getAttribute('data-bio');
  }
