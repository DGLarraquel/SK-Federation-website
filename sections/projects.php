<?php
/* -------------------------------------------------------------
   SECURE PDO CONNECTION (keep your credentials – just moved to top)
   ------------------------------------------------------------- */
$servername = "localhost";
$username   = "u601734414_sk_user";
$password   = "Federation2025";
$database   = "u601734414_sk_federation";

$dsn = "mysql:host=$servername;dbname=$database;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    error_log("DB Error: " . $e->getMessage());
    die('<div class="text-center py-5"><h3 class="text-danger">Service temporarily down. Please try again later.</h3></div>');
}

/* -------------------------------------------------------------
   FETCH APPROVED PROJECTS (newest first)
   ------------------------------------------------------------- */
$sql = "
    SELECT p.title,
           b.name      AS barangay,
           p.description,
           p.image
    FROM   projects p
    LEFT   JOIN barangays b ON p.barangay_id = b.id
    WHERE  p.status = 'approved'
       AND p.approved_at IS NOT NULL
    ORDER  BY p.approved_at DESC
";

$stmt     = $pdo->query($sql);
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="projects" class="projects-section py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-success fw-bold">Our Projects & Initiatives</h2>
            <p class="fs-5 text-muted max-width-800 mx-auto">
                Empowering youth through impactful community‑driven programs and sustainable initiatives.
            </p>
        </div>

        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4 projects-grid" 
             data-aos="fade-up" 
             data-aos-delay="200">
            <?php if ($projects): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="col">
                        <div class="project-card h-100 bg-white rounded-4 shadow-sm overflow-hidden hover-lift">
                            <img
                                src="<?= htmlspecialchars('sections/uploads/projects/' . basename($project['image'] ?? 'project-placeholder.jpg')) ?>"
                                alt="<?= htmlspecialchars($project['title'] ?? 'Project') ?>"
                                class="project-img w-100"
                                onerror="this.src='images/project-placeholder.jpg'; this.onerror=null;"
                                loading="lazy">
                            <div class="project-content p-4">
                                <h3 class="h5 fw-bold text-primary mb-2">
                                    <?= htmlspecialchars($project['title'] ?? 'Untitled Project') ?>
                                </h3>
                                <?php if (!empty($project['barangay'])): ?>
                                    <p class="text-success small mb-2">
                                        <strong>Location:</strong> <?= htmlspecialchars($project['barangay']) ?>
                                    </p>
                                <?php endif; ?>
                                <p class="text-muted small line-clamp-4">
                                    <?= nl2br(htmlspecialchars($project['description'] ?? 'No description available.')) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="fs-3 text-muted">No projects uploaded yet. Stay tuned!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Inline Styles (updated for better 4-column appearance) -->
<style>
    .max-width-800 { max-width: 800px; }
    .hover-lift { transition: transform .3s ease, box-shadow .3s ease; }
    .hover-lift:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,100,200,.15)!important; }
    .project-img { 
        height: 200px; 
        object-fit: cover; 
    }
    .project-content { 
        padding: 1.25rem; 
    }
    .line-clamp-4 {
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Adjust image height for different screen sizes */
    @media (min-width: 1400px) {
        .project-img { height: 220px; }
    }
    @media (max-width: 1199px) {
        .project-img { height: 190px; }
    }
    @media (max-width: 768px) {
        .project-img { height: 180px; }
        .project-content { padding: 1rem !important; }
    }
</style>

<?php
/* -------------------------------------------------------------
   Load AOS & Bootstrap only once (keeps your existing logic)
   ------------------------------------------------------------- */
if (!isset($loaded_scripts)):
?>
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({duration:800,easing:'ease-out-quart',once:true,offset:100});
    </script>
<?php
    $loaded_scripts = true;
endif;
?>