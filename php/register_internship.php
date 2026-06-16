<?php
require_once '../includes/db.php';
require_once '../includes/session.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $internship_id = intval($_POST['internship_id']);

    // Check if already registered
    $check = $pdo->prepare("SELECT id FROM internship_registrations WHERE user_id=? AND internship_id=?");
    $check->execute([$user_id, $internship_id]);

    if ($check->fetch()) {
        $_SESSION['flash'] = "You have already registered for this internship.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO internship_registrations (user_id, internship_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $internship_id]);
        $_SESSION['flash'] = "Registration successful! Await admin approval.";
    }

    header("Location: browse_internships.php");
    exit();
}
?>