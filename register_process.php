<?php
session_start();
include('connection.php'); // your DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $surname = trim($_POST['surname']);
    $birthdate = $_POST['birthdate'];
    $barangay = $_POST['barangay'];
    $email = trim($_POST['email']);
    $confirm_email = trim($_POST['confirm_email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($firstname) || empty($surname) || empty($birthdate) || empty($barangay) || empty($email) || empty($confirm_email) || empty($password) || empty($confirm_password)) {
        die('Please fill all required fields.');
    }

    if ($email !== $confirm_email) {
        die('Email and Confirm Email do not match.');
    }

    if ($password !== $confirm_password) {
        die('Password and Confirm Password do not match.');
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        die('Email is already registered.');
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (firstname, middlename, surname, birthdate, barangay, email, password) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $firstname, $middlename, $surname, $birthdate, $barangay, $email, $password_hash);

    if ($stmt->execute()) {
        // Redirect or success message
        header("Location: signin.php?register=success");
        exit();
    } else {
        die('Error during registration. Please try again.');
    }
} else {
    header("Location: signup.php");
    exit();
}
