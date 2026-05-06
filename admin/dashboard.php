<?php
/* -------------------------------------------------
   1. SAFE SESSION + AUTH
   ------------------------------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

/* -------------------------------------------------
   2. DB CONNECTION
   ------------------------------------------------- */
$servername = "localhost";
$username   = "u601734414_sk_user";
$password   = "Federation2025";
$database   = "u601734414_sk_federation";

$dsn = "mysql:host=$servername;dbname=$database;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("Dashboard DB Error: " . $e->getMessage());
    die("Database connection failed.");
}

/* -------------------------------------------------
   3. FETCH PROJECT + MEMBERS STATS
   ------------------------------------------------- */
$stats = [
    'projects_total'     => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn() ?? 0,
    'projects_approved'  => $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'approved'")->fetchColumn() ?? 0,
    'projects_pending'   => $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'pending'")->fetchColumn() ?? 0,
    'active_members'     => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'sk_chairperson' AND is_active = 1")->fetchColumn() ?? 0,
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard – SK Admin</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #16213e;
            --blue: #1e40af;
            --light: #f8f9fa;
            --white: #ffffff;
            --gray: #6c757d;
            --border: #dee2e6;
        }

        body { 
            background: var(--light); 
            font-family: 'Poppins', sans-serif; 
            color: #2d3748; 
            margin: 0;
        }

        /* WELCOME HEADER */
        .welcome-header {
            text-align: center; 
            margin: 20px 0 30px;
            padding: 15px 0;
            border-bottom: 1px solid var(--border);
        }
        .welcome-header h1 {
            font-family: 'Playfair Display', serif; 
            font-size: 2.5rem;
            color: var(--navy); 
            margin: 0; 
            font-weight: 700;
        }
        .welcome-header p { 
            color: var(--gray); 
            font-size: 1rem;
            margin: 8px 0 0; 
        }

        /* STAT CARDS GRID */
        .dashboard-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); 
            gap: 20px; 
            margin: 20px 0;
        }
        .stat-card {
            background: var(--white); 
            padding: 28px 20px; 
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06); 
            border: 1px solid var(--border);
            text-align: center; 
            transition: all .3s ease;
        }
        .stat-card:hover {
            transform: translateY(-6px); 
            box-shadow: 0 10px 24px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 2.4rem; 
            color: var(--navy); 
            margin-bottom: 14px;
        }
        .stat-number {
            font-size: 2.4rem; 
            font-weight: 700; 
            color: var(--navy); 
            margin: 0;
        }
        .stat-label {
            color: var(--gray); 
            font-size: 1rem; 
            margin-top: 8px; 
            font-weight: 500;
        }

        /* QUICK ACTIONS */
        .quick-actions {
            margin-top: 40px; 
            display: flex; 
            flex-wrap: wrap; 
            gap: 16px; 
            justify-content: center;
        }
        .action-btn {
            background: var(--white); 
            color: var(--navy); 
            border: 2px solid var(--navy);
            padding: 14px 28px; 
            border-radius: 50px; 
            font-weight: 600; 
            font-size: 1rem;
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 10px;
            transition: all .3s ease; 
            box-shadow: 0 4px 12px rgba(26,33,62,0.08);
        }
        .action-btn:hover {
            background: var(--navy); 
            color: white; 
            transform: translateY(-4px);
            box-shadow: 0 10px 24px rgba(26,33,62,0.18);
        }

        @media (max-width: 768px) {
            .welcome-header h1 { font-size: 2.1rem; }
            .stat-card { padding: 24px 18px; }
            .stat-number { font-size: 2rem; }
            .action-btn { padding: 12px 24px; font-size: 0.95rem; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container">

        <!-- WELCOME -->
        <div class="welcome-header">
            <h1>Welcome <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></h1>
            <p>Manage SK Federation operations efficiently.</p>
        </div>

        <!-- STATS GRID – only projects + active members -->
        <div class="dashboard-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-project-diagram"></i></div>
                <div class="stat-number"><?= $stats['projects_total'] ?></div>
                <div class="stat-label">Total Projects</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number"><?= $stats['projects_approved'] ?></div>
                <div class="stat-label">Approved & Live</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-clock"></i></div>
                <div class="stat-number"><?= $stats['projects_pending'] ?></div>
                <div class="stat-label">Pending Approval</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-number"><?= $stats['active_members'] ?></div>
                <div class="stat-label">Active Members</div>
            </div>
        </div>

        <!-- QUICK ACTIONS -->
        <div class="quick-actions">
            <a href="cms_home.php" class="action-btn">
                <i class="fas fa-home"></i> Edit Homepage
            </a>
            <a href="cms_projects.php?status=pending" class="action-btn">
                <i class="fas fa-tasks"></i> Review Pending Projects
            </a>
            <a href="cms_about.php" class="action-btn">
                <i class="fas fa-users-cog"></i> Manage Officers
            </a>
            <a href="members.php" class="action-btn">
                <i class="fas fa-users"></i> Members
            </a>
        </div>

    </div>
</div>

</body>
</html>