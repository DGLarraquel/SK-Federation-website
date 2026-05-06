<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/auth_sk_chair.php");
    exit;
}

require_once '../../connection.php';

$user_id = $_SESSION['user_id'];

// GET BARANGAY & USER INFO
try {
    $stmt = $pdo->prepare("
        SELECT b.id AS barangay_id, b.name AS barangay_name, u.firstname 
        FROM users u 
        LEFT JOIN barangays b ON u.barangay = b.name 
        WHERE u.id = ? AND u.role = 'sk_chairperson' 
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();

    if (!$user_data || !$user_data['barangay_id']) {
        die("Error: Your account is not linked to a valid barangay.");
    }

    $barangay_id   = $user_data['barangay_id'];
    $barangay_name = $user_data['barangay_name'];
    $firstname     = $user_data['firstname'] ?? 'SK';

} catch (Exception $e) {
    die("Database connection failed.");
}

$msg = $msg_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $budget      = !empty($_POST['budget']) ? (float)$_POST['budget'] : 0;
    
    $image_db_name = ''; // Maps to 'image' column
    $file_db_name  = ''; // Maps to 'file_path' column
    
    $upload_dir = '../../uploads/projects/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

    $can_proceed = true;

    // 1. HANDLE PROJECT PHOTO (Image column)
    if (!empty($_FILES['project_photo']['name']) && $_FILES['project_photo']['error'] === UPLOAD_ERR_OK) {
        $img_ext = strtolower(pathinfo($_FILES['project_photo']['name'], PATHINFO_EXTENSION));
        $allowed_imgs = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($img_ext, $allowed_imgs)) {
            $image_db_name = 'img_' . time() . '_' . uniqid() . '.' . $img_ext;
            move_uploaded_file($_FILES['project_photo']['tmp_name'], $upload_dir . $image_db_name);
        } else {
            $msg = "Invalid photo format. Only JPG, PNG, WEBP, and GIF allowed."; 
            $msg_type = "error"; 
            $can_proceed = false;
        }
    }

    // 2. HANDLE SUPPORTING DOCUMENT (file_path column)
    // Supports: Image, Docx, Excel, PDF, CSV, etc.
    if ($can_proceed && !empty($_FILES['project_doc']['name']) && $_FILES['project_doc']['error'] === UPLOAD_ERR_OK) {
        $file_ext = strtolower(pathinfo($_FILES['project_doc']['name'], PATHINFO_EXTENSION));
        
        // Expanded list to include all your requested types
        $allowed_files = ['pdf', 'docx', 'doc', 'xlsx', 'xls', 'csv', 'jpg', 'jpeg', 'png', 'txt'];

        if (in_array($file_ext, $allowed_files)) {
            $file_db_name = 'doc_' . time() . '_' . uniqid() . '.' . $file_ext;
            move_uploaded_file($_FILES['project_doc']['tmp_name'], $upload_dir . $file_db_name);
        } else {
            $msg = "Invalid file type for the document. Allowed: PDF, Word, Excel, CSV, and Images."; 
            $msg_type = "error"; 
            $can_proceed = false;
        }
    }

    // 3. INSERT INTO DATABASE
    if ($can_proceed) {
        try {
            $sql = "INSERT INTO projects 
                    (barangay_id, submitted_by, title, description, budget, image, file_path, status, submitted_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([$barangay_id, $user_id, $title, $description, $budget, $image_db_name, $file_db_name]);

            $msg = "Project and files submitted successfully!";
            $msg_type = "success";
            $_POST = []; 
        } catch (PDOException $e) {
            $msg = "Database Error: " . $e->getMessage();
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Project • SK <?= htmlspecialchars($firstname) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="card" style="max-width: 650px; margin: 2rem auto; padding: 2rem; background: #fff; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h2 style="color: #1e293b; margin-bottom: 0.5rem;">Upload New Project</h2>
        <p style="color: #64748b; margin-bottom: 2rem;">Barangay: <strong><?= htmlspecialchars($barangay_name) ?></strong></p>

        <?php if ($msg): ?>
            <div style="padding: 1rem; margin-bottom: 1.5rem; border-radius: 6px; <?= $msg_type === 'success' ? 'background:#dcfce7; color:#166534;' : 'background:#fee2e2; color:#991b1b;' ?>">
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 1.2rem;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Project Title *</label>
                <input type="text" name="title" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px;" placeholder="Enter project name" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="display:block; font-weight:600; margin-bottom:5px;">Proposed Budget (PHP) *</label>
                <input type="number" name="budget" step="0.01" required style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px;" placeholder="0.00" value="<?= htmlspecialchars($_POST['budget'] ?? '') ?>">
            </div>

            <div style="margin-bottom: 1.2rem; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; background: #f8fafc;">
                <label style="display:block; font-weight:600; color: #1e293b;"><i class="fas fa-camera"></i> Project Thumbnail Photo</label>
                <input type="file" name="project_photo" accept="image/*" style="margin-top: 10px;">
                <small style="display:block; color: #94a3b8; margin-top: 5px;">This will be displayed in the project list (JPG, PNG, WebP).</small>
            </div>

            <div style="margin-bottom: 2rem; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px; background: #f8fafc;">
                <label style="display:block; font-weight:600; color: #1e293b;"><i class="fas fa-file-export"></i> Supporting Files (Docx, Excel, PDF, etc.)</label>
                <input type="file" name="project_doc" accept=".pdf,.docx,.doc,.xlsx,.xls,.csv,.jpg,.png" style="margin-top: 10px;">
                <small style="display:block; color: #94a3b8; margin-top: 5px;">Upload liquidation reports, budgets, or extra images.</small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="projects.php" style="padding: 10px 20px; text-decoration: none; color: #64748b; border: 1px solid #e2e8f0; border-radius: 6px;">Cancel</a>
                <button type="submit" style="padding: 10px 25px; background: #1e293b; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Submit Project</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>