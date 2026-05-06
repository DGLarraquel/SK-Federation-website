<?php
session_start();
require_once '../../connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: officers.php");
    exit;
}

$barangay_id = $_SESSION['barangay_id'] ?? 0;
$id          = !empty($_POST['id']) ? (int)$_POST['id'] : null;
$name        = trim($_POST['name'] ?? '');
$role        = trim($_POST['role'] ?? '');
$old_image   = $_POST['old_image'] ?? '';
$image       = $old_image;

if ($barangay_id == 0 || $name === '' || $role === '') {
    $_SESSION['msg'] = "Please fill all required fields (name and position).";
    header("Location: officers.php");
    exit;
}

// ────────────────────────────────────────────────
//  CHECK FOR DUPLICATE ROLE IN THE SAME BARANGAY
// ────────────────────────────────────────────────
try {
    if ($id) {
        // When updating: exclude current record
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM sk_council_members 
            WHERE barangay_id = ? 
              AND role = ? 
              AND id != ?
        ");
        $stmt->execute([$barangay_id, $role, $id]);
    } else {
        // When inserting: simple check
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM sk_council_members 
            WHERE barangay_id = ? 
              AND role = ?
        ");
        $stmt->execute([$barangay_id, $role]);
    }

    if ($stmt->fetchColumn() > 0) {
        $_SESSION['msg'] = "The position <strong>" . htmlspecialchars($role) . "</strong> is already assigned to another council member in this barangay.";
        header("Location: officers.php");
        exit;
    }
} catch (Exception $e) {
    $_SESSION['msg'] = "Error checking position availability. Please try again.";
    header("Location: officers.php");
    exit;
}

// ────────────────────────────────────────────────
//  HANDLE IMAGE UPLOAD
// ────────────────────────────────────────────────
if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($ext, $allowed)) {
        $_SESSION['msg'] = "Only JPG, JPEG, PNG, GIF, WEBP images are allowed.";
        header("Location: officers.php");
        exit;
    }

    $newname = 'officer_' . uniqid() . '.' . $ext;
    $dest = "../../images/officers/" . $newname;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        // Delete old image if exists and we're replacing it
        if ($old_image && file_exists("../../images/officers/" . $old_image)) {
            @unlink("../../images/officers/" . $old_image);
        }
        $image = $newname;
    } else {
        $_SESSION['msg'] = "Failed to upload the image. Please try again.";
        header("Location: officers.php");
        exit;
    }
}

// ────────────────────────────────────────────────
//  SAVE TO DATABASE
// ────────────────────────────────────────────────
try {
    if ($id) {
        // UPDATE
        $sql = "UPDATE sk_council_members 
                SET name = ?, role = ?, image = ? 
                WHERE id = ? AND barangay_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $role, $image, $id, $barangay_id]);

        $_SESSION['msg'] = "Council member updated successfully!";
    } else {
        // INSERT
        $sql = "INSERT INTO sk_council_members 
                (barangay_id, name, role, image, sort_order) 
                VALUES (?, ?, ?, ?, 
                    (SELECT IFNULL(MAX(sort_order), 0) + 1 
                     FROM sk_council_members m 
                     WHERE m.barangay_id = ?)
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$barangay_id, $name, $role, $image, $barangay_id]);

        $_SESSION['msg'] = "Council member added successfully!";
    }

    header("Location: officers.php");
    exit;

} catch (PDOException $e) {
    // You can log this in production: error_log($e->getMessage());
    $_SESSION['msg'] = "Database error occurred. Please try again or contact support.";
    header("Location: officers.php");
    exit;
}