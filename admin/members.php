<?php
// ────────────────────────────────────────────────
// 1. SESSION & ADMIN CHECK
// ────────────────────────────────────────────────
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

// ────────────────────────────────────────────────
// 2. DATABASE CONNECTION
// ────────────────────────────────────────────────
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
    error_log("Members DB Error: " . $e->getMessage());
    die("<div style='color:red; text-align:center; padding:2rem;'><h2>Database connection failed.</h2></div>");
}

// ────────────────────────────────────────────────
// 3. HANDLE POST ACTIONS
// ────────────────────────────────────────────────
$msg_type = 'success';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'toggle_status') {
            $current = (int)($_POST['current_status'] ?? 0);
            $newStatus = $current ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE users SET is_active = ? WHERE id = ? AND role = 'sk_chairperson'");
            $stmt->execute([$newStatus, $id]);
            $msg = $newStatus ? "Member activated." : "Member deactivated.";
        }

        elseif ($_POST['action'] === 'change_password') {
            $newPass = trim($_POST['new_password'] ?? '');
            if (strlen($newPass) < 6) {
                $msg = "Password must be at least 6 characters.";
                $msg_type = 'error';
            } else {
                $hashed = password_hash($newPass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $id]);
                $msg = "Password updated successfully.";
            }
        }

        elseif ($_POST['action'] === 'delete' && isset($_POST['confirm_delete'])) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'sk_chairperson'");
            $stmt->execute([$id]);
            $msg = "Member permanently deleted.";
            $msg_type = 'warning';
        }
    }

    $_SESSION['flash_msg']  = $msg;
    $_SESSION['flash_type'] = $msg_type;
    header("Location: members.php" . (isset($_GET['status']) ? "?status={$_GET['status']}" : ""));
    exit;
}

