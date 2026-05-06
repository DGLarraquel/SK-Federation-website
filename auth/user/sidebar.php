<nav class="sidebar">
    <div class="sidebar-logo">
        <img src="<?= htmlspecialchars($profile_pic ?? '../../images/default-sk-avatar.png') ?>" alt="Profile">
    </div>
    <ul class="nav-menu">
        <li><a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
        <li><a href="projects.php" class="<?= $current_page === 'projects.php' ? 'active' : '' ?>"><i class="fas fa-project-diagram"></i><span>Projects</span></a></li>
        <li><a href="events.php" class="<?= $current_page === 'events.php' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i><span>Events</span></a></li>
        <li><a href="officers.php" class="<?= $current_page === 'officers.php' ? 'active' : '' ?>"><i class="fas fa-users"></i><span>Council</span></a></li>
        <li><a href="upload.php" class="<?= $current_page === 'upload.php' ? 'active' : '' ?>"><i class="fas fa-upload"></i><span>Upload Project</span></a></li>
        <li><a href="budget_report.php" class="<?= $current_page === 'budget_report.php' ? 'active' : '' ?>"><i class="fas fa-file-invoice-dollar"></i><span>Budget / Liquidation</span></a></li>
        <li><a href="changepassword.php" class="<?= $current_page === 'changepassword.php' ? 'active' : '' ?>"><i class="fas fa-key"></i><span>Change Password</span></a></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
    </ul>
</nav>