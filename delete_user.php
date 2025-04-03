<?php
require 'db_connect.php';
session_start();

// Get the ID of the user to delete
$user_id_to_delete = $_GET['id'] ?? null;

// Check if an admin is logged in
$current_user_id = $_SESSION['user_id'] ?? null;

// Ensure only admins can delete users
$stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    header("Location: login.php"); // Redirect non-admins
    exit();
}

if ($user_id_to_delete) {
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id_to_delete);

    if ($stmt->execute()) {
        header("Location: admin.php?message=User+deleted+successfully");
    } else {
        echo "Error deleting user: " . $stmt->error;
    }
    $stmt->close();
}
?>
