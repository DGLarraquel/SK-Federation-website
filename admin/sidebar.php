<!-- sidebar.php -->
<nav class="sidebar">
    <div class="sidebar-logo">
        <img src="../images/sk-logo.png" alt="SK Logo">
        <span>SK Federation</span>
    </div>
    <ul>
        <li>
            <a href="dashboard.php" <?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="cms_home.php" <?= basename($_SERVER['PHP_SELF']) === 'cms_home.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-home"></i><span>Manage Homepage</span>
            </a>
        </li>
        <li>
            <a href="cms_about.php" <?= basename($_SERVER['PHP_SELF']) === 'cms_about.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-info-circle"></i><span>Manage About & Officers</span>
            </a>
        </li>
        <li>
            <a href="cms_projects.php" <?= basename($_SERVER['PHP_SELF']) === 'cms_projects.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-project-diagram"></i><span>Manage Projects</span>
            </a>
        </li>
        <li>
            <a href="cms_barangays.php" <?= basename($_SERVER['PHP_SELF']) === 'cms_barangays.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-city"></i><span>Manage Barangays</span>
            </a>
        </li>
        <li>
            <a href="cms_contact.php" <?= basename($_SERVER['PHP_SELF']) === 'cms_contact.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-envelope"></i><span>Manage Contact</span>
            </a>
        </li>
        <li>
            <a href="members.php" <?= basename($_SERVER['PHP_SELF']) === 'members.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-users"></i><span>Manage Members</span>
            </a>
        </li>
        <li>
            <a href="changepassword.php" <?= basename($_SERVER['PHP_SELF']) === 'changepassword.php' ? 'class="active"' : '' ?>>
                <i class="fas fa-key"></i><span>Change Password</span>
            </a>
        </li>
        <li>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a>
        </li>
    </ul>
</nav>