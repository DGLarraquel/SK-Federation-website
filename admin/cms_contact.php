<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}

// Temporarily show errors during testing (remove/comment out on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = '';
$error = '';

// Fetch current contact info
$stmt = $pdo->query("SELECT * FROM site_contact LIMIT 1");
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$contact) {
    $error = "No contact record found in the database.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_contact'])) {
    $address = trim($_POST['address'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $email   = trim($_POST['email'] ?? '');

    if (empty($address) || empty($phone) || empty($email)) {
        $error = "All fields are required.";
    } else {
        try {
            if ($contact) {
                $stmt = $pdo->prepare("UPDATE site_contact SET address = ?, phone = ?, email = ? WHERE id = ?");
                $stmt->execute([$address, $phone, $email, $contact['id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO site_contact (address, phone, email) VALUES (?, ?, ?)");
                $stmt->execute([$address, $phone, $email]);
            }
            $message = "Contact information updated successfully!";

            // Refresh data
            $stmt = $pdo->query("SELECT * FROM site_contact LIMIT 1");
            $contact = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Contact Info – SK Admin</title>
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
        .main-content { margin-left: 260px; padding: 2.5rem 2rem; min-height: 100vh; }
        .container { max-width: 900px; margin: 0 auto; }
        h1 { color: var(--navy); font-weight: 700; margin-bottom: 0.5rem; }
        .muted { color: var(--gray); margin-bottom: 2rem; }
        .card { background: white; border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); padding: 2.5rem; }
        .form-group { margin-bottom: 1.8rem; }
        .form-group label { font-weight: 600; color: #374151; margin-bottom: 0.6rem; display: block; }
        .form-group input { width: 100%; padding: 0.85rem 1.15rem; border: 1px solid #d1d5db; border-radius: 8px; font-size: 1rem; transition: border-color 0.2s; }
        .form-group input:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(30,64,175,0.15); }
        .btn-primary { background: var(--navy); color: white; border: none; padding: 0.9rem 2.2rem; border-radius: 8px; font-weight: 600; font-size: 1.05rem; cursor: pointer; }
        .btn-primary:hover { background: var(--blue); }
        .alert { padding: 1.2rem 1.6rem; border-radius: 10px; margin-bottom: 2rem; display: flex; align-items: center; gap: 12px; }
        .alert.success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert.danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 1.8rem 1.2rem; }
            .card { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>  <!-- This should now work -->

<div class="main-content">
    <div class="container">
        <h1>Edit Contact Information</h1>
        <p class="muted">Update the address, phone, and email shown on the public contact page.</p>

        <?php if ($message): ?>
            <div class="alert success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert danger"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($contact): ?>
            <div class="card">
                <form method="POST">
                    <input type="hidden" name="update_contact" value="1">

                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" value="<?= htmlspecialchars($contact['address'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($contact['email'] ?? '') ?>" required>
                    </div>

                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save me-2"></i> Save Changes
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>