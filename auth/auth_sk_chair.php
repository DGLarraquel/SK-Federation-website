<?php
session_start();

$conn_file = '../connection.php';
if (!file_exists($conn_file)) {
    die("Error: connection.php not found!");
}
include($conn_file);

if (!isset($pdo) || !($pdo instanceof PDO)) {
    die("Database connection failed. Please check connection.php");
}

if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

$error = '';

$barangays = [
    "Anilao","Atlag","Babatnin","Bagna","Bagong Bayan","Balayong","Balite","Bangkal",
    "Barihan","Bulihan","Bungahan","Caingin","Calero","Caliligawan","Canalate","Caniogan",
    "Catmon","Cofradia","Dakila","Guinhawa","Ligas","Liang","Longos","Look 1st",
    "Look 2nd","Lugam","Mabolo","Mambog","Masile","Matimbo","Mojon","Namayan",
    "Niugan","Pamarawan","Panasahan","Pinagbakahan","San Agustin","San Gabriel",
    "San Juan","San Pablo","San Vicente","Santiago","Santor","Santisima Trinidad",
    "Sto. Cristo","Sto. Niño","Santo Rosario","Sumapang Bata","Sumapang Matanda",
    "Taal","Tikay"
];
sort($barangays);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $barangay = trim($_POST['barangay'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($barangay) || empty($password)) {
        $error = "Please select your barangay and enter your password.";
    } elseif (!in_array($barangay, $barangays)) {
        $error = "Invalid barangay selected.";
    } else {
        try {
            $stmt = $pdo->prepare("
                SELECT id, barangay, password, role
                FROM users 
                WHERE barangay = ? AND role = 'sk_chairperson' 
                LIMIT 1
            ");
            $stmt->execute([$barangay]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {

                // GET barangay_id FROM barangay TABLE (Anilao = 1)
                $stmt2 = $pdo->prepare("SELECT id FROM barangay WHERE name = ? LIMIT 1");
                $stmt2->execute([$barangay]);
                $bgy = $stmt2->fetch();

                $_SESSION['user_id']       = $user['id'];
                $_SESSION['barangay_id']   = $bgy['id'] ?? 1;           // Guaranteed: Anilao = 1
                $_SESSION['barangay_name'] = $barangay;                 // For display
                $_SESSION['role']          = $user['role'];

                session_regenerate_id(true);
                header("Location: user/dashboard.php");
                exit();
            } else {
                $error = $user 
                    ? "Incorrect password for Barangay $barangay." 
                    : "No SK Chairperson registered for Barangay $barangay. Please contact admin.";
            }
        } catch (PDOException $e) {
            error_log("Login Query Error: " . $e->getMessage());
            $error = "System error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SK Chairperson Login – Malolos City</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --bg-overlay: linear-gradient(135deg, rgba(52, 152, 219, 0.7), rgba(41, 128, 185, 0.78));
            --card-bg: rgba(255, 255, 255, 0.95);
            --text: #2c3e50;
            --text-light: #7f8c8d;
            --border: #dfe6e9;
            --cancel: #e74c3c;
            --cancel-hover: #c0392b;
            --link: #2980b9;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('../images/skfederation-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--bg-overlay);
            z-index: 1;
            backdrop-filter: blur(3px);
        }

        .login-card {
            background: var(--card-bg);
            padding: 3rem 2.5rem;
            border-radius: 26px;
            box-shadow: 0 32px 75px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 480px;
            text-align: center;
            position: relative;
            z-index: 2;
            border: 1px solid rgba(255,255,255,0.6);
            backdrop-filter: blur(22px);
        }

        .logo {
            width: 125px;
            height: 125px;
            margin: 0 auto 1.8rem;
            border-radius: 50%;
            overflow: hidden;
            border: 8px solid #fff;
            box-shadow: 0 14px 35px rgba(52,152,219,0.42);
            transition: all 0.4s ease;
        }
        .logo:hover { transform: scale(1.09) rotate(3deg); box-shadow: 0 20px 40px rgba(52,152,219,0.52); }
        .logo img { width: 100%; height: 100%; object-fit: contain; }

        h2 { margin-bottom: 1.8rem; color: var(--text); font-weight: 700; font-size: 1.95rem; letter-spacing: 0.7px; }

        .form-group { margin-bottom: 1.5rem; text-align: left; }
        label { display: block; margin-bottom: 0.7rem; color: var(--text); font-weight: 600; font-size: 1rem; }

        select, input {
            width: 100%;
            padding: 1.1rem 1.1rem 1.1rem 1.1rem;
            border: 2px solid var(--border);
            border-radius: 16px;
            font-size: 1rem;
            background: #fff;
            transition: all 0.35s ease;
        }
        select:focus, input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 5px rgba(52,152,219,0.25);
            transform: translateY(-2px);
        }

        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 55px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 16px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #95a5a6;
            transition: color 0.3s ease;
            z-index: 10;
        }
        .toggle-password:hover { color: var(--primary); }
        .toggle-password svg { width: 22px; height: 22px; }

        .button-group { display: flex; gap: 1.2rem; margin-top: 1.8rem; }
        button {
            flex: 1;
            padding: 1.15rem;
            border: none;
            border-radius: 16px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.45s ease;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }
        button[type="submit"] {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 10px 25px rgba(52,152,219,0.38);
        }
        button[type="submit"]:hover {
            background: linear-gradient(135deg, var(--primary-dark), #2573a6);
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(52,152,219,0.48);
        }
        button.cancel {
            background: var(--cancel);
            color: white;
            box-shadow: 0 10px 25px rgba(231,76,60,0.35);
        }
        button.cancel:hover {
            background: var(--cancel-hover);
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(231,76,60,0.45);
        }

        .error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            padding: 1.1rem;
            border-radius: 14px;
            font-size: 0.95rem;
            margin: 1.3rem 0;
            border: 1px solid #f5c6cb;
            font-weight: 500;
            animation: shake 0.5s ease;
        }
        @keyframes shake { 0%,100%{transform:translateX(0)} 25%{transform:translateX(-6px)} 75%{transform:translateX(6px)} }

        .signup-link {
            margin-top: 2rem;
            font-size: 0.96rem;
            color: var(--text-light);
        }
        .signup-link a { color: var(--link); text-decoration: none; font-weight: 600; }
        .signup-link a:hover { text-decoration: underline; color: var(--primary-dark); }

        @media (max-width: 480px) {
            .login-card { margin: 1rem; padding: 2.3rem; }
            .logo { width: 100px; height: 100px; }
            .button-group { flex-direction: column; }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo">
        <img src="../images/sk-logo.png" alt="SK Federation Logo">
    </div>

    <h2>SK Chairperson Sign In</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="barangay">Select Your Barangay</label>
            <select id="barangay" name="barangay" required>
                <option value="">-- Choose Barangay --</option>
                <?php foreach ($barangays as $brgy): ?>
                    <option value="<?= htmlspecialchars($brgy) ?>" 
                        <?= (isset($_POST['barangay']) && $_POST['barangay'] === $brgy) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($brgy) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <div class="toggle-password" onclick="togglePassword()">
                    <svg class="eye-icon" viewBox="0 0 576 512" fill="currentColor">
                        <path d="M572.52 241.4C518.29 135.59 410.73 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.27 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"/>
                    </svg>
                    <svg class="eye-slash-icon" viewBox="0 0 640 512" fill="currentColor" style="display:none;">
                        <path d="M320 400c-75.85 0-137.25-58.71-142.9-133.11L72.2 185.82c-13.79 17.3-26.48 35.59-36.72 55.59a32.35 32.35 0 0 0 0 29.19C89.71 376.41 197.27 448 320 448c26.91 0 52.87-4 77.89-10.46L346 397.39a144 144 0 0 1-26 2.61zm313.82 58.1l-110.55-85.44a331.25 331.25 0 0 0 81.25-102.07 32.35 32.35 0 0 0 0-29.19C550.29 135.59 442.93 64 320 64a308.15 308.15 0 0 0-147.32 37.7L45.46 3.37A16 16 0 0 0 23 6.18L3.37 31.45A16 16 0 0 0 6.18 53.9l588.36 454.73a16 16 0 0 0 22.46-2.81l19.64-25.27a16 16 0 0 0-2.82-22.45zM203.55 240a95.92 95.92 0 0 0 140.5 83.4l-138.15-106.73A95.92 95.92 0 0 0 203.55 240z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="button-group">
            <button type="submit">Sign In</button>
            <button type="button" class="cancel" onclick="window.location.href='../index.php'">Cancel</button>
        </div>
    </form>

    <div class="signup-link">
        Don't have an account? <a href="signup.php">Sign up here</a>
    </div>
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