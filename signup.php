<?php
session_start();
include('connection.php');

if (!isset($pdo) || !$pdo instanceof PDO) die("Database connection failed.");

$barangays = ["Anilao","Atlag","Babatnin","Bagna","Bagong Bayan","Balayong","Balite","Bangkal","Barihan","Bulihan","Bungahan","Caingin","Calero","Caliligawan","Canalate","Caniogan","Catmon","Cofradia","Dakila","Guinhawa","Ligas","Liang","Longos","Look 1st","Look 2nd","Lugam","Mabolo","Mambog","Masile","Matimbo","Mojon","Namayan","Niugan","Pamarawan","Panasahan","Pinagbakahan","San Agustin","San Gabriel","San Juan","San Pablo","San Vicente","Santiago","Santor","Santisima Trinidad","Sto. Cristo","Sto. Niño","Santo Rosario","Sumapang Bata","Sumapang Matanda","Taal","Tikay"];
sort($barangays);

$error = $success = '';
$step = 1;
$show_success_redirect = false;

function alert($msg, $type='error'){
    $c = $type==='success' ? 'alert-success' : 'alert-error';
    return "<div class='alert $c'>$msg</div>";
}

/* RESEND OTP */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['resend'])){
    $email = $_SESSION['pending_email'] ?? '';
    if(!$email){ $error="Session expired."; $step=1; }
    else{
        $otp = sprintf("%06d", mt_rand(0,999999));
        $_SESSION['otp_code'] = $otp;
        $_SESSION['otp_expiry'] = time() + 600;
        $_SESSION['last_resend'] = time();
        $subject = "Your SK Federation OTP (Resent)";
        $message = "<h2>New OTP Code</h2><p style='font-size:2em;color:#3498db;font-weight:bold;'>$otp</p><p>Valid for 10 minutes.</p>";
        $headers = "From: no-reply@skfederation-of-maloloscity.com\r\nContent-Type: text/html; charset=UTF-8\r\n";
        mail($email,$subject,$message,$headers) ? $success="New OTP sent!" : $error="Failed to send OTP.";
    }
    $step = 2;
}

