<?php
require_once __DIR__.'/../../../../Controller/ticketcontroller.php';

$controller = new ticketController();
$tickets = $controller->getTickets();
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
            <?php include('sidebar.php'); ?>
            
            <main class="main-content">
            <header class="content-header">
                <h2><i class="fas fa-ticket-alt"></i> Gestion des Tickets</h2>
                <div class="actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher tickets...">
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTicketModal">
                        <i class="fas fa-plus"></i> Ajouter
                    </button>
                </div>
            </header>

            <div class="content-body">
                <!-- Ticket Filters -->
                <div class="filters">
                    <div class="filter-group">
                        <label>Type :</label>
                        <select>
                            <option>Tous</option>
                            <option>General Admission</option>
                            <option>VIP</option>
                            <option>Premium</option>
                            <option>Backstage</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Prix :</label>
                        <select>
                            <option>Tous</option>
                            <option>Moins de 50€</option>
                            <option>50-100€</option>
                            <option>100-200€</option>
                            <option>Plus de 200€</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Date :</label>
                        <input type="date" placeholder="Date de l'événement">
                    </div>
                </div>

                <!-- Tickets Table -->
                <table class="users-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                            <input type="checkbox">
                            </th>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Concert</th>
                            <th>Artiste</th>
                            <th>Date</th>
                            <th>Lieu</th>
                            <th>Prix</th>
                            <th>Type</th>
                            <th>Disponibilité</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($tickets) > 0): ?>
                            <?php foreach ($tickets as $ticket): ?>
                            <tr>
                            <td class="checkbox-cell"><input type="checkbox"></td>
                                <td><?= $ticket['id'] ?></td>
                                <td>
                                    <div class="user-avatar">
                                        <img src="../../../../<?= htmlspecialchars($ticket['image_url']) ?>" alt="Image de <?= htmlspecialchars($ticket['concert_name']) ?>">
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($ticket['concert_name']) ?></td>
                                <td><?= htmlspecialchars($ticket['artist_name']) ?></td>
                                <td><?= date('d/m/Y', strtotime($ticket['event_date'])) ?> à <?= date('H:i', strtotime($ticket['event_time'])) ?></td>
                                <td><?= htmlspecialchars($ticket['venue']) ?>, <?= htmlspecialchars($ticket['city']) ?></td>
                                <td><?= number_format($ticket['price'], 2) ?>€</td>
                                <td><?= htmlspecialchars($ticket['ticket_type']) ?></td>
                                <td><?= $ticket['available_quantity'] ?></td>
                                <td class="actions-cell">
                                    <button class="btn-action edit" title="Modifier" onclick="showEditModal({
                                        id: <?= $ticket['id'] ?>,
                                        concert_name: '<?= addslashes($ticket['concert_name']) ?>',
                                        artist_name: '<?= addslashes($ticket['artist_name']) ?>',
                                        event_date: '<?= addslashes($ticket['event_date']) ?>',
                                        event_time: '<?= addslashes($ticket['event_time']) ?>',
                                        venue: '<?= addslashes($ticket['venue']) ?>',
                                        city: '<?= addslashes($ticket['city']) ?>',
                                        country: '<?= addslashes($ticket['country']) ?>',
                                        price: '<?= addslashes($ticket['price']) ?>',
                                        ticket_type: '<?= addslashes($ticket['ticket_type']) ?>',
                                        available_quantity: '<?= addslashes($ticket['available_quantity']) ?>',
                                        image_url: '<?= addslashes($ticket['image_url']) ?>'
                                    })">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a class="btn-action delete" title="Supprimer" href="delete_ticket.php?id=<?= $ticket['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce ticket ?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a class="btn-action view" title="Voir détails" href="ticket_details.php?id=<?= $ticket['id'] ?>">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" style="text-align: center; color: red;">
                                    Aucun ticket trouvé dans la base de données
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="pagination">
                    <span class="results">1-5 sur 23 tickets</span>
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

<!-- Add Ticket Modal -->
<div class="modal fade neon-modal" id="addTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content neon-content">
            <div class="modal-header neon-header">
                <h5 class="modal-title neon-title">
                    <i class="fas fa-ticket-alt"></i> AJOUTER UN NOUVEAU TICKET
                </h5>
                <button type="button" class="btn-close neon-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body neon-body">
                <form id="addTicketForm" method="POST" action="add_ticket.php" enctype="multipart/form-data">
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-music"></i> NOM DU CONCERT
                        </label>
                        <input type="text" class="neon-input" id="add_concert_name" name="concert_name" placeholder="Nom du concert" required>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-user"></i> NOM DE L'ARTISTE
                        </label>
                        <input type="text" class="neon-input" id="add_artist_name" name="artist_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-calendar"></i> DATE
                                </label>
                                <input type="date" class="neon-input" id="add_event_date" name="event_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-clock"></i> HEURE
                                </label>
                                <input type="time" class="neon-input" id="add_event_time" name="event_time" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-map-marker-alt"></i> LIEU
                        </label>
                        <input type="text" class="neon-input" id="add_venue" name="venue" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-city"></i> VILLE
                                </label>
                                <input type="text" class="neon-input" id="add_city" name="city" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-globe"></i> PAYS
                                </label>
                                <input type="text" class="neon-input" id="add_country" name="country" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-tag"></i> PRIX (€)
                                </label>
                                <input type="number" step="0.01" class="neon-input" id="add_price" name="price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-ticket-alt"></i> TYPE DE TICKET
                                </label>
                                <select class="neon-input" id="add_ticket_type" name="ticket_type" required>
                                    <option value="General Admission">General Admission</option>
                                    <option value="VIP">VIP</option>
                                    <option value="Premium">Premium</option>
                                    <option value="Backstage">Backstage</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-box-open"></i> QUANTITÉ DISPONIBLE
                        </label>
                        <input type="number" class="neon-input" id="add_available_quantity" name="available_quantity" required>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-image"></i> IMAGE
                        </label>
                        <div class="neon-upload">
                            <label class="neon-upload-btn">
                                <i class="fas fa-cloud-upload-alt"></i> CHOISIR UN FICHIER
                                <input type="file" id="add_image_url" name="image_url" accept="image/*" hidden required>
                            </label>
                            <span class="neon-file-name" id="add-ticket-file-name">Aucun fichier sélectionné</span>
                        </div>
                        
                        <div class="neon-preview">
                            <div class="neon-preview-item">
                                <p>PREVIEW:</p>
                                <img id="add_ticket_image_preview" src="#" class="neon-img" style="display:none;">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer neon-footer">
                <button type="button" class="neon-btn neon-btn-cancel" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> ANNULER
                </button>
                <button type="submit" form="addTicketForm" class="neon-btn neon-btn-save">
                    <i class="fas fa-save"></i> AJOUTER
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Ticket Modal -->
<div class="modal fade neon-modal text-dark" id="editTicketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content neon-content">
            <div class="modal-header neon-header">
                <h5 class="modal-title neon-title">
                    <i class="fas fa-ticket-alt"></i> MODIFIER LE TICKET
                </h5>
                <button type="button" class="btn-close neon-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body neon-body">
                <form id="editTicketForm" method="POST" action="edit_ticket.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_ticket_id">
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-music"></i> NOM DU CONCERT
                        </label>
                        <input type="text" class="neon-input" id="edit_concert_name" name="concert_name" placeholder="Nom du concert" required>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-user"></i> NOM DE L'ARTISTE
                        </label>
                        <input type="text" class="neon-input" id="edit_artist_name" name="artist_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-calendar"></i> DATE
                                </label>
                                <input type="date" class="neon-input" id="edit_event_date" name="event_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-clock"></i> HEURE
                                </label>
                                <input type="time" class="neon-input" id="edit_event_time" name="event_time" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-map-marker-alt"></i> LIEU
                        </label>
                        <input type="text" class="neon-input" id="edit_venue" name="venue" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-city"></i> VILLE
                                </label>
                                <input type="text" class="neon-input" id="edit_city" name="city" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-globe"></i> PAYS
                                </label>
                                <input type="text" class="neon-input" id="edit_country" name="country" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-tag"></i> PRIX (€)
                                </label>
                                <input type="number" step="0.01" class="neon-input" id="edit_price" name="price" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="neon-form-group">
                                <label class="neon-label">
                                    <i class="fas fa-ticket-alt"></i> TYPE DE TICKET
                                </label>
                                <select class="neon-input" id="edit_ticket_type" name="ticket_type" required>
                                    <option value="General Admission">General Admission</option>
                                    <option value="VIP">VIP</option>
                                    <option value="Premium">Premium</option>
                                    <option value="Backstage">Backstage</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-box-open"></i> QUANTITÉ DISPONIBLE
                        </label>
                        <input type="number" class="neon-input" id="edit_available_quantity" name="available_quantity" required>
                    </div>
                    
                    <div class="neon-form-group">
                        <label class="neon-label">
                            <i class="fas fa-image"></i> IMAGE
                        </label>
                        <div class="neon-upload">
                            <label class="neon-upload-btn">
                                <i class="fas fa-cloud-upload-alt"></i> CHOISIR UN FICHIER
                                <input type="file" id="edit_image_url" name="image_url" accept="image/*" hidden>
                            </label>
                            <span class="neon-file-name" id="ticket-file-name">Aucun fichier sélectionné</span>
                        </div>
                        
                        <div class="neon-preview">
                            <div class="neon-preview-item">
                                <p>ACTUELLE:</p>
                                <img id="current_ticket_image_preview" src="" class="neon-img">
                            </div>
                            <div class="neon-preview-item">
                                <p>NOUVELLE:</p>
                                <img id="new_ticket_image_preview" src="#" class="neon-img" style="display:none;">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer neon-footer">
                <button type="button" class="neon-btn neon-btn-cancel" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> ANNULER
                </button>
                <button type="submit" form="editTicketForm" class="neon-btn neon-btn-save">
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
        function showEditModal(ticket) {
            // Remplir les champs
            document.getElementById('edit_ticket_id').value = ticket.id;
            document.getElementById('edit_concert_name').value = ticket.concert_name;
            document.getElementById('edit_artist_name').value = ticket.artist_name;
            document.getElementById('edit_event_date').value = ticket.event_date;
            document.getElementById('edit_event_time').value = ticket.event_time;
            document.getElementById('edit_venue').value = ticket.venue;
            document.getElementById('edit_city').value = ticket.city;
            document.getElementById('edit_country').value = ticket.country;
            document.getElementById('edit_price').value = ticket.price;
            document.getElementById('edit_ticket_type').value = ticket.ticket_type;
            document.getElementById('edit_available_quantity').value = ticket.available_quantity;
            
            // Afficher l'image actuelle
            if(ticket.image_url) {
                const preview = document.getElementById('current_ticket_image_preview');
                preview.src = '../../../../' + ticket.image_url;
                preview.style.display = 'block';
            }
            
            // Gestion du nom de fichier
            document.getElementById('edit_image_url').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    document.getElementById('ticket-file-name').textContent = file.name;
                    
                    // Prévisualisation de l'image
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('new_ticket_image_preview').src = event.target.result;
                        document.getElementById('new_ticket_image_preview').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Afficher le modal
            new bootstrap.Modal(document.getElementById('editTicketModal')).show();
        }
        
        // Gestion de l'upload d'image pour l'ajout
        document.getElementById('add_image_url').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('add-ticket-file-name').textContent = file.name;
                
                // Prévisualisation de l'image
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('add_ticket_image_preview').src = event.target.result;
                    document.getElementById('add_ticket_image_preview').style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
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