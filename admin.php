<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connect.php'; // Include your database connection script
session_start();

// Check if the logged-in user is an admin
$user_id = $_SESSION['user_id'] ?? null;
$stmt = $db->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    header("Location: login.php"); // Redirect non-admins
    exit();
}

// Fetch all users
$users = [];
$result = $db->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Money Tracker</title>
  <link rel="icon" href="MART.png" type="image/png">
  <style>
    /* General Styles */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }
    header {
      background-color: #2c3e50;
      color: white;
      padding: 15px;
      text-align: center;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px auto;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    table th, table td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: left;
    }
    table th {
      background-color: #2c3e50;
      color: white;
    }
    .action-buttons button {
      margin: 5px;
      padding: 5px 10px;
      background-color: #3498db;
      color: white;
      border: none;
      cursor: pointer;
    }
    .action-buttons button:hover {
      background-color: #2980b9;
    }
  </style>
</head>
<body>
<header>
  <h1>Admin Panel</h1>
</header>
<main>
  <h2>Registered Users</h2>
  <table>
    <thead>
      <tr>
        <th>User ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Registered On</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><?= htmlspecialchars($user['id']) ?></td>
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td><?= htmlspecialchars($user['role']) ?></td>
          <td><?= htmlspecialchars($user['created_at']) ?></td>
          <td class="action-buttons">
            <button onclick="editUser(<?= $user['id'] ?>)">Edit</button>
            <button onclick="deleteUser(<?= $user['id'] ?>)">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>
<script>
  // Function to handle edit user
  function editUser(userId) {
    alert('Edit functionality for user ' + userId + ' coming soon.');
    // Redirect to an edit page or open a modal
  }

  // Function to handle delete user
  function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
      window.location.href = 'delete_user.php?id=' + userId; // Redirect to delete_user.php
    }
  }
</script>
</body>
</html>
