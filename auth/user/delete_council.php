<?php
session_start();
require_once '../../connection.php';

if (!isset($_GET['id']) || !isset($_SESSION['barangay_id'])) {
    header("Location: officers.php");
    exit;
}

$id = (int)$_GET['id'];
$barangay_id = $_SESSION['barangay_id'];

try {
    // Get image filename first
    $stmt = $pdo->prepare("SELECT image FROM sk_council_members WHERE id = ? AND barangay_id = ?");
    $stmt->execute([$id, $barangay_id]);
    $member = $stmt->fetch();

    if ($member) {
        // Delete record
        $pdo->prepare("DELETE FROM sk_council_members WHERE id = ? AND barangay_id = ?")
            ->execute([$id, $barangay_id]);

        // Delete image file
        if ($member['image'] && file_exists("../../images/officers/" . $member['image'])) {
            @unlink("../../images/officers/" . $member['image']);
        }

        $_SESSION['msg'] = "Council member deleted successfully.";
    } else {
        $_SESSION['msg'] = "Member not found or access denied.";
    }
} catch (Exception $e) {
    $_SESSION['msg'] = "Error deleting member.";
}

header("Location: officers.php");
exit;