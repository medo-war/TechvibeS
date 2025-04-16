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
            <a class="navbar-brand ps-3" href="index.html">LIVE<span>THE</span>MUSIC</a>
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
                            <a class="nav-link" href="artists.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Artistes
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

                            <a class="nav-link" href="tickets.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-ticket-alt"></i></div>
                                Tickets
                            </a>
                            <div class="sb-sidenav-menu-heading">Community</div>
                            <a class="nav-link" href="friends.html">
                                <div class="sb-nav-link-icon"><i class="fas fa-user-friends"></i></div>
                                Friends
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
require_once 'C:\xampp\htdocs\front\artcont.php'; // Make sure this points to the correct path

// Create an instance of the ArtistController
$artistController = new ArtistController();

// Fetch the list of artists using the getArtists() function
$artists = $artistController->getArtists();

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
        <button class="btn-action edit" title="Edit">
            <a href="../edit_artist.php?id=<?= $artist['id']; ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                <i class="fas fa-edit"></i>
            </a>
            </button>
        <form method="POST" action="../delete_artist.php" onsubmit="return confirm('Are you sure you want to delete this artist?');">
        <input type="hidden" name="id" value="<?= htmlspecialchars($artist['id']); ?>">
        <button type="submit"  class="btn-action " title="Delete">
            <i class="fas fa-trash-alt"></i>
        </button>
    </form>


                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
// Optionally close the connection or handle any cleanup here if needed
?>
<!-- Modal structure for editing artist information -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editForm" method="POST" action="../edit_artist.php" onsubmit="return validateForm()">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel" style="color: black;">Edit Artist</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <input type="hidden" name="id" id="editId">

          <div class="mb-3">
            <label for="editName" class="form-label" style="color: black;">Name</label>
            <input type="text" class="form-control" id="editName" name="name" >
          </div>

          <div class="mb-3">
            <label for="editUsername" class="form-label" style="color: black;">Username</label>
            <input type="text" class="form-control" id="editUsername" name="username" >
          </div>

          <div class="mb-3">
            <label for="editGroup" class="form-label" style="color: black;">Group Name</label>
            <input type="text" class="form-control" id="editGroup" name="group_name">
          </div>

          <div class="mb-3">
            <label for="editGenre" class="form-label" style="color: black;">Genre</label>
            <input type="text" class="form-control" id="editGenre" name="genre" >
          </div>

          <div class="mb-3">
            <label for="editCountry" class="form-label" style="color: black;">Country</label>
            <input type="text" class="form-control" id="editCountry" name="country" >
          </div>

          <div class="mb-3">
            <label for="editBio" class="form-label" style="color: black;">Bio</label>
            <textarea class="form-control" id="editBio" name="bio" rows="3"></textarea>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save changes</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addForm" method="POST" action="../add_artist.php" onsubmit="return validateAddForm()">
        <div class="modal-header">
          <h5 class="modal-title" id="addModalLabel" style="color: black;">Add Artist</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">

          <div class="mb-3">
            <label for="addName" class="form-label" style="color: black;">Name</label>
            <input type="text" class="form-control" id="addName" name="name" required>
          </div>

          <div class="mb-3">
            <label for="addUsername" class="form-label" style="color: black;">Username</label>
            <input type="text" class="form-control" id="addUsername" name="username" required>
          </div>

          <div class="mb-3">
            <label for="addGroup" class="form-label" style="color: black;">Group Name</label>
            <input type="text" class="form-control" id="addGroup" name="group_name" required>
          </div>

          <div class="mb-3">
            <label for="addGenre" class="form-label" style="color: black;">Genre</label>
            <input type="text" class="form-control" id="addGenre" name="genre" required>
          </div>

          <div class="mb-3">
            <label for="addCountry" class="form-label" style="color: black;">Country</label>
            <input type="text" class="form-control" id="addCountry" name="country" required>
          </div>

          <div class="mb-3">
            <label for="addBio" class="form-label" style="color: black;">Bio</label>
            <textarea class="form-control" id="addBio" name="bio" rows="3" required></textarea>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Add Artist</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>




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
        <script src="js/user.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="assets/demo/chart-pie-demo.js"></script>
    </body>
</html>
