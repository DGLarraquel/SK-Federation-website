<?php
session_start();

define('BG_IMAGE', '../images/skfederation-bg.jpg');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'secret123') {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login – SK Federation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --bg-overlay: linear-gradient(135deg, rgba(30,80,150,0.78), rgba(20,60,120,0.82));
            --card-bg: rgba(255,255,255,0.94);
            --text: #2c3e50;
            --border: #dfe6e9;
            --cancel: #e74c3c;
            --cancel-hover: #c0392b;
        }
        * { box-sizing: border-box; margin:0; padding:0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('<?= BG_IMAGE ?>') center/cover fixed;
            display: flex; align-items: center; justify-content: center;
            min-height: 100vh; position: relative; overflow: hidden;
        }
        body::before {
            content:''; position:absolute; inset:0;
            background: var(--bg-overlay); z-index:1; backdrop-filter: blur(1.5px);
        }
        .login-card {
            background: var(--card-bg); padding: 2.8rem; border-radius: 22px;
            box-shadow: 0 28px 65px rgba(0,0,0,0.28); max-width: 440px; width: 100%;
            text-align: center; position: relative; z-index: 2;
            border: 1px solid rgba(255,255,255,0.35); backdrop-filter: blur(16px);
        }
        .logo { width:115px; height:115px; margin:0 auto 1.5rem; border-radius:50%; overflow:hidden; border:6px solid #fff; box-shadow:0 10px 30px rgba(52,152,219,.42); }
        .logo:hover { transform:scale(1.07) rotate(2deg); }
        .logo img { width:100%; height:100%; object-fit:contain; }
        h2 { margin-bottom:1.7rem; color:var(--text); font-weight:700; font-size:1.85rem; }

        .form-group { margin-bottom:1.3rem; text-align:left; }
        label { display:block; margin-bottom:.7rem; color:var(--text); font-weight:600; }

        .password-wrapper { position:relative; }

        input {
            width:100%; padding:1.05rem 50px 1.05rem 1.05rem;
            border:2px solid var(--border); border-radius:14px; font-size:1rem;
            background:#fff; transition:all .35s;
        }
        input:focus {
            outline:none; border-color:var(--primary);
            box-shadow:0 0 0 5px rgba(52,152,219,.25); transform:translateY(-2px);
        }

        /* Eye Toggle Icon */
        .toggle-password {
            position: absolute;
            top: 50%; right: 14px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 26px; height: 26px;
            display: flex; align-items: center; justify-content: center;
            color: #95a5a6;
            transition: color .3s;
            z-index: 5;
        }
        .toggle-password:hover { color: var(--primary); }

        .toggle-password svg { width: 22px; height: 22px; }

        .error {
            background:#f8d7da; color:#721c24; padding:1rem; border-radius:12px;
            margin:1.2rem 0; border:1px solid #f5c6cb; animation:shake .5s;
        }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-5px)} 75%{transform:translateX(5px)} }

        .button-group { display:flex; gap:1rem; margin-top:1.5rem; }
        button { flex:1; padding:1.1rem; border:none; border-radius:14px; font-weight:700; cursor:pointer; text-transform:uppercase; letter-spacing:1px; transition:.45s; }
        button[type="submit"] { background:linear-gradient(135deg,var(--primary),var(--primary-dark)); color:#fff; box-shadow:0 8px 22px rgba(52,152,219,.38); }
        button[type="submit"]:hover { transform:translateY(-4px); box-shadow:0 15px 32px rgba(52,152,219,.5); }
        button.cancel { background:var(--cancel); color:#fff; }
        button.cancel:hover { background:var(--cancel-hover); transform:translateY(-4px); }

        @media (max-width:480px) {
            .login-card { margin:1rem; padding:2.2rem; }
            .logo { width:95px; height:95px; }
            .button-group { flex-direction:column; }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">
        <img src="../images/sk-logo.png" alt="SK Federation Logo">
    </div>
    <h2>Admin Sign In</h2>

    <?php if (isset($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <div class="toggle-password" onclick="togglePassword()">
                    <!-- Eye (hidden) -->
                    <svg class="eye-icon" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M572.52 241.4C518.29 135.59 410.73 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.27 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"/>
                    </svg>
                    <!-- Eye-slash (visible when password shown) -->
                    <svg class="eye-slash-icon" viewBox="0 0 640 512" fill="currentColor" style="display:none;">
                        <path d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.27 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144 144 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zM203.55 240a95.92 95.92 0 0 0 140.5 83.4l-138.15-106.73A95.92 95.92 0 0 0 203.55 240z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="button-group">
            <button type="submit">Login</button>
            <button type="button" class="cancel" onclick="window.location.href='../index.php'">Cancel</button>
        </div>
    </form>
</div>

<script>
function togglePassword() {
    const pwd = document.getElementById('password');
    const eye = document.querySelector('.eye-icon');
    const eyeSlash = document.querySelector('.eye-slash-icon');

    if (pwd.type === 'password') {
        pwd.type = 'text';
        eye.style.display = 'none';
        eyeSlash.style.display = 'block';
    } else {
        pwd.type = 'password';
        eye.style.display = 'block';
        eyeSlash.style.display = 'none';
    }
}
</script>

</body>
</html>