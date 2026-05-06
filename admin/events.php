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
   2. EMBEDDED DB CONNECTION (PDO)
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
    error_log("Events DB Error: " . $e->getMessage());
    die("Database connection failed. Contact developer.");
}

/* -------------------------------------------------
   3. HANDLE APPROVE ACTION
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'approve') {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("UPDATE events SET status = 'approved', approved_at = NOW() WHERE id = ? AND (status != 'approved' OR approved_at IS NULL)");
    $stmt->execute([$id]);
    $_SESSION['msg'] = "Event approved & published to website!";
    header("Location: events.php");
    exit;
}

/* -------------------------------------------------
   4. FETCH ALL EVENTS WITH IMAGE
   ------------------------------------------------- */
$stmt = $pdo->query("SELECT * FROM events ORDER BY submitted_at DESC");
$events = $stmt->fetchAll();

/* -------------------------------------------------
   5. FILTER BY STATUS
   ------------------------------------------------- */
$status_filter = $_GET['status'] ?? '';
$filtered_events = $status_filter
    ? array_filter($events, fn($e) => ($e['status'] ?? '') === $status_filter)
    : $events;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events – Admin Panel</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .tabs { margin: 20px 0; display: flex; gap: 10px; flex-wrap: wrap; }
        .tab { padding: 10px 20px; background: #ecf0f1; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; }
        .tab.active { background: #3498db; color: white; }
        .tab:hover { background: #d6dbdf; }
        .tab.active:hover { background: #2980b9; }
        .event-img { width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; border: 2px solid #ddd; }
        .no-img { background: #f1f1f1; display: flex; align-items: center; justify-content: center; color: #999; font-size: 0.8em; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 9999; justify-content: center; align-items: center; }
        .modal img { max-width: 90%; max-height: 90%; border-radius: 12px; }
        .close-modal { position: absolute; top: 20px; right: 30px; font-size: 40px; color: white; cursor: pointer; }
    </style>
</head>
<body>
    <nav class="sidebar">
        <div class="sidebar-logo">
            <img src="../images/sk-logo.png" alt="SK Logo">
            <span>SK Federation</span>
        </div>
        <ul>
            <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i><span>Dashboard</span>
            </a></li>
            <li><a href="projects.php" class="<?= basename($_SERVER['PHP_SELF']) === 'projects.php' ? 'active' : '' ?>">
                <i class="fas fa-project-diagram"></i><span>Projects</span>
            </a></li>
            <li><a href="events.php" class="active">
                <i class="fas fa-calendar-alt"></i><span>Events</span>
            </a></li>
            <li><a href="members.php" class="<?= basename($_SERVER['PHP_SELF']) === 'members.php' ? 'active' : '' ?>">
                <i class="fas fa-users"></i><span>Members</span>
            </a></li>
            <li><a href="changepassword.php" class="<?= basename($_SERVER['PHP_SELF']) === 'changepassword.php' ? 'active' : '' ?>">
                <i class="fas fa-key"></i><span>Change Password</span>
            </a></li>
            <li><a href="logout.php">
                <i class="fas fa-sign-out-alt"></i><span>Logout</span>
            </a></li>
        </ul>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="header">
                <h1>Events Management</h1>
            </div>

            <?php if (isset($_SESSION['msg'])): ?>
                <div style="background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin-bottom:20px;">
                    <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
                </div>
            <?php endif; ?>

            <!-- Tabs -->
            <div class="tabs">
                <button class="tab <?= $status_filter === '' ? 'active' : '' ?>" onclick="location.href='?status='">All</button>
                <button class="tab <?= $status_filter === 'Proposed' ? 'active' : '' ?>" onclick="location.href='?status=Proposed'">Proposed</button>
                <button class="tab <?= $status_filter === 'approved' ? 'active' : '' ?>" onclick="location.href='?status=approved'">Approved</button>
                <button class="tab <?= $status_filter === 'Ongoing' ? 'active' : '' ?>" onclick="location.href='?status=Ongoing'">Ongoing</button>
                <button class="tab <?= $status_filter === 'Completed' ? 'active' : '' ?>" onclick="location.href='?status=Completed'">Completed</button>
            </div>

            <div class="card">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Barangay</th>
                            <th>Venue</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($filtered_events)): ?>
                            <tr><td colspan="8" style="text-align:center;color:#777;">No events found.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($filtered_events as $e): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($e['image']) && file_exists("../uploads/events/" . $e['image'])): ?>
                                        <img src="../uploads/events/<?= htmlspecialchars($e['image']) ?>" alt="Event" class="event-img" onclick="openModal('../uploads/events/<?= htmlspecialchars($e['image']) ?>')">
                                    <?php else: ?>
                                        <div class="event-img no-img">No Image</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($e['title']) ?></td>
                                <td><?= htmlspecialchars($e['barangay']) ?></td>
                                <td><?= htmlspecialchars($e['venue']) ?></td>
                                <td><?= date('M d, Y', strtotime($e['event_date'])) ?></td>
                                <td>
                                    <span style="color: <?= 
                                        $e['status'] === 'approved' ? '#27ae60' : 
                                        ($e['status'] === 'Ongoing' ? '#f39c12' : 
                                        ($e['status'] === 'Completed' ? '#27ae60' : '#e74c3c')) 
                                    ?>; font-weight:bold;">
                                        <?= ucfirst($e['status'] ?? 'Proposed') ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($e['submitted_at'])) ?></td>
                                <td>
                                    <?php if ($e['status'] !== 'approved'): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?= $e['id'] ?>">
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="btn btn-success">Approve & Publish</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color:#27ae60;font-weight:bold;">Live on Site</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imgModal" class="modal" onclick="this.style.display='none'">
        <span class="close-modal">×</span>
        <img id="modalImg" src="" alt="Full Event Image">
    </div>

    <script>
        function openModal(src) {
            document.getElementById('modalImg').src = src;
            document.getElementById('imgModal').style.display = 'flex';
        }
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>