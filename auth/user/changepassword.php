<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth_sk_chair.php");
    exit;
}

require_once '../../connection.php';

$user_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);

$success = $error = '';

// GET USER DATA FOR DISPLAY
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
        die("<h2>Error: Account not linked to a barangay.</h2>");
    }

    $firstname     = $user_data['firstname'] ?? 'SK';
    $barangay_name = $user_data['barangay_name'] ?? 'Unknown Barangay';

    $profile_pic = "../../images/default-sk-avatar.png";
    if (!empty($user_data['profile_pic'])) {
        $path = "../../images/profiles/" . ltrim($user_data['profile_pic'], '/');
        if (file_exists($path)) $profile_pic = $path;
    }
} catch (Exception $e) {
    die("Database error.");
}

// HANDLE PASSWORD CHANGE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($current) || empty($new) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif ($new !== $confirm) {
        $error = "New password and confirmation do not match.";
    } elseif (strlen($new) < 6) {
        $error = "New password must be at least 6 characters.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            if ($user && password_verify($current, $user['password'])) {
                $new_hash = password_hash($new, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update->execute([$new_hash, $user_id]);

                $success = "Password changed successfully! Redirecting...";
                echo "<script>setTimeout(() => location.href='dashboard.php', 2000);</script>";
            } else {
                $error = "Current password is incorrect.";
            }
        } catch (Exception $e) {
            $error = "Failed to update password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password • SK <?= htmlspecialchars($firstname) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h1 class="page-title">Change Password</h1>
    <p class="subtitle">Barangay <?= htmlspecialchars($barangay_name) ?> • SK Chairperson</p>

    <div class="form-container">
        <h2>Update Your Password</h2>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Current Password</label>
                <div class="password-wrapper">
                    <input type="password" name="current_password" id="current_password" class="password-field" required>
                    <i class="fas fa-eye toggle-eye" onclick="togglePassword('current_password', this)"></i>
                </div>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <div class="password-wrapper">
                    <input type="password" name="new_password" id="new_password" class="password-field" required minlength="6">
                    <i class="fas fa-eye toggle-eye" onclick="togglePassword('new_password', this)"></i>
                </div>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <div class="password-wrapper">
                    <input type="password" name="confirm_password" id="confirm_password" class="password-field" required minlength="6">
                    <i class="fas fa-eye toggle-eye" onclick="togglePassword('confirm_password', this)"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Update Password
            </button>
        </form>
    </div>
</div>

<script>
function togglePassword(fieldId, icon) {
    const field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>