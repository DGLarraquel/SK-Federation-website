<?php
// Load database connection
require_once __DIR__ . '/../connection.php';

// Fetch the About description from DB
$desc_stmt = $pdo->query("SELECT description FROM site_about WHERE id = 1");
$about_description = $desc_stmt->fetchColumn() ?: 'No description available at this time.';
?>

<style>
/* ---------- ANCHOR OFFSET (Fixes title under header) ---------- */
#about-anchor {
  display: block;
  height: 120px;
  margin-top: -120px;
  visibility: hidden;
  pointer-events: none;
}

/* ---------- GALLERY WRAPPER (Centers Everything) ---------- */
.officers-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  padding: 0 20px;
  box-sizing: border-box;
}

/* ---------- SCROLLABLE GALLERY (Single Row) ---------- */
.about-gallery {
  display: flex;
  overflow-x: auto;
  gap: 24px;
  padding: 20px 0;
  scroll-snap-type: x mandatory;
  scrollbar-width: thin;
  scrollbar-color: #3498db #f1f1f1;
  width: 100%;
  max-width: 1400px;
  -webkit-overflow-scrolling: touch;
}
.about-gallery::-webkit-scrollbar {
  height: 8px;
}
.about-gallery::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}
.about-gallery::-webkit-scrollbar-thumb {
  background: #3498db;
  border-radius: 4px;
}
.about-gallery::-webkit-scrollbar-thumb:hover {
  background: #2980b9;
}

/* Hide scrollbar on mobile */
@media (max-width: 768px) {
  .about-gallery {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }
  .about-gallery::-webkit-scrollbar {
    display: none;
  }
}

/* ---------- OFFICER CARD ---------- */
.about-image-card {
  min-width: 280px;
  max-width: 300px;
  flex: 0 0 auto;
  scroll-snap-align: center;
  box-shadow: 0 6px 16px rgba(0,0,0,0.12);
  border-radius: 16px;
  overflow: hidden;
  background: #fff;
  transition: all 0.35s ease;
  display: flex;
  flex-direction: column;
}
.about-image-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 16px 32px rgba(0,0,0,0.18);
}

/* Image */
.about-img {
  width: 100%;
  height: 260px;
  object-fit: cover;
  display: block;
  border-bottom: 3px solid #3498db;
}

/* Text Overlay */
.about-img-overlay {
  padding: 16px;
  background: #fff;
  text-align: center;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.about-img-overlay h3 {
  margin: 0 0 4px;
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.2rem;
  line-height: 1.3;
}
.about-img-overlay p.role {
  margin: 0 0 3px;
  color: #3498db;
  font-weight: 600;
  font-size: 0.95rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.about-img-overlay p.barangay {
  margin: 0;
  color: #7f8c8d;
  font-size: 0.88rem;
  font-style: italic;
}

/* ---------- RESPONSIVE ---------- */
@media (max-width: 768px) {
  .about-gallery {
    gap: 16px;
    padding: 16px 0;
  }
  .about-image-card {
    min-width: 260px;
    max-width: 280px;
  }
  .about-img {
    height: 240px;
  }
  .about-img-overlay {
    padding: 14px;
  }
  .about-img-overlay h3 {
    font-size: 1.1rem;
  }
}
</style>

<!-- ANCHOR POINT: Keeps "About Us" visible under fixed header -->
<a id="about" href="#about" style="display:block; height:120px; margin-top:-120px; visibility:hidden; pointer-events:none;"></a>

<section class="p-5 bg-white" data-aos="fade-up" data-aos-delay="100">
  <div class="container about-container text-center">

    <!-- MAIN TITLE -->
    <h2 class="text-center text-success mb-4 fw-bold">About Us</h2>

    <!-- DYNAMIC DESCRIPTION (loaded from DB) -->
    <p class="about-text lead" style="max-width: 800px; margin: 0 auto 2.5rem; white-space: pre-wrap;">
      <?= nl2br(htmlspecialchars($about_description)) ?>
    </p>

    <!-- OFFICERS TITLE -->
    <h3 class="mb-5 fw-bold text-center" style="color: #1a2a6c; font-size: 1.8rem; letter-spacing: 0.8px;">
      Sangguniang Kabataan Federation Officers
    </h3>

    <!-- DYNAMIC OFFICERS GALLERY -->
    <div class="officers-wrapper">
      <div class="about-gallery">
        <?php
        $stmt = $pdo->query("SELECT * FROM sk_officers ORDER BY id ASC");
        $hasOfficers = false;

        while ($officer = $stmt->fetch(PDO::FETCH_ASSOC)):
          $hasOfficers = true;
        ?>
          <div class="about-image-card">
            <img src="<?= htmlspecialchars($officer['photo'] ?? 'images/officers/default-placeholder.jpg') ?>" 
                 alt="<?= htmlspecialchars($officer['full_name']) ?>" 
                 class="about-img"
                 onerror="this.src='images/officers/default-placeholder.jpg';">
            <div class="about-img-overlay">
              <h3><?= htmlspecialchars($officer['full_name']) ?></h3>
              <p class="role"><?= htmlspecialchars($officer['position']) ?></p>
              <p class="barangay"><?= htmlspecialchars($officer['barangay']) ?></p>
            </div>
          </div>
        <?php endwhile; ?>

        <?php if (!$hasOfficers): ?>
          <div style="padding: 60px 20px; color: #6c757d; font-style: italic; text-align: center; width: 100%;">
            No officers added yet. Add them in the admin panel.
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>