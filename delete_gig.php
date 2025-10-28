<?php
// File: delete_gig.php
// Delete gig
// Usage: delete_gig.php?id=1
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
if (isset($_GET['id'])) {
    $gig_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];
 
    $stmt = $pdo->prepare("DELETE FROM gigs WHERE id = ? AND user_id = ?");
    $stmt->execute([$gig_id, $user_id]);
    echo "<script>window.location.href = 'manage_gigs.php';</script>";
}
?>
