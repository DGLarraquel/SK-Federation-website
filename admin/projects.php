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
   2. EMBEDDED DB CONNECTION
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
    error_log("Projects DB Error: " . $e->getMessage());
    die("Database connection failed.");
}

/* -------------------------------------------------
   3. HANDLE ACTIONS
   ------------------------------------------------- */
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];

    if ($_POST['action'] === 'approve') {
        $stmt = $pdo->prepare("UPDATE projects SET status = 'approved', approved_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Project approved & LIVE on website!";
    }

    if ($_POST['action'] === 'edit') {
        $title       = trim($_POST['title']);
        $barangay_id = (int)$_POST['barangay_id'];
        $description = $_POST['description'];
        $status      = strtolower(trim($_POST['status']));

        $imageName = $_POST['existing_image'] ?? '';
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "../uploads/projects/";
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $imageName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                if ($_POST['existing_image'] && file_exists($targetDir . $_POST['existing_image'])) {
                    unlink($targetDir . $_POST['existing_image']);
                }
            }
        }

        $stmt = $pdo->prepare("
            UPDATE projects
            SET title = ?, barangay_id = ?, description = ?, status = ?, image = ?
            WHERE id = ?
        ");
        $stmt->execute([$title, $barangay_id, $description, $status, $imageName, $id]);
        $msg = "Project updated successfully!";
    }

    $_SESSION['msg'] = $msg;
    header("Location: projects.php?" . ($_GET['status'] ?? ''));
    exit;
}

/* -------------------------------------------------
   4. FETCH PROJECTS + BARANGAY NAME
   ------------------------------------------------- */
$sql = "
    SELECT p.*,
           b.name AS barangay_name
    FROM   projects p
    LEFT   JOIN barangays b ON p.barangay_id = b.id
    ORDER  BY p.submitted_at DESC
";
$stmt     = $pdo->query($sql);
$projects = $stmt->fetchAll();

