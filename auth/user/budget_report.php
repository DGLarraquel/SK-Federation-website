<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/auth_sk_chair.php");
    exit;
}

require_once '../../connection.php';

$user_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// 1. GET USER + BARANGAY INFO
try {
    $stmt = $pdo->prepare("
        SELECT u.firstname, u.profile_pic, 
                b.id AS barangay_id, b.name AS barangay_name
        FROM users u
        LEFT JOIN barangays b ON u.barangay = b.name
        WHERE u.id = ?
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();

    if (!$user_data) {
        die("<h2>Error: Account not found.</h2>");
    }

    $firstname     = $user_data['firstname'] ?? 'SK';
    $barangay_id   = $user_data['barangay_id'] ?? 0;
    $barangay_name = $user_data['barangay_name'] ?? 'Unknown Barangay';

    $profile_pic = "../../images/default-sk-avatar.png";
    if (!empty($user_data['profile_pic'])) {
        $path = "../../images/profiles/" . ltrim($user_data['profile_pic'], '/');
        if (file_exists($path)) $profile_pic = $path;
    }
} catch (Exception $e) {
    die("Database connection failed.");
}

// Block non-admin users without barangay_id
if (!$is_admin && !$barangay_id) {
    die("<h2>Error: No barangay assigned. Contact admin.</h2>");
}

// 2. FETCH PROJECTS (Dynamic Fetching - No Hardcoding)
try {
    if ($is_admin) {
        $stmt = $pdo->prepare("
            SELECT p.id, p.title, p.description, p.budget, p.status,
                   p.submitted_at, p.approved_at, p.file_path, b.name AS barangay_name
            FROM projects p
            LEFT JOIN barangays b ON p.barangay_id = b.id
            ORDER BY p.approved_at DESC, p.submitted_at DESC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT id, title, description, budget, status,
                   submitted_at, approved_at, file_path
            FROM projects 
            WHERE barangay_id = ?
            ORDER BY approved_at DESC, submitted_at DESC
        ");
        $stmt->execute([$barangay_id]);
    }

    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_budget = 0;
    $approved_count = 0;
    foreach ($projects as $p) {
        $total_budget += (float)($p['budget'] ?? 0);
        if ($p['status'] === 'approved') $approved_count++;
    }
} catch (Exception $e) {
    error_log("Budget report error: " . $e->getMessage());
    $projects = [];
    $total_budget = 0;
    $approved_count = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget & Liquidation Report • SK <?= htmlspecialchars($firstname) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
    <style>
        :root {
            --navy: #1e293b;
            --gray: #64748b;
            --border: #e2e8f0;
            --success: #22c55e;
            --warning: #f59e0b;
        }
        .report-header { text-align: center; margin-bottom: 2.5rem; }
        .report-stats { display: flex; flex-wrap: wrap; gap: 1.5rem; justify-content: center; margin-bottom: 3rem; }
        .stat-box { background: white; padding: 1.8rem 2.5rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); text-align: center; min-width: 220px; }
        .stat-box .number { font-size: 2.8rem; font-weight: 800; color: var(--navy); margin-bottom: 0.5rem; }
        .stat-box .label { color: var(--gray); font-weight: 500; }
        .project-table { width: 100%; border-collapse: collapse; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .project-table th, .project-table td { padding: 1.2rem 1.5rem; text-align: left; border-bottom: 1px solid var(--border); }
        .project-table th { background: var(--navy); color: white; font-weight: 600; }
        .project-table tr:hover { background: #f8fafc; }
        .status-live { color: var(--success); font-weight: 600; }
        .status-pending { color: var(--warning); font-weight: 600; }
        .no-data { text-align: center; padding: 4rem; color: var(--gray); font-style: italic; }
        
        .view-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            background-color: var(--navy);
            color: #fff !important;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .view-btn:hover {
            background-color: #334155;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="report-header">
        <h1 class="page-title">Budget & Liquidation Report</h1>
        <p class="subtitle">
            Barangay <?= htmlspecialchars($barangay_name) ?> 
            <?= $is_admin ? '(Admin View - All Barangays)' : '' ?>
        </p>
    </div>

    <div class="report-stats">
        <div class="stat-box">
            <div class="number"><?= count($projects) ?></div>
            <div class="label">Total Projects</div>
        </div>
        <div class="stat-box">
            <div class="number"><?= $approved_count ?></div>
            <div class="label">Approved / Live</div>
        </div>
        <div class="stat-box">
            <div class="number">₱<?= number_format($total_budget, 2) ?></div>
            <div class="label">Total Budget</div>
        </div>
    </div>

    <?php if (empty($projects)): ?>
        <div class="no-data">
            <i class="fas fa-folder-open fa-3x" style="margin-bottom:1rem; opacity:0.6;"></i><br>
            No projects found.
        </div>
    <?php else: ?>
        <table class="project-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <?php if ($is_admin): ?><th>Barangay</th><?php endif; ?>
                    <th>Budget</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>Approved</th>
                    <th>Documents</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($projects as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['title']) ?></td>
                        <?php if ($is_admin): ?>
                            <td><?= htmlspecialchars($p['barangay_name'] ?? '—') ?></td>
                        <?php endif; ?>
                        <td>₱<?= number_format($p['budget'] ?? 0, 2) ?></td>
                        <td class="status-<?= $p['status'] === 'approved' ? 'live' : 'pending' ?>">
                            <?= ucfirst($p['status'] ?? 'Pending') ?>
                        </td>
                        <td><?= $p['submitted_at'] ? date('M j, Y', strtotime($p['submitted_at'])) : '—' ?></td>
                        <td><?= $p['approved_at'] ? date('M j, Y', strtotime($p['approved_at'])) : '—' ?></td>
                        <td>
                            <?php if (!empty($p['file_path'])): ?>
                                <?php 
                                    $file = htmlspecialchars($p['file_path']);
                                    $file_url = "../../uploads/projects/" . $file; 
                                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    
                                    // Automatic icon selection
                                    $icon = "fa-file";
                                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) $icon = "fa-file-image";
                                    elseif ($ext === 'pdf') $icon = "fa-file-pdf";
                                    elseif (in_array($ext, ['doc', 'docx'])) $icon = "fa-file-word";
                                ?>
                                <a href="<?= $file_url ?>" target="_blank" class="view-btn">
                                    <i class="fas <?= $icon ?>"></i> View
                                </a>
                            <?php else: ?>
                                <span style="color: #cbd5e1; font-size: 0.85rem;">No files</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>