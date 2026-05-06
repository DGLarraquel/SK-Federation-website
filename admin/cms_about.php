<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}

// Handle messages
$message = '';
$error = '';

// ── HANDLE ABOUT DESCRIPTION ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_description') {
    $description = trim($_POST['description'] ?? '');
    if ($description !== '') {
        $stmt = $pdo->prepare("UPDATE site_about SET description = ? WHERE id = 1");
        $stmt->execute([$description]);
        $message = "About description updated successfully!";
    } else {
        $error = "Description cannot be empty.";
    }
}

// ── HANDLE OFFICER ADD / EDIT / DELETE ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add', 'edit', 'delete'])) {
    $action = $_POST['action'];

    if ($action === 'add' || $action === 'edit') {
        $full_name  = trim($_POST['full_name'] ?? '');
        $position   = trim($_POST['position'] ?? '');
        $barangay   = trim($_POST['barangay'] ?? '');
        $officer_id = ($action === 'edit') ? (int)($_POST['id'] ?? 0) : 0;

        $photo_path = '';
        if (!empty($_FILES['photo']['name'])) {
            $upload_dir = '../images/officers/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            $file_name  = time() . '_centered.png';
            $target     = $upload_dir . $file_name;
            $temp_path  = $_FILES['photo']['tmp_name'];

            if (resizeAndCenterImage($temp_path, $target)) {
                $photo_path = 'images/officers/' . $file_name;
            } else {
                $error = "Failed to process uploaded image.";
            }
        }

        if ($full_name && $position && $barangay && !$error) {
            // ── CHECK FOR DUPLICATE POSITION ───────────────────────────────
            if ($action === 'add') {
                $check = $pdo->prepare("SELECT COUNT(*) FROM sk_officers WHERE position = ?");
                $check->execute([$position]);
                if ($check->fetchColumn() > 0) {
                    $error = "This position is already taken.";
                }
            } elseif ($action === 'edit' && $officer_id > 0) {
                $check = $pdo->prepare("SELECT COUNT(*) FROM sk_officers WHERE position = ? AND id != ?");
                $check->execute([$position, $officer_id]);
                if ($check->fetchColumn() > 0) {
                    $error = "This position is already taken by someone else.";
                }
            }

            if (!$error) {
                if ($action === 'add') {
                    $stmt = $pdo->prepare("INSERT INTO sk_officers (full_name, position, barangay, photo) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$full_name, $position, $barangay, $photo_path]);
                    $message = "Officer added successfully!";
                } elseif ($action === 'edit') {
                    if ($photo_path) {
                        $stmt = $pdo->prepare("UPDATE sk_officers SET full_name=?, position=?, barangay=?, photo=? WHERE id=?");
                        $stmt->execute([$full_name, $position, $barangay, $photo_path, $officer_id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE sk_officers SET full_name=?, position=?, barangay=? WHERE id=?");
                        $stmt->execute([$full_name, $position, $barangay, $officer_id]);
                    }
                    $message = "Officer updated successfully!";
                }
            }
        } elseif (!$error) {
            $error = "Please fill all required fields.";
        }
    }

    if ($action === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("SELECT photo FROM sk_officers WHERE id = ?");
        $stmt->execute([$id]);
        $old_photo = $stmt->fetchColumn();
        if ($old_photo && file_exists('../' . $old_photo)) {
            unlink('../' . $old_photo);
        }

        $stmt = $pdo->prepare("DELETE FROM sk_officers WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Officer deleted successfully!";
    }
}

// ── FETCH CURRENT DESCRIPTION ───────────────────────────────────────
$desc_stmt = $pdo->query("SELECT description FROM site_about WHERE id = 1");
$about_desc = $desc_stmt->fetchColumn() ?: '';

// ── FETCH ALL OFFICERS ──────────────────────────────────────────────
$officers = $pdo->query("SELECT * FROM sk_officers ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// ── FETCH ALL UNIQUE POSITIONS FROM DATABASE FOR DROPDOWN ───────────
$positions_stmt = $pdo->query("SELECT DISTINCT position FROM sk_officers ORDER BY position ASC");
$all_positions = $positions_stmt->fetchAll(PDO::FETCH_COLUMN);

// ── GET CURRENTLY OCCUPIED POSITIONS ────────────────────────────────
$occupied_stmt = $pdo->query("SELECT position FROM sk_officers");
$occupied_positions = $occupied_stmt->fetchAll(PDO::FETCH_COLUMN);
$occupied = array_flip($occupied_positions);

// ── BARANGAY LIST ───────────────────────────────────────────────────
$barangays = [
    "Anilao","Atlag","Babatnin","Bagna","Bagong Bayan","Balayong","Balite","Bangkal",
    "Barihan","Bulihan","Bungahan","Caingin","Calero","Caliligawan","Canalate","Caniogan",
    "Catmon","Cofradia","Dakila","Guinhawa","Ligas","Liang","Longos","Look 1st","Look 2nd",
    "Lugam","Mabolo","Mambog","Masile","Matimbo","Mojon","Namayan","Niugan","Pamarawan",
    "Panasahan","Pinagbakahan","San Agustin","San Gabriel","San Juan","San Pablo","San Vicente",
    "Santiago","Santor","Santisima Trinidad","Sto. Cristo","Sto. Niño","Santo Rosario",
    "Sumapang Bata","Sumapang Matanda","Taal","Tikay"
];

// ── IMAGE RESIZE FUNCTION ───────────────────────────────────────────
function resizeAndCenterImage($sourcePath, $targetPath, $size = 400) {
    if (!extension_loaded('gd')) return false;
    list($width, $height, $type) = getimagesize($sourcePath);
    if ($type !== IMAGETYPE_JPEG && $type !== IMAGETYPE_PNG) return false;

    $src = ($type === IMAGETYPE_JPEG) ? imagecreatefromjpeg($sourcePath) : imagecreatefrompng($sourcePath);
    if (!$src) return false;

    $dst = imagecreatetruecolor($size, $size);
    $ratio = max($size / $width, $size / $height);
    $newWidth  = $width * $ratio;
    $newHeight = $height * $ratio;
    $x = ($newWidth - $size) / 2;
    $y = ($newHeight - $size) / 2;

    imagecopyresampled($dst, $src, -$x, -$y, 0, 0, $newWidth, $newHeight, $width, $height);
    imagepng($dst, $targetPath, 8);
    imagedestroy($src);
    imagedestroy($dst);
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About & Officers – SK Admin</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy: #16213e;
            --blue: #1e40af;
            --light: #f8f9fa;
            --gray: #6c757d;
            --border: #dee2e6;
            --success: #198754;
            --danger: #dc3545;
        }
        body { background: var(--light); font-family: 'Poppins', sans-serif; color: #2d3748; margin: 0; }
        .main-content { margin-left: 260px; padding: 2rem 1.5rem; min-height: 100vh; }
        .container { max-width: 1100px; margin: 0 auto; }
        h1, h2 { color: var(--navy); font-weight: 700; }
        .muted { color: var(--gray); margin-bottom: 2rem; }
        .alert { padding: 1rem 1.5rem; border-radius: 8px; margin: 1.5rem 0; display: flex; align-items: center; gap: 10px; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.danger   { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .form-group { margin-bottom: 1.8rem; }
        .form-group label { font-weight: 600; margin-bottom: 0.6rem; display: block; color: #2c3e50; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 1rem; border: 1px solid var(--border); border-radius: 8px; font-size: 1rem;
        }
        .form-group select { height: 52px; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: var(--blue); box-shadow: 0 0 0 3px rgba(30,64,175,0.12); outline: none;
        }
        .form-group textarea { min-height: 140px; resize: vertical; }
        .form-group small { display: block; margin-top: 0.6rem; color: var(--gray); font-size: 0.9rem; }
        select option:disabled { color: #aaa; background: #f1f3f5; font-style: italic; }
        .btn-save {
            display: inline-flex; align-items: center; gap: 10px; padding: 1rem 2.4rem;
            background: var(--navy); color: white; border: none; border-radius: 8px;
            font-weight: 600; font-size: 1.1rem; cursor: pointer; transition: all 0.25s;
        }
        .btn-save:hover { background: var(--blue); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(22,33,62,0.25); }
        table { width: 100%; border-collapse: collapse; margin-top: 2.5rem; }
        th, td { padding: 1.3rem 1.6rem; text-align: left; border-bottom: 1px solid #e9ecef; }
        th { background: #f8f9fa; font-weight: 600; color: #2c3e50; }
        tr:hover { background: #f9fbfc; }
        .action-btn { padding: 0.7rem 1.4rem; border-radius: 6px; font-size: 0.95rem; cursor: pointer; border: none; transition: all 0.2s; }
        .edit-btn { background: #0d6efd; color: white; margin-right: 0.7rem; }
        .edit-btn:hover { background: #0b5ed7; }
        .delete-btn { background: #dc3545; color: white; }
        .delete-btn:hover { background: #c82333; }
        .officer-thumb { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; border: 1px solid #dee2e6; }
        @media (max-width: 992px) { .main-content { margin-left: 0; padding: 1.5rem 1rem; } }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h1>Manage About Page & Officers</h1>
        <p class="muted">Changes appear immediately on the public site.</p>

        <?php if ($message): ?>
            <div class="alert success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- ABOUT DESCRIPTION -->
        <div style="background:white; border:1px solid var(--border); border-radius:10px; padding:2rem; margin-bottom:3rem;">
            <h2 style="margin-top:0;">About Us Description</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update_description">
                <div class="form-group">
                    <label for="about_desc">Main Description Text</label>
                    <textarea id="about_desc" name="description" required><?= htmlspecialchars($about_desc) ?></textarea>
                    <small>Live on public site immediately.</small>
                </div>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Description</button>
            </form>
        </div>

        <!-- OFFICERS MANAGEMENT -->
        <h2>Manage SK Federation Officers</h2>

        <form method="POST" enctype="multipart/form-data" style="background:white; border:1px solid var(--border); border-radius:10px; padding:2rem; margin-bottom:3rem;">
            <input type="hidden" name="action" id="form_action" value="add">
            <input type="hidden" name="id" id="edit_id" value="">

            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required placeholder="e.g. Hon. Rian Maclyn Dela Cruz">
            </div>

            <div class="form-group">
                <label for="position">Position</label>
                <select id="position" name="position" required>
                    <option value="">— Select or type position —</option>
                    <?php foreach ($all_positions as $pos): ?>
                        <?php
                        $is_occupied = isset($occupied[$pos]);
                        $disabled = $is_occupied ? 'disabled' : '';
                        $display = $pos;
                        if ($is_occupied) $display .= ' — Occupied';
                        ?>
                        <option value="<?= htmlspecialchars($pos) ?>" <?= $disabled ?>>
                            <?= htmlspecialchars($display) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>Positions already assigned are disabled. When editing, your current position remains available.</small>
            </div>

            <div class="form-group">
                <label for="barangay">Barangay</label>
                <select id="barangay" name="barangay" required>
                    <option value="">— Select barangay —</option>
                    <?php foreach ($barangays as $brgy): ?>
                        <option value="<?= htmlspecialchars($brgy) ?>"><?= htmlspecialchars($brgy) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="photo">Photo</label>
                <input type="file" id="photo" name="photo" accept="image/*">
                <small>Images are resized & centered automatically. Uploaded photo appears live after saving.</small>
            </div>

            <button type="submit" class="btn-save">
                <i class="fas fa-save"></i> Save Officer
            </button>
        </form>

        <!-- Current Officers List -->
        <h2 style="margin: 3.5rem 0 1.5rem;">Current Officers</h2>

        <?php if (count($officers) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Full Name</th>
                        <th>Position</th>
                        <th>Barangay</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($officers as $officer): ?>
                        <tr>
                            <td>
                                <?php if ($officer['photo']): ?>
                                    <img src="../<?= htmlspecialchars($officer['photo']) ?>" alt="Officer photo" class="officer-thumb">
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($officer['full_name']) ?></td>
                            <td><?= htmlspecialchars($officer['position']) ?></td>
                            <td><?= htmlspecialchars($officer['barangay']) ?></td>
                            <td>
                                <button class="action-btn edit-btn" onclick="editOfficer(
                                    <?= $officer['id'] ?>,
                                    '<?= addslashes($officer['full_name']) ?>',
                                    '<?= addslashes($officer['position']) ?>',
                                    '<?= addslashes($officer['barangay']) ?>'
                                )">Edit</button>

                                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this officer permanently?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= $officer['id'] ?>">
                                    <button type="submit" class="action-btn delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: var(--gray); font-style: italic; margin-top: 1.5rem;">
                No officers added yet.
            </p>
        <?php endif; ?>
    </div>
</div>

<script>
function editOfficer(id, name, pos, brgy) {
    document.getElementById('form_action').value = 'edit';
    document.getElementById('edit_id').value = id;
    document.getElementById('full_name').value = name;

    // Position handling
    const positionSelect = document.getElementById('position');
    let posFound = false;
    for (let option of positionSelect.options) {
        if (option.value === pos) {
            option.disabled = false;
            option.selected = true;
            posFound = true;
            break;
        }
    }
    if (!posFound) {
        let newOpt = document.createElement('option');
        newOpt.value = pos;
        newOpt.text = pos + ' (current)';
        newOpt.selected = true;
        positionSelect.appendChild(newOpt);
    }

    // Barangay handling
    const barangaySelect = document.getElementById('barangay');
    for (let option of barangaySelect.options) {
        if (option.value === brgy) {
            option.selected = true;
            break;
        }
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

</body>
</html>