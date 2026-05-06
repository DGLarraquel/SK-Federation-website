<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}

/* FETCH HOME DATA */
$stmt = $pdo->query("SELECT * FROM site_home LIMIT 1");
$home = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['hero_title' => '', 'hero_subtitle' => '', 'id' => 1];

/* UPDATE HOME DATA */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hero_title    = trim($_POST['hero_title'] ?? '');
    $hero_subtitle = trim($_POST['hero_subtitle'] ?? '');

    if ($hero_title && $hero_subtitle) {
        $update = $pdo->prepare("
            UPDATE site_home 
            SET hero_title = ?, hero_subtitle = ? 
            WHERE id = ?
        ");
        $update->execute([$hero_title, $hero_subtitle, $home['id']]);

        header("Location: cms_home.php?success=1");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Homepage – SK Admin</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy:    #16213e;
            --blue:    #1e40af;
            --light:   #f8f9fa;
            --gray:    #6c757d;
            --border:  #dee2e6;
            --danger:  #dc3545;
            --success: #198754;
        }

        body {
            background: var(--light);
            font-family: 'Poppins', sans-serif;
            color: #2d3748;
            margin: 0;
        }

        .main-content {
            margin-left: 260px;          /* ← adjust if your sidebar is wider/narrower */
            padding: 2rem 1.5rem;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        h1 {
            color: var(--navy);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .muted {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .form-group {
            margin-bottom: 1.8rem;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 0.6rem;
            display: block;
            color: #2c3e50;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(30,64,175,0.15);
            outline: none;
        }

        .form-group textarea {
            min-height: 140px;
            resize: vertical;
        }

        .btn-primary,
        .preview-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 1rem 2.2rem;
            font-size: 1.05rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.25s;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--navy);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--blue);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(22,33,62,0.25);
        }

        .preview-btn {
            background: white;
            color: var(--navy);
            border: 2px solid var(--navy);
        }

        .preview-btn:hover {
            background: var(--navy);
            color: white;
            transform: translateY(-2px);
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1.5rem 1rem;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="container">
        <h1>Edit Homepage</h1>
        <p class="muted">Update the welcome message shown on the public homepage.</p>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Changes saved successfully!
            </div>
        <?php endif; ?>

        <div style="margin: 2rem 0 3rem;">
            <a href="../index.php" target="_blank" class="preview-btn">
                <i class="fas fa-eye"></i> Preview Live Homepage
            </a>
        </div>

        <form method="POST" class="admin-form">
            <div class="form-group">
                <label for="hero_title">Title</label>
                <input 
                    type="text" 
                    id="hero_title" 
                    name="hero_title" 
                    value="<?= htmlspecialchars($home['hero_title'] ?? '') ?>" 
                    required 
                    placeholder="e.g. Welcome to SK Federation of Malolos"
                >
            </div>

            <div class="form-group">
                <label for="hero_subtitle">Subtitle</label>
                <textarea 
                    id="hero_subtitle" 
                    name="hero_subtitle" 
                    rows="5" 
                    required 
                    placeholder="e.g. Empowering the youth, shaping the future of our communities."
                ><?= htmlspecialchars($home['hero_subtitle'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>
    </div>
</div>

</body>
</html>