<?php
session_start();
session_unset();
session_destroy();

// Go to root first, then to the auth page
header("Location: /auth/auth_sk_chair.php");
exit;
?>