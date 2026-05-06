<?php
require_once __DIR__ . '/../../connection.php';

/* =============================================
   1. BARANGAY NAME VALIDATION & NORMALIZATION
   ============================================= */
$name_raw = trim($_GET['name'] ?? '');
if ($name_raw === '') {
    die('Invalid barangay.');
}

$valid_barangays = [
    "Anilao","Atlag","Babatnin","Bagna","Bagong Bayan","Balayong","Balite","Bangkal",
    "Barihan","Bulihan","Bungahan","Caingin","Calero","Caliligawan","Canalate","Caniogan",
    "Catmon","Cofradia","Dakila","Guinhawa","Ligas","Liang","Longos","Look 1st","Look 2nd",
    "Lugam","Mabolo","Mambog","Masile","Matimbo","Mojon","Namayan","Niugan","Pamarawan",
    "Panasahan","Pinagbakahan","San Agustin","San Gabriel","San Juan","San Pablo","San Vicente",
    "Santiago","Santor","Santisima Trinidad","Sto. Cristo","Sto. Niño","Santo Rosario",
    "Sumapang Bata","Sumapang Matanda","Taal","Tikay"
];

$name_clean = ucwords(strtolower(preg_replace('/\s+/', ' ', $name_raw)));

if (!in_array($name_clean, $valid_barangays)) {
    die('Barangay not recognized.');
}

/* =============================================
   2. ENSURE BARANGAY EXISTS IN DATABASE
   ============================================= */
try {
    $stmt = $pdo->prepare("SELECT id FROM barangays WHERE LOWER(name) = LOWER(?) LIMIT 1");
    $stmt->execute([$name_clean]);
    $row = $stmt->fetch();

    if (!$row) {
        $insert = $pdo->prepare("INSERT INTO barangays (name) VALUES (?)");
        $insert->execute([$name_clean]);
        $barangay_id = $pdo->lastInsertId();
    } else {
        $barangay_id = $row['id'];
    }
} catch (Exception $e) {
    error_log("Barangay error: " . $e->getMessage());
    die('System error. Please try again later.');
}

/* =============================================
   3. FETCH BARANGAY INFO (logo + description)
   ============================================= */
$stmt = $pdo->prepare("SELECT img, description FROM barangays WHERE id = ?");
$stmt->execute([$barangay_id]);
$barangay = $stmt->fetch();

$logoPath = $barangay['img'] ? "../../images/barangays/{$barangay['img']}" : null;
$description = $barangay['description'] ?? 'A vibrant and progressive barangay in Malolos City, Bulacan.';

/* =============================================
   4. FETCH OFFICERS
   ============================================= */
