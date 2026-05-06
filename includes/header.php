<header class="site-header" style="background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); padding: 1rem 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
  <div class="header-container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
    
    <!-- Logo -->
    <div class="logo" style="display: flex; align-items: center; gap: 12px;">
      <img src="images/sk-logo.png" alt="SK Federation Logo" style="height: 50px; width: auto;" />
      <h1 style="margin: 0; font-size: 1.6rem; font-weight: 600; color: #1a1a1a;">SK Federation</h1>
    </div>

    <!-- Navigation -->
    <nav class="navbar" style="flex: 1; display: flex; justify-content: center;">
      <ul style="list-style: none; display: flex; gap: 2.5rem; margin: 0; padding: 0;">
        <li><a href="#home" style="text-decoration: none; color: #2c3e50; font-weight: 500; font-size: 1rem; padding: 8px 0; position: relative; transition: color 0.3s;">Home</a></li>
        <li><a href="#about" style="text-decoration: none; color: #2c3e50; font-weight: 500; font-size: 1rem; padding: 8px 0; position: relative; transition: color 0.3s;">About Us</a></li>
        <li><a href="#barangays" style="text-decoration: none; color: #2c3e50; font-weight: 500; font-size: 1rem; padding: 8px 0; position: relative; transition: color 0.3s;">Our Barangays</a></li>
        <li><a href="#projects" style="text-decoration: none; color: #2c3e50; font-weight: 500; font-size: 1rem; padding: 8px 0; position: relative; transition: color 0.3s;">Projects</a></li>
        <li><a href="#contact" style="text-decoration: none; color: #2c3e50; font-weight: 500; font-size: 1rem; padding: 8px 0; position: relative; transition: color 0.3s;">Contact</a></li>
      </ul>
    </nav>

    <!-- Auth Section -->
    <div class="auth-section" style="display: flex; align-items: center; gap: 1rem;">

      <!-- Sign In Dropdown -->
      <div class="signin-dropdown" style="position: relative; display: inline-block;">
        <button class="signin-btn" id="signinToggle" style="background: none; border: 1.5px solid #3498db; color: #3498db; padding: 10px 18px; font-size: 0.95rem; font-weight: 500; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: all 0.3s;">
          <i class="bi bi-box-arrow-in-right"></i> Sign In <span style="font-size: 0.8rem;">▾</span>
        </button>
        <div class="signin-menu" id="signinMenu" style="display: none; position: absolute; right: 0; top: 100%; min-width: 180px; background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); overflow: hidden; z-index: 1000; margin-top: 8px;">
          <a href="#" class="open-login" data-target="admin" style="display: block; padding: 12px 16px; color: #2c3e50; text-decoration: none; font-weight: 500; font-size: 0.95rem; transition: background 0.2s;">Admin</a>
          <a href="#" class="open-login" data-target="user" style="display: block; padding: 12px 16px; color: #2c3e50; text-decoration: none; font-weight: 500; font-size: 0.95rem; transition: background 0.2s; border-top: 1px solid #f0f0f0;">SK Chairperson</a>
        </div>
      </div>

     <!-- Sign Up Button (CORRECT: Only one, links to signup.php) -->
<a href="signup.php" id="signupBtn" style="background-color: #3498db; color: white; border: none; padding: 10px 20px; font-size: 0.95rem; font-weight: 500; border-radius: 8px; cursor: pointer; transition: background 0.3s, transform 0.2s; text-decoration: none; display: inline-block;">
  Sign Up
</a>
    </div>
  </div>
</header>

<!-- JavaScript for Dropdown Toggle -->
<script>
  const signinToggle = document.getElementById('signinToggle');
  const signinMenu = document.getElementById('signinMenu');

  signinToggle.addEventListener('click', function(e) {
    e.stopPropagation();
    signinMenu.style.display = signinMenu.style.display === 'block' ? 'none' : 'block';
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', function() {
    signinMenu.style.display = 'none';
  });

  // Prevent menu from closing when clicking inside
  signinMenu.addEventListener('click', function(e) {
    e.stopPropagation();
  });

  // Hover effects for nav links
  document.querySelectorAll('.navbar a').forEach(link => {
    link.addEventListener('mouseenter', () => {
      link.style.color = '#3498db';
      link.style.transform = 'translateY(-1px)';
    });
    link.addEventListener('mouseleave', () => {
      link.style.color = '#2c3e50';
      link.style.transform = 'translateY(0)';
    });
  });

  // Active state underline
  document.querySelectorAll('.navbar a').forEach(link => {
    if (link.getAttribute('href') === '#home') {
      link.style.color = '#3498db';
      link.style.position = 'relative';
      link.style.setProperty('--after-width', '100%');
    }
    link.addEventListener('mouseenter', () => {
      link.style.setProperty('--after-width', '100%');
    });
    link.addEventListener('mouseleave', () => {
      if (link.getAttribute('href') !== '#home') {
        link.style.setProperty('--after-width', '0');
      }
    });
  });
</script>

<!-- Optional: Add underline effect on hover -->
<style>
  .navbar a::after {
    content: '';
    position: absolute;
    width: var(--after-width, 0);
    height: 2px;
    bottom: 0;
    left: 0;
    background-color: #3498db;
    transition: width 0.3s ease;
  }
  .signin-menu a:hover {
    background-color: #f8f9fa;
  }
  #signupBtn:hover {
    background-color: #2980b9;
    transform: translateY(-1px);
  }
  .signin-btn:hover {
    background-color: #f0f8ff;
    border-color: #2980b9;
    color: #2980b9;
  }
</style>