<?php
// Assuming $pdo is already available from included connection
$home = $pdo->query("SELECT * FROM site_home LIMIT 1")->fetch(PDO::FETCH_ASSOC);

// Default fallback background (navy-blue gradient)
$hero_bg_style = "background: linear-gradient(135deg, #0f172a 0%, #1e40af 100%);";

// If there's a background image in DB, override
if (!empty($home['hero_bg_img'])) {
    $img_path = htmlspecialchars($home['hero_bg_img']);
    // Assuming images are stored in public_html/images/ or similar
    $hero_bg_style = "background: url('$img_path') no-repeat center center / cover;";
}
?>

<div class="hero-section" style="<?= $hero_bg_style ?>">
  <div class="hero-overlay" style="background: rgba(15, 23, 42, 0.55);"></div>
  
  <div class="hero-content">
    <h1>
      <?= htmlspecialchars($home['hero_title'] ?? 'Welcome to SK Federation of Malolos') ?>
    </h1>
    
    <p class="hero-subtitle">
      <?= nl2br(htmlspecialchars($home['hero_subtitle'] ?? "Empowering the youth,\nshaping the future of our communities.")) ?>
    </p>
  </div>
</div>

<style>
.hero-section {
  position: relative;
  height: 100vh;
  min-height: 600px;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.hero-overlay {
  position: absolute;
  inset: 0;
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
  max-width: 900px;
  padding: 0 2rem;
}

.hero-content h1 {
  font-size: 3.8rem;
  font-weight: 800;
  margin-bottom: 1.5rem;
  text-shadow: 0 4px 12px rgba(0,0,0,0.6);
}

.hero-subtitle {
  font-size: 1.5rem;
  line-height: 1.6;
  opacity: 0.95;
  max-width: 800px;
  margin: 0 auto;
}

@media (max-width: 768px) {
  .hero-content h1 { font-size: 2.8rem; }
  .hero-subtitle { font-size: 1.3rem; }
}
</style>