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