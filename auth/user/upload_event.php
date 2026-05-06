<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth_sk_chair.php");
    exit;
}

require_once '../../connection.php';

$user_id = $_SESSION['user_id'];
$current_page = basename($_SERVER['PHP_SELF']);

// GET USER + BARANGAY DATA
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
    $barangay_id   = $user_data['barangay_id'];
    $barangay_name = $user_data['barangay_name'] ?? 'Unknown Barangay';

    $profile_pic = "../../images/default-sk-avatar.png";
    if (!empty($user_data['profile_pic'])) {
        $path = "../../images/profiles/" . ltrim($user_data['profile_pic'], '/');
        if (file_exists($path)) $profile_pic = $path;
    }
} catch (Exception $e) {
    die("Database error.");
}

// HANDLE FORM SUBMISSION
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $venue       = trim($_POST['venue'] ?? '');
    $event_date  = $_POST['event_date'] ?? '';
    $description = trim($_POST['description'] ?? '');
    $status      = $_POST['status'] ?? 'upcoming';

    if (empty($title) || empty($event_date)) {
        $error = "Title and Event Date are required.";
    } else {
        $image_path = '';

        if (!empty($_FILES['event_image']['name'])) {
            $file     = $_FILES['event_image'];
            $filename = $file['name'];
            $tmp_name = $file['tmp_name'];
            $size     = $file['size'];
            $error_code = $file['error'];

            if ($error_code !== UPLOAD_ERR_OK) {
                $error = "Upload failed. Error code: $error_code";
            } elseif ($size > 6 * 1024 * 1024) {
                $error = "Image too large (max 6MB).";
            } else {
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                if (!in_array($ext, $allowed)) {
                    $error = "Invalid file type. Only JPG, PNG, GIF, WEBP allowed.";
                } else {
                    $upload_dir = "../../images/events/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }

                    $new_filename = 'event_' . $user_id . '_' . time() . '_' . uniqid() . '.' . $ext;
                    $destination  = $upload_dir . $new_filename;

                    if (move_uploaded_file($tmp_name, $destination)) {
                        $image_path = "images/events/" . $new_filename;
                    } else {
                        $error = "Failed to move uploaded file. Check folder permissions.";
                    }
                }
            }
        }

        if (empty($error)) {
            try {
                $sql = "INSERT INTO events 
                        (title, barangay, venue, event_date, description, image, status, submitted_at, barangay_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $title,
                    $barangay_name,
                    $venue,
                    $event_date,
                    $description,
                    $image_path,
                    $status,
                    $barangay_id
                ]);

                $success = "Event created successfully!";
                $_POST = [];
            } catch (Exception $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Event • SK <?= htmlspecialchars($firstname) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/user.css">
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <h1 class="page-title">Create New Event</h1>
    <p class="subtitle">Barangay <strong><?= htmlspecialchars($barangay_name) ?></strong></p>

    <div class="form-container">
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?> Redirecting to events...</div>
            <script>setTimeout(() => location.href='events.php', 2000);</script>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Event Title <span style="color:red;">*</span></label>
                <input type="text" name="title" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Venue</label>
                <input type="text" name="venue" value="<?= htmlspecialchars($_POST['venue'] ?? '') ?>" placeholder="e.g. Barangay Hall">
            </div>

            <div class="form-group">
                <label>Event Date <span style="color:red;">*</span></label>
                <input type="date" name="event_date" value="<?= htmlspecialchars($_POST['event_date'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" placeholder="Describe the event..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="upcoming" selected>Upcoming</option>
                    <option value="ongoing">Ongoing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label>Event Image (Optional)</label>
                <div class="file-input">
                    <input type="file" name="event_image" id="event_image" accept="image/*">
                    <label for="event_image" class="file-label">
                        <i class="fas fa-cloud-upload-alt"></i><br><br>
                        <strong>Click to upload image</strong><br>
                        <small>JPG, PNG, GIF, WEBP • Max 6MB</small>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-paper-plane"></i> Create Event
            </button>
        </form>
    </div>
</div>

</body>
</html>