<?php
require_once __DIR__.'/../../../../Controller/groupcontroller.php';

$controller = new groupController();
$groups = $controller->getGroups();
?>
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

    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="index1.php">LIVE<span>THE</span>MUSIC</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for artists, events..." aria-label="Search" aria-describedby="btnNavbarSearch" style="background-color: rgba(255, 255, 255, 0.1); color: white; border: 1px solid rgba(255, 0, 85, 0.3);" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
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
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="index1.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Music</div>
                            <a class="nav-link" href="gestion_user1.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Users
                            </a>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvents" aria-expanded="false" aria-controls="collapseEvents">
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Events
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvents" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="upcoming-events.html">Lieux</a>
                                    <a class="nav-link" href="past-events.html">Concerts</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseartist" aria-expanded="false" aria-controls="collapseEvents">
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Artist
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseartist" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link active" href="gestion_group.php">Group</a>
                                    <a class="nav-link" href="gestion_user.php">Artist</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#ticketcollapse" aria-expanded="false" aria-controls="collapseEvents">
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Tickets
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="ticketcollapse" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link active" href="gestion_ticket.php">Ticket</a>
                                    <a class="nav-link" href="gestion_purchased_ticket.php">Purchase</a>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Community</div>
                            <a class="nav-link" href="friends.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-friends"></i></div>
                                Friends
                            </a>
                            <a class="nav-link" href="messages.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-envelope"></i></div>
                                Messages
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        Music Lover
                    </div>
                </nav>
            </div>
            
            <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-users"></i> Gestion des Groupes</h2>
                <div class="actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher groupe...">
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGroupModal">
                        <i class="fas fa-plus"></i> Ajouter
                    </button>
                    
                </div>
            </header>

            <div class="content-body">
                <!-- Group Filters -->
                <div class="filters">
                    <div class="filter-group">
                        <label>Genre :</label>
                        <select>
                            <option>Tous</option>
                            <option>Rock</option>
                            <option>Pop</option>
                            <option>Jazz</option>
                            <option>Classique</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Pays :</label>
                        <select>
                            <option>Tous</option>
                            <option>France</option>
                            <option>USA</option>
                            <option>UK</option>
                            <option>Autre</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Année :</label>
                        <input type="number" placeholder="Année de formation">
                    </div>
                </div>

                <!-- Groups Table -->
                <table class="users-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                            <input type="checkbox">
                            </th>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Genre</th>
                            <th>Année</th>
                            <th>Pays</th>
                            <th>Site Web</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($groups) > 0): ?>
                            <?php foreach ($groups as $group): ?>
                            <tr>
                            <td class="checkbox-cell"><input type="checkbox"></td>
                                <td><?= $group['id'] ?></td>
                                <td>
                                    <div class="user-avatar">
                                        <img src="../../../../<?= htmlspecialchars($group['image_url']) ?>" alt="Image de <?= htmlspecialchars($group['name']) ?>">
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($group['name']) ?></td>
                                <td><?= htmlspecialchars($group['genre']) ?></td>
                                <td><?= $group['formation_year'] ?? 'N/A' ?></td>
                                <td><?= $group['country'] ?? 'N/A' ?></td>
                                <td>
                                    <?php if ($group['website_url']): ?>
                                        <a href="<?= htmlspecialchars($group['website_url']) ?>" target="_blank">Visiter</a>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td class="actions-cell">
                                    <button class="btn-action edit" title="Modifier" onclick="showEditModal({
                                        id: <?= $group['id'] ?>,
                                        name: '<?= addslashes($group['name']) ?>',
                                        image_url: '<?= addslashes($group['image_url']) ?>',
                                        genre: '<?= addslashes($group['genre']) ?>',
                                        formation_year: '<?= addslashes($group['formation_year']) ?>',
                                        country: '<?= addslashes($group['country']) ?>',
                                        bio: '<?= addslashes($group['bio']) ?>',
                                        website_url: '<?= addslashes($group['website_url']) ?>'
                                    })">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a class="btn-action delete" title="Supprimer" href="delete_group.php?id=<?= $group['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a class="btn-action view" title="Voir détails" href="group_details.php?id=<?= $group['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center; color: red;">
                                    Aucun groupe trouvé dans la base de données
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="pagination">
                    <span class="results">1-5 sur 23 groupes</span>
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

<!-- Add Group Modal (similar structure but simplified) -->
<div class="modal fade neon-modal" id="addGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content neon-content">
            <div class="modal-header neon-header">
                <h5 class="modal-title neon-title">
                    <i class="fas fa-users"></i> AJOUTER UN NOUVEAU GROUPE
                </h5>
                <button type="button" class="btn-close neon-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body neon-body">
                <form id="addGroupForm" method="POST" action="add_group.php" enctype="multipart/form-data">
                    <!-- No hidden ID field needed for insert -->
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-users"></i> NOM DU GROUPE
                        </label>
                        <input type="text" class="neon-input" id="add_name" name="name" placeholder="Nom du groupe" required>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-music"></i> GENRE
                        </label>
                        <input type="text" class="neon-input" id="add_genre" name="genre" required>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-calendar"></i> ANNÉE DE FORMATION
                        </label>
                        <input type="number" class="neon-input" id="add_formation_year" name="formation_year">
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-globe"></i> PAYS
                        </label>
                        <input type="text" class="neon-input" id="add_country" name="country">
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-info-circle"></i> BIOGRAPHIE
                        </label>
                        <textarea class="neon-input" id="add_bio" name="bio" rows="3"></textarea>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-link"></i> SITE WEB
                        </label>
                        <input type="url" class="neon-input" id="add_website_url" name="website_url">
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-image"></i> PHOTO DU GROUPE
                        </label>
                        <div class="neon-upload">
                            <label class="neon-upload-btn">
                                <i class="fas fa-cloud-upload-alt"></i> CHOISIR UN FICHIER
                                <input type="file" id="add_image_url" name="image_url" accept="image/*" hidden required>
                            </label>
                            <span class="neon-file-name" id="add-group-file-name">Aucun fichier sélectionné</span>
                        </div>
                        
                        <div class="neon-preview">
                            <div class="neon-preview-item">
                                <p>PREVIEW:</p>
                                <img id="add_group_image_preview" src="#" class="neon-img" style="display:none;">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer neon-footer">
                <button type="button" class="neon-btn neon-btn-cancel" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> ANNULER
                </button>
                <button type="submit" form="addGroupForm" class="neon-btn neon-btn-save">
                    <i class="fas fa-save"></i> AJOUTER
                </button>
            </div>
        </div>
    </div>
</div>
        <!-- Modal de modification style néon -->
        <div class="modal fade neon-modal text-dark" id="editGroupModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content neon-content">
                    <div class="modal-header neon-header">
                        <h5 class="modal-title neon-title">
                            <i class="fas fa-users"></i> MODIFIER LE GROUPE
                        </h5>
                        <button type="button" class="btn-close neon-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body neon-body">
                        <form id="editGroupForm" method="POST" action="edit_group.php" enctype="multipart/form-data">
                            <input type="hidden" name="id" id="edit_group_id">
                            
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-users"></i> NOM DU GROUPE
                                </label>
                                <input type="text" class="neon-input" id="edit_name" name="name" placeholder="Nom du groupe" required>
                            </div>
                            
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-music"></i> GENRE
                                </label>
                                <input type="text" class="neon-input" id="edit_genre" name="genre" required>
                            </div>
                            
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-calendar"></i> ANNÉE DE FORMATION
                                </label>
                                <input type="number" class="neon-input" id="edit_formation_year" name="formation_year">
                            </div>
                            
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-globe"></i> PAYS
                                </label>
                                <input type="text" class="neon-input" id="edit_country" name="country">
                            </div>
                            
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-info-circle"></i> BIOGRAPHIE
                                </label>
                                <textarea class="neon-input" id="edit_bio" name="bio" rows="3"></textarea>
                            </div>
                            
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-link"></i> SITE WEB
                                </label>
                                <input type="url" class="neon-input" id="edit_website_url" name="website_url">
                            </div>
                            
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-image"></i> PHOTO DU GROUPE
                                </label>
                                <div class="neon-upload">
                                    <label class="neon-upload-btn">
                                        <i class="fas fa-cloud-upload-alt"></i> CHOISIR UN FICHIER
                                        <input type="file" id="edit_image_url" name="image_url" accept="image/*" hidden>
                                    </label>
                                    <span class="neon-file-name" id="group-file-name">Aucun fichier sélectionné</span>
                                </div>
                                
                                <div class="neon-preview">
                                    <div class="neon-preview-item">
                                        <p>ACTUELLE:</p>
                                        <img id="current_group_image_preview" src="" class="neon-img">
                                    </div>
                                    <div class="neon-preview-item">
                                        <p>NOUVELLE:</p>
                                        <img id="new_group_image_preview" src="#" class="neon-img" style="display:none;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer neon-footer">
                        <button type="button" class="neon-btn neon-btn-cancel" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> ANNULER
                        </button>
                        <button type="submit" form="editGroupForm" class="neon-btn neon-btn-save">
                            <i class="fas fa-save"></i> ENREGISTRER
                        </button>
                    </div>
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
        <script>
        // Fonction pour afficher le modal
        function showEditModal(group) {
            // Remplir les champs
            document.getElementById('edit_group_id').value = group.id;
            document.getElementById('edit_name').value = group.name;
            document.getElementById('edit_genre').value = group.genre;
            document.getElementById('edit_formation_year').value = group.formation_year || '';
            document.getElementById('edit_country').value = group.country || '';
            document.getElementById('edit_bio').value = group.bio || '';
            document.getElementById('edit_website_url').value = group.website_url || '';
            
            // Afficher l'image actuelle
            if(group.image_url) {
                const preview = document.getElementById('current_group_image_preview');
                preview.src = '../../../../' + group.image_url;
                preview.style.display = 'block';
            }
            
            // Gestion du nom de fichier
            document.getElementById('edit_image_url').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    document.getElementById('group-file-name').textContent = file.name;
                    
                    // Prévisualisation de l'image
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('new_group_image_preview').src = event.target.result;
                        document.getElementById('new_group_image_preview').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Afficher le modal
            new bootstrap.Modal(document.getElementById('editGroupModal')).show();
        }
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