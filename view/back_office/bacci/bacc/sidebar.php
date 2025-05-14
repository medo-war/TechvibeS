<?php
// Determine current page to set active classes
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Core</div>
                <a class="nav-link <?php echo ($current_page == 'index1.php') ? 'active' : ''; ?>" href="index1.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                    Dashboard
                </a>
                <div class="sb-sidenav-menu-heading">Music</div>
                <a class="nav-link <?php echo ($current_page == 'gestion_user1.php') ? 'active' : ''; ?>" href="gestion_user1.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                    Users
                </a>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvents" aria-expanded="<?php echo (in_array($current_page, ['lieuxxx.php', 'concerttt.php'])) ? 'true' : 'false'; ?>" aria-controls="collapseEvents">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Events
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php echo (in_array($current_page, ['lieuxxx.php', 'concerttt.php'])) ? 'show' : ''; ?>" id="collapseEvents" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?php echo ($current_page == 'lieuxxx.php') ? 'active' : ''; ?>" href="lieuxxx.php">Lieux</a>
                        <a class="nav-link <?php echo ($current_page == 'concerttt.php') ? 'active' : ''; ?>" href="concerttt.php">Concerts</a>
                    </nav>
                </div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseartist" aria-expanded="<?php echo (in_array($current_page, ['gestion_group.php', 'gestion_user.php'])) ? 'true' : 'false'; ?>" aria-controls="collapseEvents">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Artist
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php echo (in_array($current_page, ['gestion_group.php', 'gestion_user.php'])) ? 'show' : ''; ?>" id="collapseartist" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?php echo ($current_page == 'gestion_group.php') ? 'active' : ''; ?>" href="gestion_group.php">Group</a>
                        <a class="nav-link <?php echo ($current_page == 'gestion_user.php') ? 'active' : ''; ?>" href="gestion_user.php">Artist</a>
                    </nav>
                </div>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#ticketcollapse" aria-expanded="<?php echo (in_array($current_page, ['gestion_ticket.php', 'gestion_purchased_ticket.php'])) ? 'true' : 'false'; ?>" aria-controls="collapseEvents">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Tickets
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse <?php echo (in_array($current_page, ['gestion_ticket.php', 'gestion_purchased_ticket.php'])) ? 'show' : ''; ?>" id="ticketcollapse" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link <?php echo ($current_page == 'gestion_ticket.php') ? 'active' : ''; ?>" href="gestion_ticket.php">Ticket</a>
                        <a class="nav-link <?php echo ($current_page == 'gestion_purchased_ticket.php') ? 'active' : ''; ?>" href="gestion_purchased_ticket.php">Purchase</a>
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
                
                <div class="sb-sidenav-menu-heading">Partnerships</div>
                <?php
                // Get pending applications count
                $pendingCount = 0;
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php')) {
                    require_once $_SERVER['DOCUMENT_ROOT'] . '/livethemusic/Controller/BackOfficePartnerController.php';
                    try {
                        $partnerController = new BackOfficePartnerController();
                        $applications = $partnerController->getPartnerApplications();
                        $pendingCount = count($applications);
                    } catch (Exception $e) {
                        // Silently handle any errors
                    }
                }
                ?>
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePartners" aria-expanded="false" aria-controls="collapsePartners">
                    <div class="sb-nav-link-icon"><i class="fas fa-handshake"></i></div>
                    Partners
                    <?php if ($pendingCount > 0): ?>
                    <span class="notification-badge" style="position: absolute; top: 10px; right: 30px; background: var(--primary-color); color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 10px rgba(255, 0, 85, 0.7);">!</span>
                    <?php endif; ?>
                    <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                </a>
                <div class="collapse" id="collapsePartners" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                    <nav class="sb-sidenav-menu-nested nav">
                        <a class="nav-link" href="partner/index.php">View Partners</a>
                        <a class="nav-link position-relative" href="partner/applications.php">
                            Manage Applications
                            <?php if ($pendingCount > 0): ?>
                            <span class="notification-count" style="position: absolute; top: 2px; right: 5px; background: var(--primary-color); color: white; border-radius: 50%; min-width: 18px; height: 18px; font-size: 11px; display: flex; align-items: center; justify-content: center; padding: 0 4px; box-shadow: 0 0 10px rgba(255, 0, 85, 0.7);"><?php echo $pendingCount; ?></span>
                            <?php endif; ?>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
        <div class="sb-sidenav-footer">
            <div class="small">Logged in as:</div>
            Music Lover
        </div>
    </nav>
</div>
