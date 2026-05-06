<?php
// Enable error display temporarily (REMOVE THIS BLOCK AFTER FIXING)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session and load DB connection
session_start();
require_once 'connection.php';  // Changed to require_once for safety
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SK Federation of Malolos</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <!-- BACK TO TOP BUTTON STYLE -->
  <style>
    #backToTop {
      position: fixed;
      bottom: 30px;
      right: 30px;
      z-index: 9999;
      width: 50px;
      height: 50px;
      background: #16213e;
      color: white;
      border: none;
      border-radius: 50%;
      font-size: 24px;
      display: none;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
      cursor: pointer;
      transition: all 0.3s ease;
    }
    #backToTop:hover {
      background: #1e40af;
      transform: translateY(-5px);
    }
    #backToTop.show {
      display: flex;
    }
  </style>
</head>

<body>
  <?php include('includes/header.php'); ?>

  <main>
    <section id="home" data-aos="fade-up">
      <?php include('sections/home.php'); ?>
    </section>

    <section id="about" data-aos="fade-up" data-aos-delay="100">
      <?php include('sections/about.php'); ?>
    </section>

    <section id="barangays" data-aos="fade-up" data-aos-delay="200">
      <?php include('sections/barangays.php'); ?>
    </section>

    <section id="projects" data-aos="fade-up" data-aos-delay="300">
      <?php include('sections/projects.php'); ?>
    </section>

    <section id="contact" data-aos="fade-up" data-aos-delay="400">
      <?php include('sections/contact.php'); ?>
    </section>
  </main>

  <?php include('includes/footer.php'); ?>
  <?php include('includes/modal.php'); ?>

  <!-- BACK TO TOP BUTTON -->
  <button id="backToTop" title="Back to Top">
    <i class="bi bi-arrow-up"></i>
  </button>

  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init({
      duration: 1000,
      easing: 'ease-in-out',
      once: true,
      offset: 100
    });

    document.addEventListener('DOMContentLoaded', function () {
      const signupBtn = document.getElementById('signupBtn');
      const signinBtn = document.getElementById('signinBtn');
      const signupModal = document.getElementById('signupModal');
      const signinModal = document.getElementById('signinModal');

      function openModal(modal) { modal.style.display = 'flex'; }
      function closeModal(modal) { modal.style.display = 'none'; }

      if (signupBtn) signupBtn.addEventListener('click', () => openModal(signupModal));
      if (signinBtn) signinBtn.addEventListener('click', () => openModal(signinModal));

      window.addEventListener('click', (e) => {
        if (e.target === signupModal) closeModal(signupModal);
        if (e.target === signinModal) closeModal(signinModal);
      });
    });

    // BACK TO TOP FUNCTIONALITY
    const backToTop = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
      if (window.scrollY > 300) {
        backToTop.classList.add('show');
      } else {
        backToTop.classList.remove('show');
      }
    });

    backToTop.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  </script>
  <script src="scripts.js"></script>
</body>
</html>