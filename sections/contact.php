<?php
// Enable error reporting during development (remove or comment out on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once dirname(__DIR__) . '/connection.php';  // Try this path first

// Fetch contact info
$stmt = $pdo->query("SELECT address, phone, email FROM site_contact LIMIT 1");
$contact = $stmt->fetch(PDO::FETCH_ASSOC);

// Fallback values if no record found
if (!$contact) {
    $contact = [
        'address' => 'Mada Center 8th Floor<br>379 Hudson St<br>Malolos City, Bulacan',
        'phone'   => '+63 912 345 6789',
        'email'   => 'admin@skfederation-of-maloloscity.com'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact – SK Federation Malolos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --navy: #16213e;
            --navy-light: #1e2a4e;
            --navy-dark: #0f1a32;
            --accent: #1e40af;
            --success: #172554;
        }

        .contact-section { 
            background: linear-gradient(135deg, #f0f4f8, #d9e2ec); 
            padding: 120px 0; 
            font-family: 'Segoe UI', sans-serif;
        }
        .section-title span { 
            background: linear-gradient(90deg, var(--navy), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-size: 3.2rem;
            font-weight: 900;
        }
        .card-shadow { 
            box-shadow: 0 25px 80px rgba(22, 33, 62, 0.3); 
            border-radius: 28px; 
            overflow: hidden;
            border: 1px solid rgba(22, 33, 62, 0.1);
        }
        .form-control { 
            border-radius: 14px; 
            padding: 16px; 
            border: 2px solid #cfd8dc; 
            transition: all 0.3s;
        }
        .form-control:focus { 
            border-color: var(--navy); 
            box-shadow: 0 0 0 0.35rem rgba(22, 33, 62, 0.25); 
        }
        .btn-success { 
            background: var(--navy); 
            border: none; 
            border-radius: 14px; 
            padding: 16px 60px; 
            font-size: 1.3em; 
            font-weight: bold;
            transition: all 0.4s;
            box-shadow: 0 8px 20px rgba(22, 33, 62, 0.4);
        }
        .btn-success:hover {
            background: var(--accent);
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(30, 64, 175, 0.5);
        }
        .alert { 
            border-radius: 20px; 
            padding: 35px; 
            font-size: 1.3em; 
            box-shadow: 0 15px 40px rgba(22, 33, 62, 0.2); 
            text-align: center; 
            display: none;
            border: none;
        }
        .success-alert { 
            background: linear-gradient(135deg, var(--success), var(--navy-dark));
            color: #ffffff !important;
            border: 2px solid var(--accent);
        }
        .success-alert i { color: #60a5fa !important; }
        .success-alert strong { color: #93c5fd; }

        .left-info {
            position: relative;
            z-index: 2;
            color: #ffffff !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
            padding: 50px;
        }
        .left-info h3 { 
            color: #ffffff !important; 
            font-weight: 800; 
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }
        .left-info p, .left-info i { 
            color: #e0e7ff !important; 
            font-size: 1.2rem;
        }
        .left-info i { 
            color: #60a5fa !important; 
            font-size: 2.5rem !important;
            margin-bottom: 1rem;
        }
        .overlay { 
            position: absolute; 
            inset: 0; 
            background: linear-gradient(135deg, rgba(22, 33, 62, 0.98), rgba(15, 26, 50, 0.95)); 
        }
    </style>
</head>
<body>

<section class="contact-section" data-aos="fade-up">
    <div class="container">
        <h2 class="section-title text-center mb-5"><span>Get In Touch</span></h2>

        <div id="successAlert" class="alert success-alert">
            <i class="bi bi-check-circle-fill fs-1"></i><br><br>
            <strong>Salamat po! Your message was sent successfully! 📧✅</strong><br><br>
            <p>We received it and will reply within 24 hours.<br>
            <small>Maraming salamat sa inyong suporta!<br>– SK Federation of Malolos City 💙</small></p>
        </div>

        <div class="card-shadow">
            <table style="width:100%; border-collapse:collapse;">
                <tr>
                    <td style="width:50%; background:#16213e; position:relative; background: url('../images/skfederation-bg.jpg') center/cover no-repeat;">
                        <div class="overlay"></div>
                        <div class="left-info">
                            <div>
                                <i class="bi bi-building-fill"></i>
                                <h3>Visit Us</h3>
                                <p><?= nl2br(htmlspecialchars($contact['address'])) ?></p>
                            </div>

                            <div class="mt-5">
                                <i class="bi bi-telephone-fill"></i>
                                <h3>Call Us</h3>
                                <p><?= htmlspecialchars($contact['phone']) ?></p>
                            </div>

                            <div class="mt-5">
                                <i class="bi bi-envelope-fill"></i>
                                <h3>Email Us</h3>
                                <p><?= htmlspecialchars($contact['email']) ?></p>
                            </div>
                        </div>
                    </td>

                    <td style="width:50%; background:#ffffff; padding:80px;">
                        <form id="contactForm" action="https://formsubmit.co/admin@skfederation-of-maloloscity.com" method="POST">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <input type="text" name="first_name" class="form-control" placeholder="First Name *" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="last_name" class="form-control" placeholder="Last Name *" required>
                                </div>
                                <div class="col-12">
                                    <input type="email" name="email" class="form-control" placeholder="Your Email *" required>
                                </div>
                                <div class="col-12">
                                    <input type="text" name="phone" class="form-control" placeholder="Phone (optional)">
                                </div>
                                <div class="col-12">
                                    <textarea name="message" class="form-control" rows="6" placeholder="Your Message *" required></textarea>
                                </div>

                                <input type="hidden" name="_next" value="javascript:void(0)">
                                <input type="hidden" name="_autoresponse" value="Salamat po! We received your message and will reply soon. – SK Federation of Malolos City 💙">
                                <input type="hidden" name="_subject" value="NEW MESSAGE from SK Website!">
                                <input type="hidden" name="_template" value="table">

                                <div class="col-12 text-center mt-5">
                                    <button type="submit" class="btn btn-success shadow-lg">
                                        <i class="bi bi-send-fill me-3"></i> Send Message Now
                                    </button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</section>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split me-3"></i> Sending...';

    fetch(this.action, {
        method: 'POST',
        body: new FormData(this)
    }).then(() => {
        document.getElementById('successAlert').style.display = 'block';
        this.reset();
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-3"></i> Send Message Now';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }).catch(() => {
        alert('Failed to send. Please try again or email us directly.');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill me-3"></i> Send Message Now';
    });
});
</script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ duration: 1200, once: true });</script>
</body>
</html>