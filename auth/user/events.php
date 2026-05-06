<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/auth_sk_chair.php");
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
        die("<h2>Error: Account not linked to a valid barangay.</h2>");
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
    die("Database connection failed.");
}

// FETCH EVENTS
$filter_status = $_GET['status'] ?? 'all';
$valid_status = ['all', 'upcoming', 'ongoing', 'completed'];
$filter_status = in_array($filter_status, $valid_status) ? $filter_status : 'all';

$sql = "SELECT id, title, description, image, event_date, status, venue, submitted_at 
        FROM events 
        WHERE barangay_id = ?";

$params = [$barangay_id];

if ($filter_status !== 'all') {
    $sql .= " AND status = ?";
    $params[] = $filter_status;
}

$sql .= " ORDER BY event_date DESC, submitted_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Events fetch error: " . $e->getMessage());
    $events = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events • SK <?= htmlspecialchars($firstname) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h1 class="page-title">My Events</h1>
    <p class="subtitle">Barangay <strong><?= htmlspecialchars($barangay_name) ?></strong></p>

    <div class="filters">
        <a href="?status=all" class="<?= $filter_status==='all'?'active':'' ?>">All</a>
        <a href="?status=upcoming" class="<?= $filter_status==='upcoming'?'active':'' ?>">Upcoming</a>
        <a href="?status=ongoing" class="<?= $filter_status==='ongoing'?'active':'' ?>">Ongoing</a>
        <a href="?status=completed" class="<?= $filter_status==='completed'?'active':'' ?>">Completed</a>
    </div>

    <a href="upload_event.php" class="add-event-btn" title="Add New Event">
        <i class="fas fa-plus"></i>
    </a>

    <div class="event-grid">
        <?php if (empty($events)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>No events found</h3>
                <p>You haven't created any events yet.</p>
                <a href="upload_event.php" style="color:var(--blue);font-weight:600;">Create Your First Event</a>
            </div>
        <?php else: foreach ($events as $e): ?>
            <div class="event-card">
                <?php 
                $image_path = !empty($e['image']) ? '../../' . $e['image'] : '';
                if ($image_path && file_exists($image_path)): 
                ?>
                    <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($e['title']) ?>" class="event-img">
                <?php else: ?>
                    <div class="event-img-placeholder">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                <?php endif; ?>

                <div class="event-body">
                    <h3 class="event-title"><?= htmlspecialchars($e['title']) ?></h3>
                    
                    <?php if (!empty($e['venue'])): ?>
                        <span class="event-venue">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($e['venue']) ?>
                        </span>
                    <?php endif; ?>

                    <p class="event-desc">
                        <?= htmlspecialchars(strlen($e['description']) > 130 
                            ? substr($e['description'], 0, 130).'...' 
                            : ($e['description'] ?: 'No description.')
                        ) ?>
                    </p>

                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:1rem;">
                        <span class="status-badge status-<?= htmlspecialchars($e['status']) ?>">
                            <?= ucfirst($e['status']) ?>
                        </span>
                        <small class="date">
                            <i class="fas fa-calendar-day"></i>
                            <?= date('M j, Y', strtotime($e['event_date'])) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

</body>
</html>