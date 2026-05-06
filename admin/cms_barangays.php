<?php
session_start();
require_once '../connection.php';

// Optional: you may decide to keep admin-only access or open it to barangay coordinators
// For now keeping admin check – remove if you want public view
if (!isset($_SESSION['admin'])) {
    header("Location: ../index.php");
    exit();
}

// FETCH ALL BARANGAYS
$stmt = $pdo->query("
    SELECT id, name, description, updated_at 
    FROM barangays 
    ORDER BY name ASC
");
$barangays = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Overview – SK Federation of Malolos</title>
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
            --shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        body {
            background: var(--light);
            font-family: 'Poppins', sans-serif;
            color: #2d3748;
            margin: 0;
        }

        .main-content {
            margin-left: 260px;
            padding: 2.5rem 2rem;
            min-height: 100vh;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        h1 {
            color: var(--navy);
            font-weight: 700;
            margin: 0 0 0.6rem;
        }

        .subtitle {
            color: var(--gray);
            margin-bottom: 2.5rem;
            font-size: 1.05rem;
        }

        .barangay-card {
            background: white;
            border-radius: 12px;
            border: 1px solid var(--border);
            margin-bottom: 2rem;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .barangay-header {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
            color: white;
            padding: 1.4rem 1.8rem;
            font-size: 1.35rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .barangay-header i {
            transition: transform 0.3s;
        }

        .barangay-header.active i {
            transform: rotate(180deg);
        }

        .barangay-content {
            padding: 2rem;
            display: none;
        }

        .meta {
            font-size: 0.9rem;
            color: #64748b;
            margin-bottom: 1.5rem;
        }

        .stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin: 1.5rem 0;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.95rem;
            color: var(--gray);
        }

        .section-title {
            font-size: 1.25rem;
            color: var(--navy);
            margin: 2rem 0 1rem;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 0.5rem;
        }

        .item-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.2rem;
        }

        .item-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 1.2rem;
            display: flex;
            gap: 1rem;
        }

        .item-card img {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
        }

        .item-info strong {
            display: block;
            margin-bottom: 0.3rem;
        }

        .view-public-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.7rem 1.4rem;
            background: var(--blue);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            margin-top: 1.5rem;
        }

        .view-public-btn:hover {
            background: #1e3a8a;
        }

        .empty-state {
            color: var(--gray);
            font-style: italic;
            padding: 1.5rem 0;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 0;
                padding: 1.5rem 1rem;
            }
            .item-list {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<div class="main-content">
    <div class="container">
        <h1>Barangay Overview</h1>
        <p class="subtitle">Current status of SK activities across all barangays in Malolos City</p>

        <?php if (empty($barangays)): ?>
            <div style="background:white; padding:2rem; border-radius:12px; text-align:center; color:var(--gray);">
                <i class="fas fa-folder-open fa-3x" style="margin-bottom:1rem; opacity:0.6;"></i><br>
                No barangay data available yet.
            </div>
        <?php else: ?>
            <?php foreach ($barangays as $b): 
                $bid = $b['id'];
            ?>
                <div class="barangay-card">
                    <div class="barangay-header" onclick="toggleSection(this)">
                        <?= htmlspecialchars($b['name']) ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>

                    <div class="barangay-content">

                        <div class="meta">
                            <?php if (!empty($b['updated_at'])): ?>
                                Last updated: <?= date('F d, Y \a\t g:i A', strtotime($b['updated_at'])) ?>
                            <?php endif; ?>
                        </div>

                        <!-- Quick Stats -->
                        <?php
                        $off_count = $pdo->query("SELECT COUNT(*) FROM barangay_officers WHERE barangay_id = $bid")->fetchColumn();
                        $evt_count = $pdo->query("SELECT COUNT(*) FROM events WHERE barangay_id = $bid")->fetchColumn();
                        $prj_count = $pdo->query("SELECT COUNT(*) FROM projects WHERE barangay_id = $bid")->fetchColumn();
                        ?>
                        <div class="stats">
                            <div class="stat-item"><i class="fas fa-users"></i> <?= $off_count ?> Officers</div>
                            <div class="stat-item"><i class="fas fa-calendar-check"></i> <?= $evt_count ?> Events</div>
                            <div class="stat-item"><i class="fas fa-tasks"></i> <?= $prj_count ?> Projects</div>
                        </div>

                        <!-- Description -->
                        <div style="margin:1.8rem 0;">
                            <strong style="color:var(--navy);">Description</strong><br><br>
                            <?= nl2br(htmlspecialchars($b['description'] ?: 'No description provided yet.')) ?>
                        </div>

                        <a href="../sections/barangay/barangay.php?name=<?= urlencode($b['name']) ?>"
                           target="_blank" class="view-public-btn">
                            <i class="fas fa-external-link-alt"></i>
                            View Public Barangay Page
                        </a>

                        <!-- Officers -->
                        <h3 class="section-title">SK Officers</h3>
                        <?php
                        $officers = $pdo->prepare("SELECT name, role, image FROM barangay_officers WHERE barangay_id = ? ORDER BY sort_order, name");
                        $officers->execute([$bid]);
                        $officer_list = $officers->fetchAll();
                        ?>
                        <?php if ($officer_list): ?>
                            <div class="item-list">
                                <?php foreach ($officer_list as $o): ?>
                                    <div class="item-card">
                                        <?php if ($o['image']): ?>
                                            <img src="../<?= htmlspecialchars($o['image']) ?>" alt="">
                                        <?php else: ?>
                                            <div style="width:90px; height:90px; background:#e2e8f0; border-radius:8px; display:flex; align-items:center; justify-content:center;">
                                                <i class="fas fa-user" style="font-size:2rem; color:#94a3b8;"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="item-info">
                                            <strong><?= htmlspecialchars($o['name']) ?></strong>
                                            <div style="color:#4b5563; margin-top:0.3rem;"><?= htmlspecialchars($o['role']) ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="empty-state">No officers recorded yet.</p>
                        <?php endif; ?>

                        <!-- Events & Projects can be added in similar style if needed -->
                        <!-- For now omitted to keep page clean – can be expanded later -->

                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleSection(el) {
    const content = el.nextElementSibling;
    const icon = el.querySelector('i');
    if (content.style.display === 'block') {
        content.style.display = 'none';
        icon.classList.remove('active');
    } else {
        content.style.display = 'block';
        icon.classList.add('active');
    }
}

// Optional: open first barangay by default
document.addEventListener('DOMContentLoaded', () => {
    const firstHeader = document.querySelector('.barangay-header');
    if (firstHeader) firstHeader.click();
});
</script>

</body>
</html>