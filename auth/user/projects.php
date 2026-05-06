<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/auth_sk_chair.php");
    exit;
}

require_once '../../connection.php';

$user_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Get user & barangay info for sidebar
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

    if (!$user_data || !$user_data['barangay_id']) {
        die("<h2 style='text-align:center;padding:4rem;'>Error: Your account is not linked to a valid barangay. Contact admin.</h2>");
    }

    $firstname     = $user_data['firstname'] ?? 'SK';
    $barangay_id   = $user_data['barangay_id'];
    $barangay_name = $user_data['barangay_name'] ?? 'Unknown Barangay';

    $profile_pic = "../../images/default-sk-avatar.png";
    if (!empty($user_data['profile_pic'])) {
        $path = "../../images/profiles/" . ltrim($user_data['profile_pic'], '/');
        if (file_exists($path)) $profile_pic = $path;
    }
} catch (Exception $e) {
    die("<h2 style='text-align:center;padding:4rem;'>Database error. Please try again later.</h2>");
}

// Fetch approved projects
try {
    $stmt = $pdo->prepare("
        SELECT id, title, description, image, status, 
               submitted_at, approved_at, budget
        FROM projects
        WHERE submitted_by = ?
          AND barangay_id = ?
          AND status = 'approved'
        ORDER BY approved_at DESC, submitted_at DESC
    ");
    $stmt->execute([$user_id, $barangay_id]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Projects fetch error: " . $e->getMessage());
    $projects = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Published Projects • SK <?= htmlspecialchars($firstname) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h1 class="page-title">My Published Projects</h1>
    <p class="subtitle">Barangay <strong><?= htmlspecialchars($barangay_name) ?></strong></p>

    <a href="upload.php" class="add-project-btn" title="Submit New Project">
        <i class="fas fa-plus"></i>
    </a>

    <?php if (empty($projects)): ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h3>No published projects yet</h3>
            <p>Your approved projects will appear here once reviewed.</p>
            <a href="upload.php" style="color:var(--blue); font-weight:600; text-decoration:none;">Submit your first project →</a>
        </div>
    <?php else: ?>
        <div class="project-grid">
            <?php foreach ($projects as $p): ?>
                <div class="project-card">
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= htmlspecialchars($p['image']) ?>" 
                             alt="<?= htmlspecialchars($p['title']) ?>" 
                             class="project-img"
                             onerror="this.src='../../images/placeholder-project.jpg'; this.onerror=null;">
                    <?php else: ?>
                        <div class="project-img-placeholder">
                            <i class="fas fa-image"></i>
                        </div>
                    <?php endif; ?>

                    <div class="project-body">
                        <h3 class="project-title"><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="project-desc">
                            <?= htmlspecialchars(
                                strlen($p['description']) > 140 
                                    ? substr($p['description'], 0, 140) . '...' 
                                    : ($p['description'] ?: 'No description provided.')
                            ) ?>
                        </p>

                        <div class="meta-row">
                            <span class="status-badge">Published</span>
                            <div style="text-align:right;">
                                <small class="date">
                                    Approved <?= date('M j, Y', strtotime($p['approved_at'])) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>