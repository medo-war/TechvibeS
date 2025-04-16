document.addEventListener('DOMContentLoaded', function() {
    // Table row selection
    const checkboxes = document.querySelectorAll('.users-table tbody input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const row = this.closest('tr');
            if (this.checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });
    });

    // Action buttons
    const viewButtons = document.querySelectorAll('.btn-action.view');
    const editButtons = document.querySelectorAll('.btn-action.edit');
    const deleteButtons = document.querySelectorAll('.btn-action.delete');

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.closest('tr').querySelector('td:nth-child(2)').textContent;
            alert(`Voir profil utilisateur ${userId}`);
        });
    });

    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.closest('tr').querySelector('td:nth-child(2)').textContent;
            alert(`Modifier utilisateur ${userId}`);
        });
    });

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.closest('tr').querySelector('td:nth-child(2)').textContent;
            if (confirm(`Voulez-vous vraiment supprimer l'utilisateur ${userId} ?`)) {
                this.closest('tr').remove();
            }
        });
    });

    // Search functionality
    const searchInput = document.querySelector('.search-box input');
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.users-table tbody tr');

        rows.forEach(row => {
            const userName = row.querySelector('.user-cell span').textContent.toLowerCase();
            const userEmail = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
            
            if (userName.includes(searchTerm) || userEmail.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Pagination buttons
    const paginationButtons = document.querySelectorAll('.btn-pagination:not(:disabled)');
    paginationButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (!this.classList.contains('active')) {
                document.querySelector('.btn-pagination.active').classList.remove('active');
                this.classList.add('active');
            }
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    // Afficher les mots de passe en clair avec option de masquage
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function() {
            const passwordCell = this.closest('.password-cell');
            const passwordText = passwordCell.querySelector('.password-text');
            
            if (this.classList.contains('fa-eye-slash')) {
                // Masquer le mot de passe
                passwordText.style.webkitTextSecurity = 'disc';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
                this.title = "Afficher le mot de passe";
            } else {
                // Afficher le mot de passe
                passwordText.style.webkitTextSecurity = 'none';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
                this.title = "Masquer le mot de passe";
            }
        });
    });

    // Initialisation - Afficher tout en clair par défaut
    document.querySelectorAll('.password-text').forEach(el => {
        el.style.webkitTextSecurity = 'none';
    });
});
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Ouvrir le modal avec les données de l'utilisateur
        const editButtons = document.querySelectorAll('.open-edit-modal');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.dataset.id;
                const firstName = this.dataset.firstname;
                const lastName = this.dataset.lastname;
                const email = this.dataset.email;
                const phone = this.dataset.phone;
                const role = this.dataset.role;
                const pwd = this.dataset.pwd;
                const image = this.dataset.image;

                // Remplir les champs du formulaire
                document.getElementById('userId').value = userId;
                document.getElementById('firstName').value = firstName;
                document.getElementById('lastName').value = lastName;
                document.getElementById('email').value = email;
                document.getElementById('phone').value = phone;
                document.getElementById('role').value = role;
                document.getElementById('pwd').value = pwd;

                // Si une image existe, afficher l'image actuelle
                const imagePreview = document.getElementById('imagePreview');
                if (image) {
                    imagePreview.src = '../../../../' + image;
                }

                // Afficher le modal
                const myModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                myModal.show();
            });
        });
    });
</script>
