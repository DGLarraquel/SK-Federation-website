<!-- includes/modal.php -->
<!-- ADMIN SIGN IN MODAL -->
<div id="adminSigninModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeAdminSignin">×</span>
    <h2>Admin Sign In</h2>
    <p style="text-align:center; color:#666; margin:1rem 0;">
      You will be redirected to the secure admin login.
    </p>
    <button onclick="window.location.href='admin/auth_admin.php'" class="btn btn-primary">
      Go to Admin Login
    </button>
  </div>
</div>

<!-- SK CHAIRPERSON SIGN IN MODAL -->
<div id="skChairSigninModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeSkChairSignin">×</span>
    <h2>SK Chairperson Sign In</h2>
    <p style="text-align:center; color:#666; margin:1rem 0;">
      You will be redirected to the secure chairperson login.
    </p>
    <button onclick="window.location.href='auth/auth_sk_chair.php'" class="btn btn-success">
      Go to Chairperson Login
    </button>
  </div>
</div>

<style>
  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
  }
  .modal-content {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    width: 90%;
    max-width: 380px;
    text-align: center;
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
  }
  .close {
    position: absolute;
    top: 12px; right: 18px;
    font-size: 28px;
    font-weight: bold;
    color: #aaa;
    cursor: pointer;
  }
  .close:hover { color: #e74c3c; }
  .btn {
    width: 100%;
    padding: 0.9rem;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 0.5rem;
  }
  .btn-primary { background: #3498db; color: white; }
  .btn-success { background: #27ae60; color: white; }
  .btn:hover { opacity: 0.9; }
</style>

<script>
  document.querySelectorAll('.open-login').forEach(el => {
    el.onclick = function(e) {
      e.preventDefault();
      const target = this.dataset.target;
      if (target === 'admin') {
        document.getElementById('adminSigninModal').style.display = 'flex';
      } else if (target === 'user') {
        document.getElementById('skChairSigninModal').style.display = 'flex';
      }
    };
  });

  document.getElementById('closeAdminSignin').onclick = () => {
    document.getElementById('adminSigninModal').style.display = 'none';
  };
  document.getElementById('closeSkChairSignin').onclick = () => {
    document.getElementById('skChairSigninModal').style.display = 'none';
  };

  window.onclick = function(e) {
    if (e.target.id === 'adminSigninModal') e.target.style.display = 'none';
    if (e.target.id === 'skChairSigninModal') e.target.style.display = 'none';
  };
</script>