<?php
require 'db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errorMessage = "";
$successMessage = "";

// Fetch user details securely
$stmt = $db->prepare("SELECT user_name, email, dob FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    $errorMessage = "Failed to fetch user details.";
}

// Handle form submission to update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = htmlspecialchars(trim($_POST['user_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $dob = htmlspecialchars(trim($_POST['dob']));

    $stmt = $db->prepare("UPDATE users SET user_name = ?, email = ?, dob = ? WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("sssi", $user_name, $email, $dob, $user_id);

        if ($stmt->execute()) {
            $successMessage = "Profile updated successfully!";
            $user['user_name'] = $user_name;
            $user['email'] = $email;
            $user['dob'] = $dob;
        } else {
            $errorMessage = "Failed to update profile: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "Error preparing the SQL statement.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
    /* Header */
header {
    position: fixed; /* Ensures the header stays at the top */
    top: 0; /* Aligns it to the very top of the page */
    width: 100%; /* Spans the full width of the page */
    background-color: #2c3e50; /* Dark background for contrast */
    color: white; /* White text for readability */
    padding: 15px 0; /* Adjust padding for spacing */
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Adds shadow for depth */
    z-index: 1000; /* Ensures it stays above other elements */
}

/* Navigation Menu */
nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: center;
    gap: 20px;
}

nav ul li a {
    text-decoration: none;
    color: white;
    font-weight: bold;
    padding: 10px 15px;
    transition: background-color 0.3s ease;
}

nav ul li a:hover {
    background-color: #34495e;
    border-radius: 5px;
}

/* Main Content */
main {
    margin-top: 100px; /* Prevents overlap with the fixed header */
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

        form, .profile-info {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
<header>
   <img src="MART.png" alt="Money Accounting & Resource Tracking" width="100">
        <nav>
            <ul>
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="inventory.php">Inventory</a></li>
              <li><a href="expenses.php">Expenses</a></li>
              <li><a href="budget.php">Budget</a></li>
              <li><a href="report.php">Report</a></li>
              <li><a href="profile.php">Profile</a></li>
            </ul>
        </nav>
</header>
<main>
    <form method="POST" action="">
        <h2>My Profile</h2>
        <!-- Display success or error messages -->
        <?php if (!empty($successMessage)): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php elseif (!empty($errorMessage)): ?>
            <p class="error"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <label for="user_name">Username:</label>
        <input type="text" id="user_name" name="user_name" value="<?= htmlspecialchars($user['user_name'] ?? '') ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>">

        <button type="submit">Update Profile</button>
    </form>
</main>   
</body>
</html>

