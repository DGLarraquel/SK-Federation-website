<?php
// auth/login_process.php
session_start();
header('Content-Type: application/json');
include('../connection.php');

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'admin';

// === DEMO USERS (Replace with DB query later) ===
$valid = [
  'admin' => ['admin' => 'admin123'],
  'chairperson' => ['chair1' => 'chair123', 'chair2' => 'chair456']
];

$response = ['success' => false, 'message' => 'Invalid credentials'];

if (isset($valid[$role][$username]) && $valid[$role][$username] === $password) {
  $_SESSION[$role] = true;
  $_SESSION['role'] = $role;
  $_SESSION['username'] = $username;

  $redirect = $role === 'admin' ? '../admin/dashboard.php' : '../chair/dashboard.php';

  $response = [
    'success' => true,
    'redirect' => $redirect
  ];
} else {
  // Store error to show in modal
  $session_key = $role === 'admin' ? 'admin_error' : 'chair_error';
  $_SESSION[$session_key] = "Invalid username or password.";
}

echo json_encode($response);
exit;