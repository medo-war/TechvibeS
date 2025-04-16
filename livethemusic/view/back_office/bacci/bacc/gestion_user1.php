<?php
require_once __DIR__.'/../../../../Controller/userController.php';

$controller = new userController();
$users = $controller->getUsers();
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
                            <a class="nav-link" href="index.html">
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
                            <a class="nav-link" href="playlists.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-music"></i></div>
                                Artistes
                            </a>
                            <a class="nav-link" href="tickets.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-ticket-alt"></i></div>
                                Tickets
                            </a>
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
                <h2><i class="fas fa-users"></i> Gestion des Utilisateurs</h2>
                <div class="actions">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Rechercher utilisateur...">
                    </div>
                    <button class="btn btn-primary">
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
              <table class="users-table">
    <thead>
        <tr>
            <th class="checkbox-cell">
            <input type="checkbox">
            </th>
            <th>ID</th>
            <th>Image</th>
            <th>Nom Complet</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Mot de passe</th>
            <th>Rôle</th>
            <!--<th>Inscription</th>
            <th>Statut</th>-->
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($users) > 0): ?>
            <?php foreach ($users as $user): ?>
            <tr>
            <td class="checkbox-cell"><input type="checkbox"></td>
  
                <td><?= $user['id'] ?></td>
                <td>
    <div class="user-avatar">
        <img src="../../../../<?= htmlspecialchars($user['image']) ?>" alt="Image de <?= htmlspecialchars($user['first_name']) ?>">
    </div>
</td>

                <td><?= htmlspecialchars($user['first_name'].' '.$user['last_name']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['phone'] ?? 'N/A' ?></td>
                <td class="password-cell">
                    <span class="password-text"><?= $user['pwd'] ?></span>
                    <i class="fas fa-eye toggle-password"></i>
                </td>
                <td>
                    <span class="role-badge <?= strtolower($user['role']) ?>">
                        <?= ucfirst($user['role']) ?>
                    </span>
                </td>
               <!-- <td><?= $user['inscription_date'] ?></td>
                <td class="<?= $user['is_active'] ? 'active' : 'inactive' ?>">
                    <?= $user['is_active'] ? 'Actif' : 'Inactif' ?>
                </td>-->
                <td class="actions-cell">
                <td class="actions-cell">
    <a class="btn-action edit" title="Modifier" href="modifier_user.php?id=<?= $user['id'] ?>">
        <i class="fas fa-edit"></i>
    </a>
    <a class="btn-action delete" title="Supprimer" href="supprimer_user.php?id=<?= $user['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
        <i class="fas fa-trash"></i>
    </a>
    <a class="btn-action view" title="Voir détails" href="profile.php?id=<?= $user['id'] ?>">
        <i class="fas fa-eye"></i>
    </a>
</td>
                    <button class="btn-action view" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="10" style="text-align: center; color: red;">
                    Aucun utilisateur trouvé dans la base de données
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
            </div>

                <!--Pagination -->
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
        <script src="js/user.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="assets/demo/chart-pie-demo.js"></script>
    </body>
</html>
