<?php
session_start();
require_once '../connection.php';

if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch projects — now only fetching file_path for the document view
$stmt = $pdo->query("
    SELECT 
        p.id, p.title, p.description, p.budget, p.status, 
        p.file_path,
        p.submitted_at, p.approved_at,
        COALESCE(b.name, '— Unknown —') AS barangay_name
    FROM projects p
    LEFT JOIN barangay b ON p.barangay_id = b.id
    ORDER BY p.approved_at DESC, p.submitted_at DESC
");
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ... [Keep your existing POST handling logic for 'add' and 'update_status' exactly as it was] ...

$message = '';
$success = isset($_GET['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $barangay_id = !empty($_POST['barangay_id']) ? (int)$_POST['barangay_id'] : 0;
        $budget      = !empty($_POST['budget']) ? (float)$_POST['budget'] : 0;

        if ($title && $barangay_id > 0 && $description) {
            $insert = $pdo->prepare("
                INSERT INTO projects 
                (barangay_id, title, description, budget, status, approved_at, submitted_at)
                VALUES (?, ?, ?, ?, 'approved', NOW(), NOW())
            ");
            $insert->execute([$barangay_id, $title, $description, $budget]);
            header("Location: cms_projects.php?success=1");
            exit();
        } else {
            $message = "Please fill in all required fields.";
        }
    }

    elseif (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $updates = [];
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'status_') === 0) {
                $id = (int) substr($key, 7);
                $status = $value === 'approved' ? 'approved' : 'pending';
                $updates[] = [$status, $id];
            }
        }

        if ($updates) {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE projects SET status = ?, approved_at = ? WHERE id = ?");
                foreach ($updates as [$status, $id]) {
                    $approved_at = ($status === 'approved') ? date('Y-m-d H:i:s') : null;
                    $stmt->execute([$status, $approved_at, $id]);
                }
                $pdo->commit();
                header("Location: cms_projects.php?success=1");
                exit();
            } catch (Exception $e) {
                $pdo->rollBack();
                $message = "Error saving changes.";
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
    <title>Manage Projects – SK Admin</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --navy: #0f172a;
            --blue: #2563eb;
            --success: #16a34a;
            --light: #f8fafc;
            --gray: #64748b;
            --border: #e2e8f0;
            --pending: #f59e0b;
        }
        body {
            background: var(--light);
            font-family: 'Segoe UI', system-ui, sans-serif;
            color: #1e293b;
            margin: 0;
            line-height: 1.6;
        }
        .main-content {
            margin-left: 260px;
            padding: 2.5rem 2rem;
            min-height: 100vh;
        }
        .container {
            max-width: 1280px;
            margin: 0 auto;
        }
        h1 { color: var(--navy); font-size: 2.2rem; margin: 0 0 0.5rem; }
        .muted { color: var(--gray); margin: 0 0 2.5rem; }
        .alert {
            padding: 1.2rem 1.5rem;
            border-radius: 10px;
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 1.5rem 0;
            font-weight: 500;
        }

        .add-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.06);
            padding: 2.2rem;
            margin-bottom: 3.5rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.8rem 2.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #334155;
        }
        input, select, textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
        }
        textarea { min-height: 130px; resize: vertical; }
        .btn {
            padding: 0.9rem 1.8rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background: var(--blue);
            color: white;
        }
        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

        .projects-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 3.5rem 0 1.8rem;
        }
        .project-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 2rem;
        }
        .project-card {
            background: white;
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 1.8rem;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .project-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        }
        .project-card.live {
            border-color: var(--success);
            background: #f0fdf4;
        }
        .project-card.live::after {
            content: "LIVE";
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--success);
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
        }
        .project-card.pending::after {
            content: "Pending";
            position: absolute;
            top: 12px;
            right: 12px;
            background: var(--pending);
            color: white;
            font-size: 0.8rem;
            font-weight: bold;
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
        }
        .project-card h3 {
            margin: 0 0 1rem;
            font-size: 1.35rem;
            color: #1e293b;
        }
        .project-card .meta {
            color: var(--gray);
            font-size: 0.95rem;
            margin: 0.5rem 0;
        }
        .project-card .description {
            margin-top: 1.2rem;
            color: #475569;
            line-height: 1.55;
            font-size: 0.97rem;
            flex-grow: 1;
        }

        /* DOCUMENT VIEW STYLES */
        .admin-document-area {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }
        .doc-link {
            text-decoration: none;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.85rem;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .doc-link:hover {
            background: var(--navy);
            color: white;
        }

        .project-card .date {
            color: #64748b;
            font-size: 0.9rem;
            margin-top: 1rem;
            display: block;
        }

        .save-all-bar {
            margin: 3.5rem 0 2.5rem;
            text-align: center;
        }
        .save-all-bar .btn {
            font-size: 1.15rem;
            padding: 1.1rem 2.8rem;
        }

        @media (max-width: 992px) {
            .main-content { margin-left: 0; padding: 1.8rem 1.2rem; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container">

        <h1>Manage Projects</h1>
        <p class="muted">Add new projects or change status of existing ones</p>

        <?php if ($success || $message): ?>
            <div class="alert">
                <i class="fas fa-<?= $success ? 'check-circle' : 'exclamation-circle' ?>"></i>
                <?= htmlspecialchars($success ? "Project added successfully!" : $message) ?>
            </div>
        <?php endif; ?>

        <div class="add-section">
            <h2 style="margin-top:0; font-size:1.45rem;">Add New Project</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Project Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Youth Training">
                    </div>
                    <div class="form-group">
                        <label>Barangay *</label>
                        <select name="barangay_id" required>
                            <option value="">— Select Barangay —</option>
                            <?php
                            $barangay_list = $pdo->query("SELECT id, name FROM barangay ORDER BY name ASC")->fetchAll();
                            foreach ($barangay_list as $b) {
                                echo "<option value=\"{$b['id']}\">" . htmlspecialchars($b['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Budget (PHP)</label>
                        <input type="number" name="budget" step="0.01" min="0">
                    </div>
                </div>
                <div style="margin-top:1.8rem;">
                    <textarea name="description" placeholder="Project description..." rows="5" required></textarea>
                </div>
                <div style="margin-top:2rem; text-align:right;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Publish Project
                    </button>
                </div>
            </form>
        </div>

        <div class="projects-header">
            <h2 style="margin:0; font-size:1.6rem;">Existing Projects</h2>
        </div>

        <?php if (empty($projects)): ?>
            <p style="text-align:center; padding:4rem 0; color:var(--gray);">No projects found.</p>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="action" value="update_status">

                <div class="project-grid">
                <?php foreach ($projects as $p):
                    $isLive = ($p['status'] ?? 'pending') === 'approved';
                    $class = $isLive ? 'live' : 'pending';
                ?>
                    <div class="project-card <?= $class ?>" onclick="toggleStatus(this)">
                        <input type="hidden" name="status_<?= $p['id'] ?>"
                               value="<?= $isLive ? 'approved' : 'pending' ?>"
                               class="status-field">

                        <h3><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="meta"><strong>Barangay:</strong> <?= htmlspecialchars($p['barangay_name']) ?></p>
                        <p class="meta"><strong>Budget:</strong> ₱<?= number_format($p['budget'] ?? 0, 2) ?></p>

                        <p class="description">
                            <?= nl2br(htmlspecialchars($p['description'] ?: 'No description provided.')) ?>
                        </p>

                        <div class="admin-document-area" onclick="event.stopPropagation();">
                            <?php if (!empty($p['file_path'])): ?>
                                <?php 
                                    $file_ext = strtolower(pathinfo($p['file_path'], PATHINFO_EXTENSION));
                                    $icon = "fa-file-alt";
                                    if ($file_ext == 'pdf') $icon = "fa-file-pdf";
                                    elseif (in_array($file_ext, ['xls', 'xlsx', 'csv'])) $icon = "fa-file-excel";
                                    elseif (in_array($file_ext, ['doc', 'docx'])) $icon = "fa-file-word";
                                ?>
                                <a href="../uploads/projects/<?= htmlspecialchars($p['file_path']) ?>" target="_blank" class="doc-link">
                                    <i class="fas <?= $icon ?>"></i> View Submitted File
                                </a>
                            <?php else: ?>
                                <span style="font-size: 0.85rem; color: #94a3b8;"><i class="fas fa-info-circle"></i> No file attached</span>
                            <?php endif; ?>
                        </div>

                        <div class="date">
                            Submitted: <?= date('M j, Y', strtotime($p['submitted_at'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>

                <div class="save-all-bar">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save All Status Changes
                    </button>
                </div>
            </form>
        <?php endif; ?>

    </div>
</div>

<script>
function toggleStatus(card) {
    const field = card.querySelector('.status-field');
    const current = field.value;
    field.value = current === 'approved' ? 'pending' : 'approved';
    card.classList.toggle('live');
    card.classList.toggle('pending');
}
</script>

</body>
</html>