$status_filter = $_GET['status'] ?? '';
$filtered_projects = $status_filter
    ? array_filter($projects, fn($p) => strtolower($p['status'] ?? '') === strtolower($status_filter))
    : $projects;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Projects – Admin Panel</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .tabs { margin:20px 0; display:flex; gap:10px; flex-wrap:wrap; }
        .tab { padding:10px 20px; background:#ecf0f1; border:none; border-radius:8px; cursor:pointer; font-weight:bold; }
        .tab.active { background:#3498db; color:#fff; }
        .btn-small { padding:8px 12px; margin:2px; font-size:0.9em; border:none; border-radius:6px; cursor:pointer; }
        .btn-success { background:#27ae60; color:#fff; }
        .btn-warning { background:#f39c12; color:#fff; }
        .icon-btn i { color:#fff !important; }
        .modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999; justify-content:center; align-items:center; }
        .modal-content { background:#fff; padding:30px; width:500px; max-height:90vh; overflow-y:auto; border-radius:12px; position:relative; }
        .close { position:absolute; top:10px; right:15px; font-size:28px; cursor:pointer; color:#aaa; }
        .close:hover { color:#000; }
        .form-group { margin:15px 0; }
        .form-group label { display:block; margin-bottom:5px; font-weight:bold; }
        .form-group input, .form-group textarea, .form-group select { width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; }
        .current-img { max-width:100%; margin:10px 0; border-radius:8px; }
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
        <li><a href="projects.php" class="active">
            <i class="fas fa-project-diagram"></i><span>Projects</span>
        </a></li>
        <li><a href="events.php" class="<?= basename($_SERVER['PHP_SELF']) === 'events.php' ? 'active' : '' ?>">
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
        <div class="header"><h1>Projects Management</h1></div>

        <?php if (isset($_SESSION['msg'])): ?>
            <div style="background:#d4edda;color:#155724;padding:15px;border-radius:8px;margin-bottom:20px;font-weight:bold;">
                <?= $_SESSION['msg']; unset($_SESSION['msg']); ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab <?= $status_filter === '' ? 'active' : '' ?>" onclick="location.href='?status='">All</button>
            <button class="tab <?= strtolower($status_filter) === 'pending' ? 'active' : '' ?>" onclick="location.href='?status=pending'">Pending</button>
            <button class="tab <?= strtolower($status_filter) === 'approved' ? 'active' : '' ?>" onclick="location.href='?status=approved'">Approved</button>
            <button class="tab <?= strtolower($status_filter) === 'ongoing' ? 'active' : '' ?>" onclick="location.href='?status=ongoing'">Ongoing</button>
            <button class="tab <?= strtolower($status_filter) === 'completed' ? 'active' : '' ?>" onclick="location.href='?status=completed'">Completed</button>
        </div>

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Barangay</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($filtered_projects)): ?>
                        <tr><td colspan="5" style="text-align:center;color:#777;">No projects found.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($filtered_projects as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['title']) ?></td>
                            <td><?= htmlspecialchars($p['barangay_name'] ?? '—') ?></td>
                            <td>
                                <span style="color: <?= 
                                    strtolower($p['status'] ?? '') === 'approved' ? '#27ae60' : 
                                    (strtolower($p['status'] ?? '') === 'ongoing' ? '#f39c12' : 
                                    (strtolower($p['status'] ?? '') === 'completed' ? '#27ae60' : '#e74c3c')) ?>; font-weight:bold;">
                                    <?= ucfirst($p['status'] ?? 'Pending') ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($p['submitted_at'] ?? 'now')) ?></td>
                            <td>
                                <!-- APPROVE BUTTON: Show only if NOT approved -->
                                <?php if (strtolower($p['status'] ?? '') !== 'approved'): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="btn-small btn-success">
                                            Approve & Publish
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color:#27ae60;font-weight:bold;">LIVE on Site</span>
                                <?php endif; ?>

                                <!-- EDIT BUTTON -->
                                <button onclick="openEditModal(<?= $p['id'] ?>, <?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)"
                                        class="btn-small btn-warning icon-btn" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('editModal').style.display='none'">×</span>
        <h3>Edit Project</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" id="editId">
            <input type="hidden" name="existing_image" id="existingImage">

            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" id="editTitle" required>
            </div>

            <div class="form-group">
                <label>Barangay</label>
                <select name="barangay_id" id="editBarangayId" required>
                    <option value="">— Select Barangay —</option>
                    <?php
                    $bStmt = $pdo->query("SELECT id, name FROM barangays ORDER BY name");
                    foreach ($bStmt->fetchAll() as $b) {
                        echo "<option value=\"{$b['id']}\">{$b['name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="editDesc" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="editStatus">
                    <option value="pending">Pending</option>
                    <option value="approved">Approved (Live)</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <div class="form-group">
                <label>Current Image</label>
                <img id="currentImg" src="" class="current-img" style="display:none;">
                <p id="noImg" style="color:#777;">No image uploaded</p>
            </div>

            <div class="form-group">
                <label>Replace Image (optional)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <button type="submit" class="btn-success" style="width:100%;padding:12px;margin-top:10px;">Save Changes</button>
        </form>
    </div>
</div>

<script>
function openEditModal(id, data) {
    document.getElementById('editId').value = id;
    document.getElementById('editTitle').value = data.title || '';
    document.getElementById('editBarangayId').value = data.barangay_id || '';
    document.getElementById('editDesc').value = data.description || '';
    document.getElementById('editStatus').value = data.status || 'pending';
    document.getElementById('existingImage').value = data.image || '';

    const img = document.getElementById('currentImg');
    const noImg = document.getElementById('noImg');
    if (data.image && data.image.trim() !== '') {
        img.src = '../uploads/projects/' + data.image;
        img.style.display = 'block';
        noImg.style.display = 'none';
    } else {
        img.style.display = 'none';
        noImg.style.display = 'block';
    }

    document.getElementById('editModal').style.display = 'flex';
}
window.onclick = function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
}
</script>
</body>
</html>