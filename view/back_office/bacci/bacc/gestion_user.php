<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="LiveTheMusic - Dashboard" />
        <meta name="author" content="" />
        <title>Dashboard - LiveTheMusic</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="css/user.css">
        <script scr="js/script.js"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index.html">LIVE<span>THE</span>MUSIC</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0" method="GET" action="">
                <div class="input-group">
                    <input class="form-control" type="text" name="search" placeholder="Search for artists..." aria-label="Search" aria-describedby="btnNavbarSearch" style="background-color: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 0, 85, 0.3);" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" style="background-color: var(--light-color); border: 1px solid rgba(255, 0, 85, 0.3);">
                        <li><a class="dropdown-item" href="#!" style="color: white;">Profile</a></li>
                        <li><a class="dropdown-item" href="#!" style="color: white;">Settings</a></li>
                        <li><hr class="dropdown-divider" style="border-color: rgba(255, 0, 85, 0.3);" /></li>
                        <li><a class="dropdown-item" href="#!" style="color: white;">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <?php include('sidebar.php'); ?>
            
            <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-users"></i> Gestion des artistes</h2>
                <div class="actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher utilisateur...">
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus"></i> Ajouter
                    </button>
                </div>
            </header>

            <div class="content-body">
                <!-- User Filters -->
                <div class="filters">
                    <div class="filter-group">
                        <label>Statut :</label>
                        <select>
                            <option>Tous</option>
                            <option>Actif</option>
                            <option>Inactif</option>
                            <option>Suspendu</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Rôle :</label>
                        <select>
                            <option>Tous</option>
                            <option>Admin</option>
                            <option>Éditeur</option>
                            <option>Utilisateur</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Date :</label>
                        <input type="date">
                    </div>
                </div>

                <!-- Users Table -->
                <?php
                require $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/artcont.php';
                $artistController = new ArtistController();
                
                // Check if search query is provided
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $searchTerm = trim($_GET['search']);
                    $artists = $artistController->searchArtistsByName($searchTerm);
                    
                    // Display search results message
                    echo '<div class="alert alert-info">Showing results for: "' . htmlspecialchars($searchTerm) . '" (' . count($artists) . ' results found)</div>';
                } else {
                    // If no search, get all artists
                    $artists = $artistController->getArtists();
                }
                ?>

                <div class="table-responsive">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell">
                                    <input type="checkbox">
                                </th>
                                <th>ID</th>
                                <th>Artist</th>
                                <th>Username</th>
                                <th>Group/Band</th>
                                <th>Genre</th>
                                <th>Country</th>
                                <th>Bio</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($artists as $artist): ?>
                                <tr>
                                    <td class="checkbox-cell"><input type="checkbox"></td>
                                    <td>#<?= htmlspecialchars($artist['id']); ?></td>
                                    <td class="user-cell">
                                        <img src="<?= htmlspecialchars($artist['image_url']); ?>" alt="<?= htmlspecialchars($artist['name']); ?>">
                                        <span><?= htmlspecialchars($artist['name']); ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($artist['username']); ?></td>
                                    <td><?= htmlspecialchars($artist['group_name']); ?></td>
                                    <td><span class="role-badge <?= strtolower($artist['genre']); ?>"><?= htmlspecialchars($artist['genre']); ?></span></td>
                                    <td><?= htmlspecialchars($artist['country']); ?></td>
                                    <td class="bio-cell"><?= nl2br(htmlspecialchars($artist['bio'])); ?></td>
                                    <td class="actions-cell">
                                        <button class="btn-action view" title="View profile">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn-action edit" title="Edit" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editModal"
                                                data-id="<?= $artist['id']; ?>"
                                                data-name="<?= htmlspecialchars($artist['name']); ?>"
                                                data-username="<?= htmlspecialchars($artist['username']); ?>"
                                                data-group="<?= htmlspecialchars($artist['group_name']); ?>"
                                                data-genre="<?= htmlspecialchars($artist['genre']); ?>"
                                                data-country="<?= htmlspecialchars($artist['country']); ?>"
                                                data-bio="<?= htmlspecialchars($artist['bio']); ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" action="delete_artist.php" onsubmit="return confirm('Are you sure you want to delete this artist?');">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($artist['id']); ?>">
                                            <button type="submit" class="btn-action" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade neon-modal text-dark" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content neon-content">
            <form id="editForm" method="POST" action="edit_artist.php" enctype="multipart/form-data">
                <div class="modal-header neon-header">
                    <h5 class="modal-title neon-title" id="editModalLabel">
                        <i class="fas fa-user-edit"></i> EDIT ARTIST
                    </h5>
                    <button type="button" class="btn-close neon-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body neon-body">
                    <input type="hidden" name="id" id="editId">
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-user"></i> NAME
                        </label>
                        <input type="text" class="neon-input" id="editName" name="name" required>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-image"></i> ARTIST IMAGE
                        </label>
                        <div class="neon-upload">
                            <label class="neon-upload-btn">
                                <i class="fas fa-cloud-upload-alt"></i> CHANGE IMAGE
                                <input type="file" id="editImage" name="image" accept="image/*" hidden>
                            </label>
                            <span class="neon-file-name" id="edit-file-name">No file selected</span>
                        </div>
                        <div class="neon-form-text">Leave empty to keep current image</div>
                        <div class="neon-preview mt-2 text-center">
                            <img id="currentImagePreview" src="" class="neon-img rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                    </div>

                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-at"></i> USERNAME
                        </label>
                        <input type="text" class="neon-input" id="editUsername" name="username" required>
                    </div>

                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-users"></i> GROUP
                        </label>
                        <select class="neon-select text-dark" id="editGroup" name="group_id">
                            <option value="">-- No Group --</option>
                            <?php
                            // Fetch groups from database
                            require_once __DIR__.'/../../../../Controller/groupController.php';
                            $groupController = new groupController();
                            $groups = $groupController->getGroups();
                            
                            foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-music"></i> GENRE
                        </label>
                        <input type="text" class="neon-input" id="editGenre" name="genre">
                    </div>

                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-globe"></i> COUNTRY
                        </label>
                        <input type="text" class="neon-input" id="editCountry" name="country">
                    </div>

                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-info-circle"></i> BIO
                        </label>
                        <textarea class="neon-input" id="editBio" name="bio" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer neon-footer">
                    <button type="button" class="neon-btn neon-btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> CANCEL
                    </button>
                    <button type="submit" class="neon-btn neon-btn-save">
                        <i class="fas fa-save"></i> SAVE CHANGES
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

                <!-- Add Modal -->
                <div class="modal fade neon-modal text-dark" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content neon-content">
            <form id="addForm" method="POST" action="add_artist.php" enctype="multipart/form-data">
                <div class="modal-header neon-header">
                    <h5 class="modal-title neon-title" id="addModalLabel">
                        <i class="fas fa-user-plus"></i> ADD ARTIST
                    </h5>
                    <button type="button" class="btn-close neon-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body neon-body">
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-user"></i> NAME
                        </label>
                        <input type="text" class="neon-input" id="addName" name="name" required>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-image"></i> ARTIST IMAGE
                        </label>
                        <div class="neon-upload">
                            <label class="neon-upload-btn">
                                <i class="fas fa-cloud-upload-alt"></i> CHOOSE FILE
                                <input type="file" id="addImage" name="image" accept="image/*" hidden>
                            </label>
                            <span class="neon-file-name" id="add-file-name">No file selected</span>
                        </div>
                        <div class="neon-form-text">Recommended size: 500x500px (Max 2MB)</div>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-at"></i> USERNAME
                        </label>
                        <input type="text" class="neon-input" id="addUsername" name="username" required>
                    </div>

                    <div class="neon-form-group ">
                        <label class="neon-label">
                            <i class="fas fa-users"></i> GROUP
                        </label>
                        <select class="neon-select text-dark" id="addGroup" name="group_id">
                            <option value="">-- Select Group --</option>
                            <?php
                            // Fetch groups from database
                            require_once __DIR__.'/../../../../Controller/groupcontroller.php';
                            $groupController = new groupController();
                            $groups = $groupController->getGroups();
                            
                            foreach ($groups as $group): ?>
                                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-music"></i> GENRE
                        </label>
                        <input type="text" class="neon-input" id="addGenre" name="genre">
                    </div>

                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-globe"></i> COUNTRY
                        </label>
                        <input type="text" class="neon-input" id="addCountry" name="country">
                    </div>

                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-info-circle"></i> BIO
                        </label>
                        <textarea class="neon-input" id="addBio" name="bio" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer neon-footer">
                    <button type="button" class="neon-btn neon-btn-cancel" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> CANCEL
                    </button>
                    <button type="submit" class="neon-btn neon-btn-save">
                        <i class="fas fa-plus"></i> ADD ARTIST
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Style Néon */
.neon-modal {
    font-family: 'Poppins', sans-serif;
}

