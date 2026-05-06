<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth_sk_chair.php"); 
    exit;
}

require_once '../../connection.php';

$user_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// Fetch user profile for sidebar
$stmt = $pdo->prepare("SELECT firstname, profile_pic FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$firstname   = $user['firstname'] ?? 'SK';
$profile_pic = !empty($user['profile_pic']) && file_exists("../../images/profiles/".$user['profile_pic'])
    ? "../../images/profiles/".$user['profile_pic'] : "../../images/default-sk-avatar.png";

// Fetch council members
$stmt = $pdo->prepare("SELECT id, name, role, image FROM sk_council_members WHERE barangay_id = ? ORDER BY sort_order ASC, id ASC");
$stmt->execute([$_SESSION['barangay_id']]);
$council = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Council • <?= htmlspecialchars($firstname) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
    <style>
        /* Page-specific overrides for officers layout */
        .page-title {
            font-size: 2.6rem;
            margin-bottom: 0.6rem;
        }
        .page-subtitle {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
        }
        .header-actions {
            text-align: center;
            margin-bottom: 3rem;
        }
        .btn-add {
            padding: 0.9rem 2rem;
            background: var(--blue);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-add:hover {
            background: var(--blue-dark);
            transform: translateY(-2px);
        }
        .council-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin-top: 1rem;
        }
        .member-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            text-align: center;
        }
        .member-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.12);
        }
        .member-img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            margin: 2.5rem auto 1.5rem;
            border: 6px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .member-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 0.6rem;
        }
        .member-role {
            font-size: 1.1rem;
            color: var(--blue);
            font-weight: 600;
            background: rgba(30,64,175,0.08);
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 999px;
            margin-bottom: 1.8rem;
        }
        .card-actions {
            padding: 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: center;
            gap: 1.2rem;
        }
        .action-btn {
            padding: 0.7rem 1.6rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .edit-btn {
            background: #f59e0b;
            color: white;
        }
        .delete-btn {
            background: var(--danger);
            color: white;
        }
        .action-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }
        .empty-state {
            text-align: center;
            padding: 8rem 2rem;
            color: var(--gray);
            font-size: 1.3rem;
        }
        .empty-state i {
            font-size: 7rem;
            color: #e2e8f0;
            margin-bottom: 1.5rem;
            display: block;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.7);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 2.8rem;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        }
        .modal h3 {
            font-size: 1.8rem;
            color: var(--navy);
            margin-bottom: 2rem;
            text-align: center;
        }
        .modal label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 600;
            color: var(--navy);
        }
        .modal input[type="text"],
        .modal input[type="file"] {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        .modal .btn-group {
            display: flex;
            gap: 1.2rem;
            margin-top: 2.5rem;
        }
        .modal .btn-group button {
            flex: 1;
            padding: 1.1rem;
        }
        .modal .btn.cancel {
            background: #e2e8f0;
            color: #475569;
        }

        @media (max-width: 768px) {
            .council-grid { grid-template-columns: 1fr; }
            .main-content { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h1 class="page-title">SK Council Members</h1>
    <p class="page-subtitle">Barangay <?= htmlspecialchars($_SESSION['barangay_name'] ?? 'Unknown') ?></p>

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= htmlspecialchars($_SESSION['msg']) ?>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <div class="header-actions">
        <button class="btn btn-add" onclick="openModal()">
            <i class="fas fa-plus"></i> Add Council Member
        </button>
    </div>

    <?php if (empty($council)): ?>
        <div class="empty-state">
            <i class="fas fa-users-slash"></i>
            <h3>No council members added yet</h3>
            <p>Your SK Council team is empty. Start by adding members.</p>
            <button class="btn btn-add" onclick="openModal()" style="margin-top:1.5rem;">
                Add First Member
            </button>
        </div>
    <?php else: ?>
        <div class="council-grid">
            <?php foreach ($council as $m): 
                $img = !empty($m['image']) && file_exists("../../images/officers/".$m['image']) 
                    ? "../../images/officers/".$m['image'] : "../../images/default-officer.png";
            ?>
                <div class="member-card">
                    <img src="<?= $img ?>" alt="<?= htmlspecialchars($m['name']) ?>" class="member-img">
                    <div class="member-name"><?= htmlspecialchars($m['name']) ?></div>
                    <div class="member-role"><?= htmlspecialchars($m['role']) ?></div>
                    <div class="card-actions">
                        <button class="action-btn edit-btn" 
                                onclick="editMember(<?= $m['id'] ?>, 
                                '<?= htmlspecialchars(addslashes($m['name']), ENT_QUOTES) ?>', 
                                '<?= htmlspecialchars(addslashes($m['role']), ENT_QUOTES) ?>', 
                                '<?= htmlspecialchars(addslashes($m['image'] ?? ''), ENT_QUOTES) ?>')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="action-btn delete-btn" onclick="deleteMember(<?= $m['id'] ?>)">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL -->
<div id="memberModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Add Council Member</h3>
        <form action="save_council.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="memberId">
            <input type="hidden" name="old_image" id="oldImage">

            <label for="name">Full Name</label>
            <input type="text" name="name" id="name" required>

            <label for="role">Position / Role</label>
            <input type="text" name="role" id="role" required placeholder="e.g. SK Chairperson, Secretary, Treasurer">

            <label for="image">Photo (optional)</label>
            <input type="file" name="image" id="image" accept="image/*">

            <div class="btn-group">
                <button type="submit" class="btn">Save Member</button>
                <button type="button" class="btn cancel" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('memberModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = 'Add Council Member';
    document.querySelector('#memberModal form').reset();
    document.getElementById('memberId').value = '';
    document.getElementById('oldImage').value = '';
}

function editMember(id, name, role, image) {
    document.getElementById('memberModal').style.display = 'flex';
    document.getElementById('modalTitle').textContent = 'Edit Council Member';
    document.getElementById('memberId').value = id;
    document.getElementById('name').value = name;
    document.getElementById('role').value = role;
    document.getElementById('oldImage').value = image;
}

function closeModal() {
    document.getElementById('memberModal').style.display = 'none';
}

function deleteMember(id) {
    if (confirm('Are you sure you want to delete this council member?')) {
        window.location.href = 'delete_council.php?id=' + id;
    }
}

window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) closeModal();
});
</script>

</body>
</html>