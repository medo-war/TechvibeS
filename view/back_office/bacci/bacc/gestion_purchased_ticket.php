<?php
require_once __DIR__.'/../../../../Controller/ticketpurchasecont.php';

$controller = new TicketPurchaseController();
$purchases = $controller->getTicketPurchases() ;
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
                                    <a class="nav-link" href="gestion_group.php">Group</a>
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
                <h2><i class="fas fa-ticket-alt"></i> Gestion des Achats de Tickets</h2>
                <div class="actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher achat...">
                    </div>
                    <button class="btn btn-primary" onclick="showAddModal()">
                        <i class="fas fa-plus"></i> Ajouter
                    </button>
                </div>
            </header>

            <div class="content-body">
                <!-- Purchase Filters -->
                <div class="filters">
                    <div class="filter-group">
                        <label>Statut :</label>
                        <select>
                            <option>Tous</option>
                            <option>Completed</option>
                            <option>Pending</option>
                            <option>Cancelled</option>
                            <option>Refunded</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Concert :</label>
                        <select>
                            <option>Tous</option>
                            <?php foreach (array_unique(array_column($purchases, 'concert_name')) as $concert): ?>
                                <option><?= htmlspecialchars($concert) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Date :</label>
                        <input type="date">
                    </div>
                </div>

                <!-- Purchases Table -->
                <table class="users-table">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input type="checkbox">
                            </th>
                            <th>ID</th>
                            <th>Client</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Concert</th>
                            <th>Ticket ID</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($purchases) > 0): ?>
                            <?php foreach ($purchases as $purchase): ?>
                            <tr>
                                <td class="checkbox-cell"><input type="checkbox"></td>
                                <td><?= $purchase['id'] ?></td>
                                <td><?= htmlspecialchars($purchase['first_name'].' '.$purchase['last_name']) ?></td>
                                <td><?= htmlspecialchars($purchase['email']) ?></td>
                                <td><?= $purchase['phone'] ?? 'N/A' ?></td>
                                <td><?= htmlspecialchars($purchase['concert_name']) ?></td>
                                <td><?= $purchase['ticket_id'] ?></td>
                                <td><?= number_format($purchase['ticket_price'], 2) ?> €</td>
                                <td><?= $purchase['quantity'] ?></td>
                                <td><?= number_format($purchase['total_amount'], 2) ?> €</td>
                                <td><?= date('d/m/Y H:i', strtotime($purchase['purchase_date'])) ?></td>
                                <td>
                                    <span class="status-badge <?= strtolower($purchase['status']) ?>">
                                        <?= ucfirst($purchase['status']) ?>
                                    </span>
                                </td>
                                <td class="actions-cell">

                                    <a class="btn-action delete" title="Supprimer" href="delete_ticket_purchase.php?id=<?= $purchase['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet achat ?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <button class="btn-action view" title="Voir ticket" onclick="viewTicket('<?= addslashes($purchase['ticket_code'] ?? '') ?>')">
                                        <i class="fas fa-ticket-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13" style="text-align: center; color: red;">
                                    Aucun achat de ticket trouvé dans la base de données
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                <span class="results">1-5 sur 23 achats</span>
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
        </main>

        <!-- Modal de modification style néon -->

        <!-- Modal d'ajout -->

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
            // Fonction pour afficher le modal d'édition
            function showEditModal(purchase) {
                // Remplir les champs
                document.getElementById('edit_purchase_id').value = purchase.id;
                document.getElementById('edit_first_name').value = purchase.first_name;
                document.getElementById('edit_last_name').value = purchase.last_name;
                document.getElementById('edit_email').value = purchase.email;
                document.getElementById('edit_phone').value = purchase.phone || '';
                document.getElementById('edit_concert_name').value = purchase.concert_name;
                document.getElementById('edit_ticket_id').value = purchase.ticket_id;
                document.getElementById('edit_ticket_price').value = purchase.ticket_price;
                document.getElementById('edit_quantity').value = purchase.quantity;
                document.getElementById('edit_total_amount').value = purchase.total_amount;
                document.getElementById('edit_status').value = purchase.status;
                document.getElementById('edit_payment_method').value = purchase.payment_method || '';
                document.getElementById('edit_transaction_id').value = purchase.transaction_id || '';
                document.getElementById('edit_ticket_code').value = purchase.ticket_code || '';
                
                // Calcul automatique du total
                document.getElementById('edit_ticket_price').addEventListener('input', calculateTotal);
                document.getElementById('edit_quantity').addEventListener('input', calculateTotal);
                
                function calculateTotal() {
                    const price = parseFloat(document.getElementById('edit_ticket_price').value) || 0;
                    const quantity = parseInt(document.getElementById('edit_quantity').value) || 0;
                    document.getElementById('edit_total_amount').value = (price * quantity).toFixed(2);
                }
                
                // Afficher le modal
                new bootstrap.Modal(document.getElementById('editPurchaseModal')).show();
            }
            
            // Fonction pour afficher le modal d'ajout
            function showAddModal() {
                // Calcul automatique du total
                document.getElementById('add_ticket_price').addEventListener('input', calculateTotal);
                document.getElementById('add_quantity').addEventListener('input', calculateTotal);
                
                function calculateTotal() {
                    const price = parseFloat(document.getElementById('add_ticket_price').value) || 0;
                    const quantity = parseInt(document.getElementById('add_quantity').value) || 0;
                    document.getElementById('add_total_amount').value = (price * quantity).toFixed(2);
                }
                
                // Générer un code ticket aléatoire si vide
                document.getElementById('add_ticket_code').addEventListener('focus', function() {
                    if (!this.value) {
                        this.value = 'TKT-' + Math.random().toString(36).substr(2, 8).toUpperCase();
                    }
                });
                
                // Afficher le modal
                new bootstrap.Modal(document.getElementById('addPurchaseModal')).show();
            }
            
            // Fonction pour visualiser un ticket
            function viewTicket(ticketCode) {
                if (ticketCode) {
                    alert(`Visualisation du ticket: ${ticketCode}\n\nCette fonctionnalité pourrait ouvrir une fenêtre modale avec les détails du ticket ou générer un PDF.`);
                } else {
                    alert('Ce ticket n\'a pas de code associé.');
                }
            }
        </script>
        
        <script src="js/tickets.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="assets/demo/chart-pie-demo.js"></script>
    </body>
</html>