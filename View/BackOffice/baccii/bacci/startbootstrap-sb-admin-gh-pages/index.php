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
        <style>
            :root {
                --primary-color: #FF0055; /* Rouge néon */
                --secondary-color: #FF2A7F; 
                --accent-color: #00F0FF; /* Bleu néon */
                --dark-color: #0F0F1B; /* Fond sombre */
                --light-color: #1E1E3A;
                --neon-green: #00FFAA;
                --neon-purple: #A83AFB;
            }
            
            body {
                font-family: 'Poppins', sans-serif;
                background-color: var(--dark-color);
                color: white;
            }
            
            .sb-topnav {
                background-color: rgba(15, 15, 27, 0.9) !important;
                backdrop-filter: blur(10px);
                border-bottom: 1px solid rgba(255, 0, 85, 0.3);
            }
            
            .navbar-brand {
                font-weight: 700;
                font-size: 1.5rem;
                color: white !important;
                text-shadow: 0 0 10px rgba(255, 0, 85, 0.7);
            }
            
            .navbar-brand span {
                color: var(--accent-color);
                text-shadow: 0 0 10px rgba(0, 240, 255, 0.7);
            }
            
            .sb-sidenav {
                background-color: var(--dark-color);
                border-right: 1px solid rgba(255, 0, 85, 0.2);
            }
            
            .sb-sidenav .nav-link {
                color: rgba(255, 255, 255, 0.7);
            }
            
            .sb-sidenav .nav-link:hover {
                color: white;
                text-shadow: 0 0 5px var(--primary-color);
            }
            
            .sb-sidenav .nav-link .sb-nav-link-icon {
                color: var(--accent-color);
            }
            
            .sb-sidenav-footer {
                background-color: rgba(255, 0, 85, 0.1);
                color: white;
                border-top: 1px solid rgba(255, 0, 85, 0.2);
            }
            
            .card {
                border: none;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(255, 0, 85, 0.1);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
                background-color: var(--light-color);
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
            
            .card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(255, 0, 85, 0.3);
                border: 1px solid rgba(255, 0, 85, 0.3);
            }
            
            .card-header {
                background-color: var(--light-color);
                border-bottom: 1px solid rgba(255, 0, 85, 0.2);
                font-weight: 600;
                color: white;
            }
            
            .stat-card {
                border-left: 4px solid;
                border-radius: 8px;
            }
            
            .stat-card.primary {
                border-left-color: var(--primary-color);
                background: linear-gradient(135deg, rgba(255, 0, 85, 0.2), rgba(255, 42, 127, 0.2));
                color: white;
                position: relative;
                overflow: hidden;
            }
            
            .stat-card.primary::before {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(
                    to bottom right,
                    transparent,
                    transparent,
                    transparent,
                    rgba(255, 0, 85, 0.1)
                );
                transform: rotate(30deg);
                animation: shine 3s infinite;
            }
            
            .stat-card.warning {
                border-left-color: var(--neon-green);
                background: linear-gradient(135deg, rgba(0, 255, 170, 0.2), rgba(0, 200, 150, 0.2));
                color: white;
            }
            
            .stat-card.success {
                border-left-color: var(--accent-color);
                background: linear-gradient(135deg, rgba(0, 240, 255, 0.2), rgba(0, 200, 255, 0.2));
                color: white;
            }
            
            .stat-card.danger {
                border-left-color: var(--neon-purple);
                background: linear-gradient(135deg, rgba(168, 58, 251, 0.2), rgba(140, 30, 255, 0.2));
                color: white;
            }
            
            .stat-card .card-title {
                font-size: 1rem;
                font-weight: 500;
                opacity: 0.9;
            }
            
            .stat-card .card-value {
                font-size: 1.8rem;
                font-weight: 600;
                margin: 10px 0;
                text-shadow: 0 0 10px currentColor;
            }
            
            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
                color: white;
                font-weight: 600;
                text-shadow: 0 0 5px rgba(255, 0, 85, 0.5);
                box-shadow: 0 0 10px rgba(255, 0, 85, 0.5);
                transition: all 0.3s ease;
            }
            
            .btn-primary:hover {
                background-color: #E0004D;
                border-color: #E0004D;
                box-shadow: 0 0 15px rgba(255, 0, 85, 0.8);
                transform: translateY(-2px);
            }
            
            .news-flash {
                background: linear-gradient(135deg, var(--primary-color), var(--neon-purple));
                color: white;
                border-radius: 10px;
                padding: 20px;
                margin-bottom: 30px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                box-shadow: 0 0 20px rgba(255, 0, 85, 0.5);
                position: relative;
                overflow: hidden;
            }
            
            .news-flash::after {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: linear-gradient(
                    to bottom right,
                    transparent,
                    transparent,
                    transparent,
                    rgba(255, 255, 255, 0.1)
                );
                transform: rotate(30deg);
                animation: shine 3s infinite;
            }
            
            @keyframes shine {
                0% { transform: rotate(30deg) translate(-10%, -10%); }
                100% { transform: rotate(30deg) translate(10%, 10%); }
            }
            
            .news-flash h2 {
                font-weight: 700;
                margin-bottom: 10px;
                text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
                position: relative;
                z-index: 2;
            }
            
            .news-flash p {
                font-size: 1.1rem;
                margin-bottom: 20px;
                position: relative;
                z-index: 2;
            }
            
            .btn-explore {
                background-color: white;
                color: var(--primary-color);
                font-weight: 600;
                border-radius: 50px;
                padding: 8px 20px;
                transition: all 0.3s ease;
                position: relative;
                z-index: 2;
                border: none;
            }
            
            .btn-explore:hover {
                background-color: rgba(255, 255, 255, 0.9);
                color: var(--primary-color);
                box-shadow: 0 0 15px rgba(255, 255, 255, 0.5);
                transform: translateY(-2px);
            }
            
            /* New styles for music distribution */
            .genre-distribution {
                display: flex;
                justify-content: space-between;
                margin-top: 15px;
            }
            
            .genre-tag {
                padding: 5px 10px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 500;
                color: white;
                text-shadow: 0 0 5px currentColor;
                box-shadow: 0 0 5px currentColor;
            }
            
            .activity-chart {
                height: 200px;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }
            
            .activity-labels {
                display: flex;
                justify-content: space-between;
                margin-top: 10px;
                font-size: 0.8rem;
                color: rgba(255, 255, 255, 0.7);
            }
            
            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
            }
            
            ::-webkit-scrollbar-track {
                background: var(--dark-color);
            }
            
            ::-webkit-scrollbar-thumb {
                background: var(--primary-color);
                border-radius: 10px;
            }
            
            /* Table styles */
            table {
                color: white;
                border-color: rgba(255, 255, 255, 0.1);
            }
            
            table thead th {
                background-color: rgba(255, 0, 85, 0.1);
                color: white;
                border-bottom: 2px solid var(--primary-color);
            }
            
            table tbody tr {
                background-color: rgba(30, 30, 58, 0.5);
            }
            
            table tbody tr:hover {
                background-color: rgba(255, 0, 85, 0.1);
            }
            
            .badge {
                font-weight: 600;
                text-shadow: none;
            }
            
            .badge.bg-success {
                background-color: var(--neon-green) !important;
                color: #111;
            }
            
            .badge.bg-warning {
                background-color: var(--accent-color) !important;
                color: #111;
            }
            
            .badge.bg-danger {
                background-color: var(--primary-color) !important;
            }
        </style>
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
                            <a class="nav-link" href="gestion_user.html">
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
                                    <a class="nav-link" href="lieuxxx.php">Lieux</a>
                                    <a class="nav-link" href="concerttt.php">Concerts</a>
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
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Music Overview</li>
                        </ol>
                        
                        <!-- News Flash Section -->
                        <div class="news-flash mb-4">
                            <h2>NEWS FLASH</h2>
                            <p>EXCLUSIVE PRE-SALE FOR OUR TOP LISTENERS STARTS TODAY!</p>
                            <div>
                                <a href="#" class="btn btn-explore me-2">View Concerts</a>
                                <a href="#" class="btn btn-explore">Get Tickets</a>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="stat-card primary text-white mb-4">
                                    <div class="card-body">
                                        <div class="card-title">UPCOMING EVENTS</div>
                                        <div class="card-value">24</div>
                                        <div class="small">View Details <i class="fas fa-angle-right ms-1"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="stat-card warning mb-4">
                                    <div class="card-body">
                                        <div class="card-title">FRIENDS ONLINE</div>
                                        <div class="card-value">15</div>
                                        <div class="small">View Details <i class="fas fa-angle-right ms-1"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="stat-card success text-white mb-4">
                                    <div class="card-body">
                                        <div class="card-title">NEW MESSAGES</div>
                                        <div class="card-value">8</div>
                                        <div class="small">View Details <i class="fas fa-angle-right ms-1"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="stat-card danger text-white mb-4">
                                    <div class="card-body">
                                        <div class="card-title">ACHIEVEMENTS</div>
                                        <div class="card-value">12</div>
                                        <div class="small">View Details <i class="fas fa-angle-right ms-1"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-music me-1"></i>
                                        MUSIC GENRE DISTRIBUTION
                                    </div>
                                    <div class="card-body">
                                        <div class="genre-distribution">
                                            <span class="genre-tag" style="background-color: var(--primary-color);">Pop</span>
                                            <span class="genre-tag" style="background-color: var(--neon-purple);">Rock</span>
                                            <span class="genre-tag" style="background-color: var(--neon-green);">Hip-Hop</span>
                                            <span class="genre-tag" style="background-color: var(--accent-color);">Electronic</span>
                                            <span class="genre-tag" style="background-color: #FF5500;">Jazz</span>
                                            <span class="genre-tag" style="background-color: #FF00AA;">Classical</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-headphones me-1"></i>
                                        LISTENING ACTIVITY (LAST 30 DAYS)
                                    </div>
                                    <div class="card-body">
                                        <div class="activity-chart">
                                            <!-- This would be replaced with an actual chart in production -->
                                            <div style="height: 70%; display: flex; align-items: flex-end;">
                                                <div style="width: 10%; height: 75%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 65%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 80%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 60%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 45%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 70%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 85%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 90%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 75%; background-color: var(--primary-color); margin-right: 2%;"></div>
                                                <div style="width: 10%; height: 65%; background-color: var(--primary-color);"></div>
                                            </div>
                                            <div class="activity-labels">
                                                <span>1 Dec</span>
                                                <span>5 Dec</span>
                                                <span>10 Dec</span>
                                                <span>15 Dec</span>
                                                <span>20 Dec</span>
                                                <span>25 Dec</span>
                                                <span>30 Dec</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-calendar-alt me-1"></i>
                                UPCOMING CONCERTS
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Event</th>
                                            <th>Artist</th>
                                            <th>Venue</th>
                                            <th>Date</th>
                                            <th>Your Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Summer Beats Festival</td>
                                            <td>Various Artists</td>
                                            <td>Central Park</td>
                                            <td>2023-07-15</td>
                                            <td><span class="badge bg-success">Going</span></td>
                                            <td><button class="btn btn-sm btn-primary">Invite Friends</button></td>
                                        </tr>
                                        <tr>
                                            <td>Electric Dreams Tour</td>
                                            <td>The Neon Lights</td>
                                            <td>Madison Square Garden</td>
                                            <td>2023-08-02</td>
                                            <td><span class="badge bg-warning">Interested</span></td>
                                            <td><button class="btn btn-sm btn-outline-primary">Get Tickets</button></td>
                                        </tr>
                                        <tr>
                                            <td>Jazz Nights</td>
                                            <td>Blue Note Quartet</td>
                                            <td>Blue Note Club</td>
                                            <td>2023-06-28</td>
                                            <td><span class="badge bg-secondary" style="background-color: var(--neon-purple) !important; color: white;">Not Attending</span></td>
                                            <td><button class="btn btn-sm btn-outline-secondary" style="border-color: var(--neon-purple); color: var(--neon-purple);">Learn More</button></td>
                                        </tr>
                                        <tr>
                                            <td>Rock Revolution</td>
                                            <td>The Wild Strings</td>
                                            <td>Barclays Center</td>
                                            <td>2023-09-12</td>
                                            <td><span class="badge bg-success">Going</span></td>
                                            <td><button class="btn btn-sm btn-primary">Invite Friends</button></td>
                                        </tr>
                                        <tr>
                                            <td>Pop Sensation Live</td>
                                            <td>Stella Moon</td>
                                            <td>Radio City Music Hall</td>
                                            <td>2023-07-22</td>
                                            <td><span class="badge bg-danger">Sold Out</span></td>
                                            <td><button class="btn btn-sm btn-outline-danger" style="border-color: var(--primary-color); color: var(--primary-color);" disabled>Waitlist</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 mt-auto" style="background-color: var(--dark-color); border-top: 1px solid rgba(255, 0, 85, 0.3);">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; LiveTheMusic 2023</div>
                            <div>
                                <a href="#" style="color: rgba(255, 255, 255, 0.7);">Privacy Policy</a>
                                &middot;
                                <a href="#" style="color: rgba(255, 255, 255, 0.7);">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>