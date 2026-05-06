<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In | SK Federation</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
  <div class="signin-container">
    <img src="images/sk-logo.png" alt="SK Logo">
    <h2>Sign In</h2>

    <form method="POST" action="auth/auth_user.php">
      <input type="hidden" name="user_login" value="1">

      <div class="form-group">
        <label>Sign in as</label>
        <select name="role" required>
          <option value="">Select Role</option>
          <option value="admin">Admin</option>
          <option value="sk_chairperson">SK Chairperson</option>
        </select>
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>
      </div>

      <div class="form-group position-relative">
        <label>Password</label>
        <input type="password" name="password" id="signinPassword" placeholder="Enter your password" required>
        <span class="toggle-eye" onclick="togglePassword('signinPassword', event)">
          <i class="bi bi-eye"></i>
        </span>
      </div>

      <button type="submit" class="btn-primary">Sign In</button>

      <p class="text-center mt-3">
        Don’t have an account?
        <a href="signup.php">Sign Up</a>
      </p>
    </form>
  </div>

  <script>
    function togglePassword(id, event) {
      const input = document.getElementById(id);
      const icon = event.target;
      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("bi-eye", "bi-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("bi-eye-slash", "bi-eye");
      }
    }
  </script>
</body>
</html>