/* OTP VERIFICATION */
elseif(isset($_POST['verify_otp'])){
    $otp_input = trim($_POST['otp_combined'] ?? '');
    $stored    = $_SESSION['otp_code'] ?? '';
    $expiry    = $_SESSION['otp_expiry'] ?? 0;

    if($otp_input !== '' && $otp_input === $stored && time() < $expiry){
        $hashed = password_hash($_SESSION['pending_password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (firstname,middlename,surname,birthdate,barangay,email,password,role)
                VALUES (:fn,:mn,:sn,:bd,:brgy,:email,:pass,'sk_chairperson')";
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute([
            ':fn'    => $_SESSION['pending']['firstname'],
            ':mn'    => $_SESSION['pending']['middlename'],
            ':sn'    => $_SESSION['pending']['surname'],
            ':bd'    => $_SESSION['pending']['birthdate'],
            ':brgy'  => $_SESSION['pending']['barangay'],
            ':email' => $_SESSION['pending_email'],
            ':pass'  => $hashed
        ]);

        if($ok){
            $show_success_redirect = true;
            session_unset();
            session_destroy();
            session_start();
        } else {
            $error = "Failed to create account.";
        }
    } else {
        $error = "Invalid or expired OTP.";
    }
    $step = 2;
}

/* MAIN REGISTRATION */
elseif($_SERVER['REQUEST_METHOD']==='POST'){
    $firstname = trim($_POST['firstname']??'');
    $middlename = trim($_POST['middlename']??'');
    $surname = trim($_POST['surname']??'');
    $birthdate = $_POST['birthdate']??'';
    $barangay = $_POST['barangay']??'';
    $email = trim($_POST['email']??'');
    $confirm_email = trim($_POST['confirm_email']??'');
    $password = $_POST['password']??'';
    $confirm_password = $_POST['confirm_password']??'';

    $errors = [];
    if(strlen($password)<8) $errors[]="8+ characters";
    if(!preg_match("/[A-Z]/",$password)) $errors[]="1 uppercase";
    if(!preg_match("/[a-z]/",$password)) $errors[]="1 lowercase";
    if(!preg_match("/\d/",$password)) $errors[]="1 number";
    if(!preg_match("/[!@#$%^&*]/",$password)) $errors[]="1 symbol";

    if(!empty($errors)) $error="Password must include: <strong>".implode(', ',$errors)."</strong>.";
    elseif($email!==$confirm_email) $error="Emails do not match.";
    elseif($password!==$confirm_password) $error="Passwords do not match.";
    elseif(!filter_var($email,FILTER_VALIDATE_EMAIL)) $error="Invalid email.";
    elseif(!in_array($barangay,$barangays)) $error="Invalid barangay.";
    else{
        $stmt=$pdo->prepare("SELECT id FROM users WHERE barangay=? AND role='sk_chairperson'");
        $stmt->execute([$barangay]);
        if($stmt->rowCount()>0) $error="Barangay already registered.";
        else{
            $stmt=$pdo->prepare("SELECT id FROM users WHERE email=?");
            $stmt->execute([$email]);
            if($stmt->rowCount()>0) $error="Email already registered.";
            else{
                $otp=sprintf("%06d",mt_rand(0,999999));
                $_SESSION['otp_code']=$otp;
                $_SESSION['otp_expiry']=time()+600;
                $_SESSION['pending_email']=$email;
                $_SESSION['pending_password']=$password;
                $_SESSION['pending']=compact('firstname','middlename','surname','birthdate','barangay');
                $_SESSION['last_resend']=time();

                $subject="Your SK Federation OTP";
                $message="<h2>Verify Your Email</h2><p style='font-size:2em;color:#27ae60;font-weight:bold;'>$otp</p><p>Valid for 10 minutes.</p>";
                $headers="From: no-reply@skfederation-of-maloloscity.com\r\nContent-Type: text/html; charset=UTF-8\r\n";

                if(mail($email,$subject,$message,$headers)){
                    $success="OTP sent to <strong>$email</strong>!";
                    $step=2;
                }else $error="Failed to send OTP.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | SK Federation</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root{--navy:#1a2a6c;--blue:#3498db;--red:#e74c3c;--green:#27ae60;--gray:#95a5a6;}
    *{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Inter',sans-serif;background:url('images/skfederation-bg.jpg') center/cover fixed;min-height:100vh;display:flex;align-items:center;justify-content:center;position:relative;}
    body::before{content:'';position:absolute;inset:0;background:rgba(26,42,108,0.75);z-index:1;}
    .container{position:relative;z-index:2;background:white;max-width:800px;width:100%;border-radius:20px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,0.3);}
    .header{background:var(--navy);color:white;padding:2rem;text-align:center;}
    .header img{width:90px;height:90px;border-radius:50%;border:4px solid rgba(255,255,255,0.3);margin-bottom:1rem;}
    .content{padding:2.5rem;}
    .form-grid{display:grid;grid-template-columns:1fr 1fr;gap:1.2rem;}
    .form-row.full{grid-column:1/-1;}
    .form-row{position:relative;}
    input,select{width:100%;padding:0.9rem 40px 0.9rem 1rem;border:2px solid #ddd;border-radius:12px;font-size:1rem;transition:.3s;}
    input:focus,select:focus{outline:none;border-color:var(--blue);box-shadow:0 0 0 4px rgba(52,152,219,.15);}
    .toggle-eye{position:absolute;right:12px;top:50%;transform:translateY(-50%);cursor:pointer;color:#777;font-size:1.3rem;z-index:10;}
    .toggle-eye:hover{color:var(--blue);}
    .btn{width:100%;padding:1rem;background:var(--blue);color:white;border:none;border-radius:12px;font-weight:600;cursor:pointer;margin-top:1rem;font-size:1.1rem;transition:.3s;}
    .btn:hover{background:#2980b9;transform:translateY(-3px);}
    .btn-resend{background:var(--gray);margin-top:.5rem;}
    .alert{padding:1rem;border-radius:10px;margin-bottom:1.5rem;text-align:center;}
    .alert-error{background:#fdf2f2;color:var(--red);border:1px solid #f5c6cb;}
    .alert-success{background:#f0f9f0;color:var(--green);border:1px solid #d4edda;}
    .otp-container{display:flex;flex-direction:column;align-items:center;gap:1.8rem;margin:2rem 0;}
    .otp-input{display:flex;gap:10px;justify-content:center;}
    .otp-input input {
        width: 62px;
        height: 62px;
        text-align: center;
        font-size: 2.4rem;
        font-weight: bold;
        color: #000000 !important;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        caret-color: transparent;
        -webkit-text-fill-color: #000000;
        transition: background 0.15s, border-color 0.2s;
    }
    .otp-input input:focus {
        border-color: #3b82f6;
        background: #f0f9ff;
    }
    .otp-input input:not(:placeholder-shown) {
        background: #ecfdf5;
        border-color: #10b981;
    }
    .success-box{
      text-align:center;padding:3rem 2rem;background:#f8fff8;border:3px solid #d4edda;border-radius:20px;margin:2rem 0;
    }
    .success-box i{font-size:4rem;color:#27ae60;margin-bottom:1rem;display:block;}
    .success-box h2{color:#27ae60;font-size:2rem;margin:1rem 0;}
    .countdown{color:#27ae60;font-weight:bold;font-size:1.3rem;}
    @media(max-width:768px){
      .form-grid{grid-template-columns:1fr;}
      .otp-input input{width:54px;height:54px;font-size:2.1rem;}
      .otp-input input:not(:placeholder-shown) { font-size: 2.2rem; }
    }
  </style>
</head>
<body>
<div class="container">
  <div class="header">
    <img src="images/sk-logo.png" alt="SK Logo">
    <h2>SK Federation Malolos</h2>
  </div>
  <div class="content">

    <?php if($error): ?>
      <?=alert($error,'error')?>
    <?php endif; ?>

    <?php if($show_success_redirect): ?>
      <div class="success-box">
        <i class="bi bi-check-circle-fill"></i>
        <h2>Account Created Successfully!</h2>
        <p>Your SK Chairperson account is ready.</p>
        <p class="countdown">Redirecting to login in <strong>3</strong> seconds...</p>
      </div>

      <script>
        let seconds = 3;
        const countdown = document.querySelector('.countdown strong');
        const timer = setInterval(() => {
          seconds--;
          countdown.textContent = seconds;
          if(seconds <= 0){
            clearInterval(timer);
            window.location.href = "https://skfederation-of-maloloscity.com/auth/auth_sk_chair.php";
          }
        }, 1000);
      </script>

    <?php elseif($step == 1): ?>
      <form action="signup.php" method="POST">
        <div class="form-grid">
          <div class="form-row"><input type="text" name="firstname" placeholder="First Name" value="<?=htmlspecialchars($_POST['firstname']??'')?>" required></div>
          <div class="form-row"><input type="text" name="middlename" placeholder="Middle Name (Optional)" value="<?=htmlspecialchars($_POST['middlename']??'')?>"></div>
          <div class="form-row full"><input type="text" name="surname" placeholder="Surname" value="<?=htmlspecialchars($_POST['surname']??'')?>" required></div>
          <div class="form-row"><input type="date" name="birthdate" value="<?=$_POST['birthdate']??''?>" required></div>
          <div class="form-row">
            <select name="barangay" required>
              <option value="">Select Barangay</option>
              <?php foreach($barangays as $b): ?>
                <option value="<?=htmlspecialchars($b)?>" <?=(($_POST['barangay']??'')===$b?'selected':'')?>><?=htmlspecialchars($b)?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-row full"><input type="email" name="email" placeholder="Email" value="<?=htmlspecialchars($_POST['email']??'')?>" required></div>
          <div class="form-row full"><input type="email" name="confirm_email" placeholder="Confirm Email" required></div>
          <div class="form-row full">
            <input type="password" name="password" id="password" placeholder="Password" required>
            <span class="toggle-eye" onclick="togglePass(this,'password')"><i class="bi bi-eye"></i></span>
          </div>
          <div class="form-row full">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
            <span class="toggle-eye" onclick="togglePass(this,'confirm_password')"><i class="bi bi-eye"></i></span>
          </div>
        </div>
        <button type="submit" class="btn">Send OTP</button>
      </form>

    <?php else: ?>
      <?php if($success): ?>
        <?=alert($success,'success')?>
      <?php endif; ?>

      <form action="signup.php" method="POST" id="otpForm">
        <div class="otp-container">
          <p style="color:var(--gray);text-align:center;font-size:1.1rem;">
            Enter the 6-digit code sent to<br>
            <strong><?=htmlspecialchars($_SESSION['pending_email']??'')?></strong>
          </p>
          <div class="otp-input">
            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off">
            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off">
            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off">
            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off">
            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off">
            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off">
          </div>
          <input type="hidden" name="otp_combined" id="otpCombined">
          <input type="hidden" name="verify_otp" value="1">
        </div>
        <button type="submit" class="btn">Verify OTP</button>
        <button type="submit" formnovalidate class="btn btn-resend" name="resend" value="1">
          Resend OTP <?php $w=60-(time()-($_SESSION['last_resend']??0)); echo $w>0?"($w s)":''; ?>
        </button>
      </form>
    <?php endif; ?>

    <div style="text-align:center;margin-top:1.5rem;">
      <a href="index.php" style="color:var(--blue);text-decoration:none;font-weight:600;">Back to Home</a>
    </div>
  </div>
</div>

<script>
  function togglePass(icon,id){
    const input=document.getElementById(id);
    input.type = input.type==='password' ? 'text' : 'password';
    icon.innerHTML = input.type==='password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
  }

  const inputs = document.querySelectorAll('.otp-input input');
  inputs.forEach((el, i) => {
    el.addEventListener('input', function() {
      this.value = this.value.replace(/[^0-9]/g, '');
      if (this.value && i < 5) inputs[i+1].focus();
    });
    el.addEventListener('keydown', e => {
      if (e.key === 'Backspace' && !el.value && i > 0) inputs[i-1].focus();
    });
    el.addEventListener('paste', e => {
      e.preventDefault();
      let paste = (e.clipboardData.getData('text')).replace(/\D/g,'').substring(0,6);
      paste.split('').forEach((char, j) => {
        if (inputs[i+j]) inputs[i+j].value = char;
      });
      let next = inputs[Math.min(i + paste.length - 1, 5)];
      if (next) next.focus();
    });
  });

  document.getElementById('otpForm')?.addEventListener('submit', () => {
    document.getElementById('otpCombined').value = Array.from(inputs).map(i => i.value || '').join('');
  });
</script>
</body>
</html>