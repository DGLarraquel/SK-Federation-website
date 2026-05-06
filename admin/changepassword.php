<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit;
}

$error = $success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current'] ?? '';
    $new     = $_POST['new'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    // Demo logic → replace with real password verification from DB
    // Example: $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
    // Then password_verify($current, $stored_hash)

    if ($current === 'secret123' && $new === $confirm && strlen($new) >= 6) {
        // In real system: update DB here
        // $hashed = password_hash($new, PASSWORD_DEFAULT);
        // UPDATE admins SET password = ? WHERE id = $_SESSION['admin_id']
        $success = 'Password changed successfully!';
    } else {
        $error = 'Invalid current password or passwords do not match.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password – SK Admin</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            max-width: 500px;
            margin: 2rem auto;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 0.9rem;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }
        .error { color: #dc3545; margin-bottom: 1rem; }
        .success { color: #198754; margin-bottom: 1rem; }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h1>Change Password</h1>
        <p style="color:#6c757d; margin-bottom:2rem;">Update your admin account password.</p>

        <div class="card">
            <?php if ($error): ?>
                <div class="error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <label for="current">Current Password</label>
                    <input type="password" id="current" name="current" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="new">New Password</label>
                    <input type="password" id="new" name="new" required minlength="6" autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label for="confirm">Confirm New Password</label>
                    <input type="password" id="confirm" name="confirm" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:1rem; font-size:1.1rem;">
                    <i class="fas fa-lock"></i> Update Password
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>