// ────────────────────────────────────────────────
// 4. FETCH & FILTER MEMBERS
// ────────────────────────────────────────────────
$stmt = $pdo->prepare("
    SELECT id, email,
           CONCAT(firstname, ' ', COALESCE(middlename, ''), ' ', surname) AS full_name,
           barangay, is_active
    FROM users
    WHERE role = 'sk_chairperson'
    ORDER BY barangay ASC, full_name ASC
");
$stmt->execute();
$all_members = $stmt->fetchAll();

$status_filter = $_GET['status'] ?? 'All';
$members = $all_members;

if ($status_filter === 'Active') {
    $members = array_filter($all_members, fn($m) => $m['is_active']);
} elseif ($status_filter === 'Deactivated') {
    $members = array_filter($all_members, fn($m) => !$m['is_active']);
}

$total       = count($all_members);
$active      = count(array_filter($all_members, fn($m) => $m['is_active']));
$deactivated = $total - $active;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members – SK Admin</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy: #16213e;
            --blue: #1e40af;
            --success: #198754;
            --warning: #e67e22;
            --danger: #dc3545;
            --light: #f8f9fa;
            --gray: #6c757d;
            --border: #dee2e6;
        }

        body { background: var(--light); font-family: 'Poppins', sans-serif; margin:0; color:#333; }
        .main-content { margin-left:260px; padding:2rem 1.5rem; min-height:100vh; }
        .container { max-width:1300px; margin:0 auto; }

        h1 { color:var(--navy); margin-bottom:0.5rem; font-weight:700; }

        .flash {
            padding:1rem 1.5rem;
            border-radius:8px;
            margin-bottom:1.5rem;
            font-weight:500;
            display:flex;
            align-items:center;
            gap:12px;
        }
        .flash-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
        .flash-error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
        .flash-warning { background:#fff3cd; color:#856404; border:1px solid #ffeeba; }

        .stats-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:1.2rem;
            margin:1.8rem 0 2.2rem;
        }
        .stat-card {
            background:white;
            border-radius:10px;
            padding:1.4rem;
            text-align:center;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
            transition:transform 0.2s;
        }
        .stat-card:hover { transform:translateY(-4px); }
        .stat-card h3 { margin:0 0 0.6rem; font-size:1rem; color:var(--gray); }
        .stat-card p { font-size:2.2rem; font-weight:700; margin:0; color:var(--navy); }

        .filter { margin-bottom:1.8rem; }
        .filter label { font-weight:600; margin-right:0.8rem; color:var(--navy); }
        .filter select {
            padding:0.6rem 1rem;
            border:1px solid var(--border);
            border-radius:6px;
            font-size:1rem;
        }

        .table-container { overflow-x:auto; border-radius:10px; box-shadow:0 2px 12px rgba(0,0,0,0.08); }
        table {
            width:100%;
            border-collapse:collapse;
            background:white;
        }
        th, td {
            padding:1rem 1.2rem;
            text-align:left;
            border-bottom:1px solid var(--border);
        }
        th {
            background:var(--navy);
            color:white;
            font-weight:600;
            text-transform:uppercase;
            font-size:0.9rem;
        }
        tr:hover { background:#f8fcff; }

        .status-badge {
            padding:0.35rem 0.9rem;
            border-radius:20px;
            font-size:0.85rem;
            font-weight:600;
        }
        .status-active   { background:#d4edda; color:#155724; }
        .status-inactive { background:#f8d7da; color:#721c24; }

        .action-btn {
            padding:0.5rem 0.9rem;
            border:none;
            border-radius:6px;
            cursor:pointer;
            font-size:0.95rem;
            transition:all 0.2s;
            margin:0 0.3rem;
        }
        .action-btn i { margin-right:0.4rem; }
        .btn-activate   { background:var(--success); color:white; }
        .btn-deactivate { background:var(--danger);   color:white; }
        .btn-password   { background:var(--warning);  color:white; }
        .btn-delete     { background:#dc3545; color:white; }

        .action-btn:hover { opacity:0.9; transform:translateY(-1px); }

        /* Modal */
        .modal {
            display:none;
            position:fixed;
            inset:0;
            background:rgba(0,0,0,0.65);
            z-index:2000;
            align-items:center;
            justify-content:center;
        }
        .modal-content {
            background:white;
            width:90%;
            max-width:460px;
            border-radius:12px;
            padding:2rem;
            box-shadow:0 15px 40px rgba(0,0,0,0.3);
            position:relative;
        }
        .close {
            position:absolute;
            top:1rem;
            right:1.5rem;
            font-size:2rem;
            cursor:pointer;
            color:#aaa;
        }
        .close:hover { color:#333; }

        @media (max-width:992px) {
            .main-content { margin-left:0; padding:1.5rem 1rem; }
            .stats-grid { grid-template-columns:1fr 1fr; }
        }

        @media (max-width:600px) {
            .action-btn { display:block; margin:0.5rem 0; width:100%; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container">

        <h1>Members Management</h1>
        <p style="color:var(--gray); margin-bottom:2rem;">SK Chairpersons (Barangay Level)</p>

        <?php if (isset($_SESSION['flash_msg'])): ?>
            <div class="flash flash-<?= $_SESSION['flash_type'] ?>">
                <i class="fas <?= $_SESSION['flash_type']==='success'?'fa-check-circle':($_SESSION['flash_type']==='error'?'fa-exclamation-circle':'fa-exclamation-triangle') ?>"></i>
                <?= htmlspecialchars($_SESSION['flash_msg']) ?>
            </div>
            <?php unset($_SESSION['flash_msg'], $_SESSION['flash_type']); ?>
        <?php endif; ?>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Members</h3>
                <p><?= $total ?></p>
            </div>
            <div class="stat-card">
                <h3>Active</h3>
                <p style="color:var(--success);"><?= $active ?></p>
            </div>
            <div class="stat-card">
                <h3>Deactivated</h3>
                <p style="color:var(--danger);"><?= $deactivated ?></p>
            </div>
        </div>

        <!-- Filter -->
        <div class="filter">
            <label>Show:</label>
            <select onchange="location.href='?status='+this.value">
                <option value="All"        <?= $status_filter==='All'        ? 'selected' : '' ?>>All</option>
                <option value="Active"     <?= $status_filter==='Active'     ? 'selected' : '' ?>>Active Only</option>
                <option value="Deactivated"<?= $status_filter==='Deactivated'? 'selected' : '' ?>>Deactivated Only</option>
            </select>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Full Name</th>
                        <th>Barangay</th>
                        <th>Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($members)): ?>
                        <tr><td colspan="5" style="text-align:center; padding:3rem; color:var(--gray); font-style:italic;">
                            No members found matching the filter.
                        </td></tr>
                    <?php else: foreach ($members as $m): ?>
                        <tr>
                            <td><?= htmlspecialchars($m['email']) ?></td>
                            <td><?= htmlspecialchars($m['full_name']) ?></td>
                            <td><?= htmlspecialchars($m['barangay'] ?: '—') ?></td>
                            <td>
                                <span class="status-badge <?= $m['is_active'] ? 'status-active' : 'status-inactive' ?>">
                                    <?= $m['is_active'] ? 'Active' : 'Deactivated' ?>
                                </span>
                            </td>
                            <td style="text-align:right; white-space:nowrap;">
                                <button class="action-btn <?= $m['is_active'] ? 'btn-deactivate' : 'btn-activate' ?>"
                                        onclick="toggleStatus(<?= $m['id'] ?>, <?= $m['is_active'] ?>)"
                                        title="<?= $m['is_active'] ? 'Deactivate' : 'Activate' ?>">
                                    <i class="fas fa-power-off"></i> <?= $m['is_active'] ? 'Deactivate' : 'Activate' ?>
                                </button>

                                <button class="action-btn btn-password"
                                        onclick="openPasswordModal(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['email'])) ?>')">
                                    <i class="fas fa-key"></i> Password
                                </button>

                                <button class="action-btn btn-delete"
                                        onclick="confirmDelete(<?= $m['id'] ?>, '<?= htmlspecialchars(addslashes($m['full_name'])) ?>')">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Password Modal -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('passwordModal')">×</span>
        <h2 style="margin-top:0; color:var(--navy);">Change Password</h2>
        <p><strong>User:</strong> <span id="modalEmail" style="color:var(--blue);"></span></p>
        <form method="post">
            <input type="hidden" name="action" value="change_password">
            <input type="hidden" name="id" id="modalUserId">
            <input type="password" name="new_password" placeholder="New password (min 6 characters)" required minlength="6" autocomplete="new-password">
            <div style="margin-top:1.5rem; text-align:right;">
                <button type="button" class="action-btn btn-secondary" onclick="closeModal('passwordModal')">Cancel</button>
                <button type="submit" class="action-btn btn-success">Update Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('deleteModal')">×</span>
        <h2 style="color:var(--danger); margin-top:0;">Delete Member</h2>
        <p>Are you sure you want to permanently delete <strong id="deleteName"></strong>?</p>
        <p style="color:var(--danger); font-weight:500;">This action cannot be undone.</p>
        <form method="post">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" id="deleteId">
            <input type="hidden" name="confirm_delete" value="1">
            <div style="margin-top:1.8rem; text-align:right;">
                <button type="button" class="action-btn btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                <button type="submit" class="action-btn btn-delete">Yes, Delete</button>
            </div>
        </form>
    </div>
</div>

<!-- Hidden toggle form -->
<form id="statusForm" method="post" style="display:none;">
    <input type="hidden" name="action" value="toggle_status">
    <input type="hidden" name="id" id="statusId">
    <input type="hidden" name="current_status" id="currentStatus">
</form>

<script>
function toggleStatus(id, current) {
    if (confirm("Are you sure you want to " + (current ? "deactivate" : "activate") + " this member?")) {
        document.getElementById('statusId').value = id;
        document.getElementById('currentStatus').value = current;
        document.getElementById('statusForm').submit();
    }
}

function openPasswordModal(id, email) {
    document.getElementById('modalUserId').value = id;
    document.getElementById('modalEmail').textContent = email;
    document.getElementById('passwordModal').style.display = 'flex';
}

function confirmDelete(id, name) {
    document.getElementById('deleteId').value = id;
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

window.onclick = function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
}

// Auto-dismiss flash after 4 seconds
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('.flash');
    if (flash) setTimeout(() => flash.style.opacity = '0', 4000);
});
</script>

</body>
</html>