document.addEventListener('DOMContentLoaded', function() {
    const profileIcon = document.getElementById('profileIcon');
    
    profileIcon.addEventListener('click', function() {
        fetch('get_user_profile.php')
            .then(response => response.json())
            .then(data => {
                showProfileModal(data);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load profile information');
            });
    });
    
    function showProfileModal(userData) {
        const modalHTML = `
            <div class="profile-modal" id="profileModal">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    
                    <div class="profile-header">
                        <div class="profile-image">
                            <img src="${userData.image || 'assets/images/default-avatar.png'}" alt="Profile">
                        </div>
                        <h2>${userData.first_name} ${userData.last_name}</h2>
                        <p>${userData.role === 'artist' ? 'Artist' : 'Music Fan'}</p>
                    </div>
                    
                    <div class="profile-info">
                        <div class="profile-info-item">
                            <strong>NAME</strong>
                            <div>${userData.first_name}</div>
                        </div>
                        <div class="profile-info-item">
                            <strong>FULL NAME</strong>
                            <div>${userData.first_name} ${userData.last_name}</div>
                        </div>
                        <div class="profile-info-item">
                            <strong>EMAIL ADDRESS</strong>
                            <div>${userData.email}</div>
                        </div>
                        <div class="profile-info-item">
                            <strong>PHONE NUMBER</strong>
                            <div>${userData.phone || 'Not provided'}</div>
                        </div>
                        <div class="profile-info-item">
                            <strong>LOCATION</strong>
                            <div>${userData.location || 'Not specified'}</div>
                        </div>
                        <div class="profile-info-item">
                            <strong>POSTAL CODE</strong>
                            <div>${userData.postal_code || 'Not specified'}</div>
                        </div>
                    </div>
                    
                    <button class="save-btn">SAVE CHANGES</button>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        const modal = document.getElementById('profileModal');
        modal.style.display = 'flex';
        
        document.querySelector('.close-modal').addEventListener('click', function() {
            modal.remove();
        });
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }
});