$officersStmt = $pdo->prepare("
    SELECT name, role, image 
    FROM officers 
    WHERE barangay_id = ? 
    ORDER BY sort_order ASC, name ASC
");
$officersStmt->execute([$barangay_id]);
$officers = $officersStmt->fetchAll();

/* =============================================
   5. FETCH PROJECTS (NO IMAGE, FIXED ORDER BY)
   ============================================= */
$projStmt = $pdo->prepare("
    SELECT title, description 
    FROM projects 
    WHERE barangay_id = ? AND status = 'approved' 
    ORDER BY title ASC
");
$projStmt->execute([$barangay_id]);
$projects = $projStmt->fetchAll();

/* =============================================
   6. FETCH EVENTS (NO IMAGE)
   ============================================= */
$eventStmt = $pdo->prepare("
    SELECT title, description, event_date, venue 
    FROM events 
    WHERE barangay_id = ? AND status = 'approved' 
    ORDER BY event_date DESC
");
$eventStmt->execute([$barangay_id]);
$events = $eventStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay <?= htmlspecialchars($name_clean) ?> | SK Federation Malolos</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --navy: #0f3460;
            --gold: #eab308;
            --light: #f8fafc;
            --gray: #64748b;
            --dark: #1e293b;
            --border: #e2e8f0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.7;
        }

        .hero-section {
            background: linear-gradient(135deg, rgba(15, 52, 96, 0.95), rgba(15, 52, 96, 0.85)), url('../../images/skfederation-bg.jpg') center/cover no-repeat;
            color: white;
            padding: 5rem 0;
            text-align: center;
            border-bottom: 8px solid var(--gold);
        }

        .logo-circle {
            width: 180px;
            height: 180px;
            object-fit: contain;
            border: 10px solid white;
            border-radius: 50%;
            padding: 12px;
            background: white;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            margin-bottom: 1.5rem;
        }

        .barangay-name {
            font-family: 'Playfair Display', serif;
            font-size: 3.2rem;
            font-weight: 800;
            margin: 0;
            text-shadow: 0 4px 10px rgba(0,0,0,0.4);
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            color: var(--navy);
            font-size: 2.4rem;
            font-weight: 700;
            position: relative;
            padding-bottom: 1rem;
            margin-bottom: 2.5rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 80px;
            height: 6px;
            background: var(--gold);
            border-radius: 3px;
        }

        .officer-card {
            text-align: center;
            transition: transform 0.3s ease;
        }

        .officer-card:hover {
            transform: translateY(-10px);
        }

        .officer-img {
            width: 160px;
            height: 160px;
            object-fit: cover;
            border-radius: 50%;
            border: 6px solid white;
            box-shadow: 0 15px 35px rgba(15,52,96,0.2);
            transition: all 0.3s ease;
        }

        .officer-card:hover .officer-img {
            border-color: var(--gold);
            box-shadow: 0 20px 45px rgba(15,52,96,0.3);
        }

        .officer-name {
            font-weight: 600;
            color: var(--navy);
            margin: 1rem 0 0.4rem;
            font-size: 1.15rem;
        }

        .officer-role {
            color: var(--gray);
            font-size: 0.95rem;
        }

        .content-card {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border-left: 5px solid var(--gold);
            transition: all 0.3s ease;
            height: 100%;
        }

        .content-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12);
        }

        .event-date {
            background: var(--gold);
            color: var(--navy);
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .back-btn {
            background: var(--navy);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.4s ease;
            box-shadow: 0 10px 30px rgba(15,52,96,0.4);
        }

        .back-btn:hover {
            background: var(--gold);
            color: var(--navy);
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(234,179,8,0.4);
        }

        @media (max-width: 768px) {
            .barangay-name { font-size: 2.5rem; }
            .logo-circle { width: 140px; height: 140px; }
            .officer-img { width: 130px; height: 130px; }
        }
    </style>
</head>
<body>

    <!-- Hero Header -->
    <section class="hero-section">
        <div class="container">
            <?php if ($logoPath): ?>
                <img src="<?= htmlspecialchars($logoPath) ?>" class="logo-circle" alt="Barangay <?= htmlspecialchars($name_clean) ?> Seal">
            <?php endif; ?>
            <h1 class="barangay-name">Barangay <?= htmlspecialchars($name_clean) ?></h1>
            <p class="lead mt-3 opacity-90" style="max-width: 800px; margin: 0 auto; font-size: 1.1rem;">
                <?= nl2br(htmlspecialchars($description)) ?>
            </p>
        </div>
    </section>

    <div class="container my-5">

        <!-- SK Officers -->
        <section class="mb-5">
            <h2 class="section-title">SK Officials</h2>
            <?php if ($officers): ?>
                <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5 g-4 justify-content-center">
                    <?php foreach ($officers as $o): ?>
                        <div class="col">
                            <div class="officer-card">
                                <img src="../../images/officers/<?= htmlspecialchars($o['image'] ?? 'default-officer.png') ?>"
                                     class="officer-img"
                                     alt="<?= htmlspecialchars($o['name']) ?>"
                                     onerror="this.src='../../images/officers/default-officer.png'">
                                <div class="officer-name"><?= htmlspecialchars($o['name']) ?></div>
                                <div class="officer-role"><?= htmlspecialchars($o['role']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted fst-italic">No SK officials listed yet.</p>
            <?php endif; ?>
        </section>

        <!-- Projects -->
        <section class="mb-5">
            <h2 class="section-title">Youth Programs & Projects</h2>
            <?php if ($projects): ?>
                <div class="row g-4">
                    <?php foreach ($projects as $p): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="content-card">
                                <h5 class="fw-bold" style="color: var(--navy);"><?= htmlspecialchars($p['title']) ?></h5>
                                <p class="text-muted small"><?= nl2br(htmlspecialchars($p['description'])) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted fst-italic">No approved projects at this time.</p>
            <?php endif; ?>
        </section>

        <!-- Events -->
        <section class="mb-5">
            <h2 class="section-title">Events & Activities</h2>
            <?php if ($events): ?>
                <div class="row g-4">
                    <?php foreach ($events as $e): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="content-card d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="fw-bold mb-0" style="color: var(--navy);"><?= htmlspecialchars($e['title']) ?></h5>
                                    <span class="event-date"><?= date('M j, Y', strtotime($e['event_date'])) ?></span>
                                </div>
                                <p class="text-muted small flex-grow-1"><?= nl2br(htmlspecialchars($e['description'])) ?></p>
                                <?php if ($e['venue']): ?>
                                    <p class="mt-3 mb-0"><strong>Venue:</strong> <?= htmlspecialchars($e['venue']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted fst-italic">No upcoming events scheduled.</p>
            <?php endif; ?>
        </section>

        <!-- Back Button -->
        <div class="text-center mt-5">
            <a href="https://skfederation-of-maloloscity.com/#barangays" class="btn back-btn px-5">
                ← Back to All Barangays
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>