.neon-content {
    background: #1a1a2e;
    border: 1px solid #ff0055;
    box-shadow: 0 0 10px #ff0055, 0 0 20px #ff0055;
    color: #fff;
    border-radius: 8px;
}

.neon-header {
    border-bottom: 1px solid rgba(255, 0, 85, 0.3);
}

.neon-title {
    color: #ff0055;
    text-shadow: 0 0 5px #ff0055;
    font-weight: 600;
    letter-spacing: 1px;
}

.neon-close {
    color: #ff0055;
    opacity: 1;
    text-shadow: none;
}

.neon-label {
    color: #ff0055;
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    letter-spacing: 1px;
}

.neon-input, .neon-select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 0, 85, 0.3);
    color: white;
    padding: 10px 15px;
    border-radius: 4px;
    width: 100%;
    transition: all 0.3s;
}

.neon-input:focus, .neon-select:focus {
    border-color: #ff0055;
    box-shadow: 0 0 5px #ff0055;
    outline: none;
}

.neon-upload {
    display: flex;
    align-items: center;
    gap: 10px;
}

.neon-upload-btn {
    background: rgba(255, 0, 85, 0.2);
    border: 1px dashed #ff0055;
    color: #ff0055;
    padding: 8px 15px;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

.neon-upload-btn:hover {
    background: rgba(255, 0, 85, 0.3);
}

.neon-file-name {
    color: #aaa;
    font-size: 13px;
}

.neon-preview {
    display: flex;
    gap: 20px;
    margin-top: 15px;
}

.neon-preview-item {
    text-align: center;
}

.neon-preview-item p {
    color: #ff0055;
    font-size: 12px;
    margin-bottom: 5px;
}

.neon-img {
    max-height: 100px;
    border: 1px solid rgba(255, 0, 85, 0.3);
    border-radius: 4px;
}

.neon-btn {
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    font-weight: 600;
    letter-spacing: 1px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.neon-btn-cancel {
    background: rgba(255, 0, 85, 0.2);
    color: #ff0055;
}

.neon-btn-cancel:hover {
    background: rgba(255, 0, 85, 0.3);
}

.neon-btn-save {
    background: #ff0055;
    color: white;
    box-shadow: 0 0 5px #ff0055;
}

.neon-btn-save:hover {
    background: #ff0066;
    box-shadow: 0 0 10px #ff0055;
}
</style>

                <!-- Pagination -->
                <div class="pagination">
                    <span class="results">1-5 sur 23 utilisateurs</span>
                    <div class="pagination-controls">
                        <button class="btn-pagination" disabled>
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="btn-pagination active">1</button>
                        <button class="btn-pagination">2</button>
                        <button class="btn-pagination">3</button>
                        <button class="btn-pagination">4</button>
                        <button class="btn-pagination">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <script>
        // Edit modal handling
        document.addEventListener('DOMContentLoaded', function() {
            var editModal = document.getElementById('editModal');
            
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    // Button that triggered the modal
                    var button = event.relatedTarget;
                    
                    // Extract info from data-* attributes
                    var id = button.getAttribute('data-id');
                    var name = button.getAttribute('data-name');
                    var username = button.getAttribute('data-username');
                    var group = button.getAttribute('data-group');
                    var genre = button.getAttribute('data-genre');
                    var country = button.getAttribute('data-country');
                    var bio = button.getAttribute('data-bio');
                    
                    // Update the modal's content
                    var modalTitle = editModal.querySelector('.modal-title');
                    var modalId = editModal.querySelector('#editId');
                    var modalName = editModal.querySelector('#editName');
                    var modalUsername = editModal.querySelector('#editUsername');
                    var modalGroup = editModal.querySelector('#editGroup');
                    var modalGenre = editModal.querySelector('#editGenre');
                    var modalCountry = editModal.querySelector('#editCountry');
                    var modalBio = editModal.querySelector('#editBio');
                    
                    modalTitle.textContent = 'Edit Artist #' + id;
                    modalId.value = id;
                    modalName.value = name;
                    modalUsername.value = username;
                    modalGroup.value = group;
                    modalGenre.value = genre;
                    modalCountry.value = country;
                    modalBio.value = bio;
                });
            }
            
            // Form validation for edit form
            document.getElementById('editForm').addEventListener('submit', function(e) {
                var name = document.getElementById('editName').value;
                var username = document.getElementById('editUsername').value;
                
                if (name.trim() === '' || username.trim() === '') {
                    alert('Name and Username are required fields');
                    e.preventDefault();
                }
            });
            
            // Form validation for add form
            document.getElementById('addForm').addEventListener('submit', function(e) {
                var name = document.getElementById('addName').value;
                var username = document.getElementById('addUsername').value;
                
                if (name.trim() === '' || username.trim() === '') {
                    alert('Name and Username are required fields');
                    e.preventDefault();
                }
            });
        });
        </script>

        <script src="js/user.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="assets/demo/chart-pie-demo.js"></script>
    </body>
</html>