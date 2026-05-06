<?php
/* --------------------------------------------------------------
   1. DB connection (optional – you can remove if not using DB)
   -------------------------------------------------------------- */
require_once __DIR__ . '/../connection.php';

/* --------------------------------------------------------------
   2. Full list of 51 barangays
   -------------------------------------------------------------- */
$barangays = [
    ['name' => 'Anilao',      'img' => 'Anilao.png'],
    ['name' => 'Atlag',       'img' => 'Atlag.png'],
    ['name' => 'Babatnin',    'img' => 'Babatnin.png'],
    ['name' => 'Bagna',       'img' => 'Bagna.png'],
    ['name' => 'Bagong Bayan','img' => 'Bagong Bayan.png'],
    ['name' => 'Balayong',    'img' => 'Balayong.png'],
    ['name' => 'Balite',      'img' => 'Balite.png'],
    ['name' => 'Bangkal',     'img' => 'Bangkal.png'],
    ['name' => 'Barihan',     'img' => 'Barihan.png'],
    ['name' => 'Bulihan',     'img' => 'Bulihan.png'],
    ['name' => 'Bungahan',    'img' => 'Bungahan.png'],
    ['name' => 'Caingin',     'img' => 'Caingin.png'],
    ['name' => 'Calero',      'img' => 'Calero.png'],
    ['name' => 'Caliligawan', 'img' => 'Caliligawan.png'],
    ['name' => 'Canalate',    'img' => 'Canalate.png'],
    ['name' => 'Caniogan',    'img' => 'Caniogan.png'],
    ['name' => 'Cofradia',    'img' => 'Cofradia.png'],
    ['name' => 'Catmon',      'img' => 'Catmon.png'],
    ['name' => 'Dakila',      'img' => 'Dakila.png'],
    ['name' => 'Guinhawa',    'img' => 'Guinhawa.png'],
    ['name' => 'Liang',       'img' => 'Liang.png'],
    ['name' => 'Ligas',       'img' => 'Ligas.png'],
    ['name' => 'Longos',      'img' => 'Longos.png'],
    ['name' => 'Lugam',       'img' => 'Lugam.png'],
    ['name' => 'Look 1st',    'img' => 'Look 1st.png'],
    ['name' => 'Look 2nd',    'img' => 'Look 2nd.png'],
    ['name' => 'Mabolo',      'img' => 'Mabolo.png'],
    ['name' => 'Mambog',      'img' => 'Mambog.png'],
    ['name' => 'Masile',      'img' => 'Masile.png'],
    ['name' => 'Matimbo',     'img' => 'Matimbo.png'],
    ['name' => 'Mojon',       'img' => 'Mojon.png'],
    ['name' => 'Namayan',     'img' => 'Namayan.png'],
    ['name' => 'Niugan',      'img' => 'Niugan.png'],
    ['name' => 'Pamarawan',   'img' => 'Pamarawan.png'],
    ['name' => 'Panasahan',   'img' => 'Panasahan.png'],
    ['name' => 'Pinagbakahan','img' => 'Pinagbakahan.png'],
    ['name' => 'San Agustin', 'img' => 'San Agustin.png'],
    ['name' => 'San Gabriel', 'img' => 'San Gabriel.png'],
    ['name' => 'San Juan',    'img' => 'San Juan.png'],
    ['name' => 'San Pablo',   'img' => 'San Pablo.png'],
    ['name' => 'Santiago',    'img' => 'Santiago.png'],
    ['name' => 'Santor',      'img' => 'Santor.png'],
    ['name' => 'San Vicente', 'img' => 'San Vicente.png'],
    ['name' => 'Santisima Trinidad', 'img' => 'Santisima Trinidad.png'],
    ['name' => 'Santo Cristo', 'img' => 'Santo Cristo.png'],
    ['name' => 'Santo Rosario','img' => 'Santo Rosario.png'],
    ['name' => 'Santo Niño',  'img' => 'Santo Niño.png'],
    ['name' => 'Sumapang Bata', 'img' => 'Sumapang Bata.png'],
    ['name' => 'Sumapang Matanda', 'img' => 'Sumapang Matanda.png'],
    ['name' => 'Taal',        'img' => 'Taal.png'],
    ['name' => 'Tikay',       'img' => 'Tikay.png'],
];
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Our Barangays</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --primary: #198754;
            --hover: #146c43;
        }
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .section-title {
            font-weight: 700;
            color: var(--primary);
        }
        .barangay-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.8rem;
            padding: 2rem 0;
        }
        .barangay-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
            transition: all .3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .barangay-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 16px 32px rgba(0,0,0,.15);
        }
        .barangay-img-container {
            height: 180px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .barangay-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .barangay-name {
            padding: 1rem;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            color: #212529;
            background: linear-gradient(to top, rgba(255,255,255,.95), white);
        }
        .barangay-name::after {
            content: '';
            display: block;
            width: 40px;
            height: 3px;
            background: var(--primary);
            margin: 0.5rem auto 0;
            border-radius: 2px;
        }
        @media (max-width: 576px) {
            .barangay-grid { grid-template-columns: 1fr 1fr; gap: 1rem; }
        }
    </style>
</head>
<body>

<!-- HERO SECTION -->
<section class="py-5 bg-white">
    <div class="container text-center" data-aos="fade-up">
        <h1 class="display-5 fw-bold text-success mb-3">Our Barangays</h1>
        <p class="lead text-muted">Explore the heart of our community — 51 vibrant barangays of Malolos</p>
    </div>
</section>

<!-- BARANGAY GRID -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="barangay-grid" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($barangays as $b):
                $imgPath = "images/SK MALOLOS LOGO/" . $b['img'];
                $profileUrl = "sections/barangay/barangay.php?name=" . urlencode($b['name']);
            ?>
                <a href="<?= $profileUrl ?>" class="barangay-card">
                    <div class="barangay-img-container">
                        <img src="<?= $imgPath ?>"
                             class="barangay-img"
                             alt="<?= htmlspecialchars($b['name']) ?> Logo"
                             onerror="this.outerHTML=''; this.onerror=null;">
                    </div>
                    <div class="barangay-name"><?= htmlspecialchars($b['name']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        once: true
    });
</script>
</body>
</html>