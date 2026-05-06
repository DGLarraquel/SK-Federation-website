<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth_sk_chair.php");
    exit;
}

require_once '../../connection.php';

$user_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// GET USER + BARANGAY INFO
try {
    $stmt = $pdo->prepare("
        SELECT u.firstname, u.middlename, u.surname, u.profile_pic, 
               b.id AS barangay_id, b.name AS barangay_name
        FROM users u
        LEFT JOIN barangays b ON u.barangay = b.name
        WHERE u.id = ? AND u.role = 'sk_chairperson'
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();

    if (!$user_data || !$user_data['barangay_id']) {
        die("<h2>Error: Your account is not properly linked to a barangay. Contact administrator.</h2>");
    }

    $firstname     = $user_data['firstname'] ?? 'SK';
    $fullname      = trim($user_data['firstname'] . ' ' . ($user_data['middlename'] ? $user_data['middlename'][0].'. ' : '') . $user_data['surname']);
    $barangay_id   = $user_data['barangay_id'];
    $barangay_name = $user_data['barangay_name'] ?? 'Unknown Barangay';

    $profile_pic = "../../images/default-sk-avatar.png";
    if (!empty($user_data['profile_pic'])) {
        $path = "../../images/profiles/" . ltrim($user_data['profile_pic'], '/');
        if (file_exists($path)) $profile_pic = $path;
    }
} catch (Exception $e) {
    die("Database connection failed.");
}

// STATS
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE submitted_by = ? AND barangay_id = ? AND status = 'approved'");
    $stmt->execute([$user_id, $barangay_id]);
    $my_published = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE barangay_id = ? AND status = 'approved'");
    $stmt->execute([$barangay_id]);
    $barangay_published = (int)$stmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM events 
        WHERE barangay_id = ? 
          AND MONTH(event_date) = MONTH(CURDATE()) 
          AND YEAR(event_date) = YEAR(CURDATE())
    ");
    $stmt->execute([$barangay_id]);
    $events_this_month = (int)$stmt->fetchColumn();
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $my_published = $barangay_published = $events_this_month = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard • SK <?= htmlspecialchars($firstname) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="header">
        <h1>Welcome, <?= htmlspecialchars($firstname) ?>!</h1>
        <p>SK Chairperson • Barangay <?= htmlspecialchars($barangay_name) ?></p>
    </div>

    <div class="profile-card">
        <h2><?= htmlspecialchars($fullname) ?></h2>
        <div class="role">SK Chairperson</div>
    </div>

    <div class="section-title">My Published Projects</div>
    <div class="stats-grid my-stats">
        <div class="stat-card">
            <i class="fas fa-check-circle"></i>
            <div class="num"><?= $my_published ?></div>
            <div class="label">Published Projects</div>
        </div>
    </div>

    <div class="section-title">Barangay <?= htmlspecialchars($barangay_name) ?> Overview</div>
    <div class="stats-grid barangay-stats">
        <div class="stat-card">
            <i class="fas fa-project-diagram"></i>
            <div class="num"><?= $barangay_published ?></div>
            <div class="label">Published Projects</div>
        </div>
        <div class="stat-card">
            <i class="fas fa-calendar-check"></i>
            <div class="num"><?= $events_this_month ?></div>
            <div class="label">Events This Month</div>
        </div>
    </div>
</div>

</body>
</html>