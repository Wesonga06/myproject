<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connect.php'; // Include your database connection script
session_start();

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Check if the user is logged in
if (!$user_id) {
    header("Location: login.php"); // Redirect to login if the user is not logged in
    exit();
}

// Initialize variables
$items = [];
$message = "";

// Handle form submission to add a new item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;
    $price = $_POST['price'] ?? 0.00;

    if (!empty($name) && $quantity > 0 && $price > 0) {
        // Insert inventory item for the logged-in user
        $stmt = $db->prepare("INSERT INTO inventory (user_id, name, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isid", $user_id, $name, $quantity, $price);

        if ($stmt->execute()) {
            $message = "Item added successfully!";
        } else {
            $message = "Failed to add item: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Please fill in all fields correctly.";
    }
}

// Fetch all inventory items for the logged-in user
$result = $db->prepare("SELECT * FROM inventory WHERE user_id = ? ORDER BY id DESC");
$result->bind_param("i", $user_id);
$result->execute();
$queryResult = $result->get_result();

if ($queryResult->num_rows > 0) {
    while ($row = $queryResult->fetch_assoc()) {
        $items[] = $row;
    }
}
$result->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory - Money Tracker</title>
  <link rel="icon" href="MART.png" type="image">
  <style>
  /* General Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    margin: 0;
    padding: 0;
}

/* Header */
header {
    background-color: #2c3e50;
    color: white;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
}

/* Navigation Bar Styling */
nav {
    background-color: #2c3e50; /* Dark blue background for the navbar */
    padding: 15px 0; /* Vertical padding for spacing */
}

nav ul {
    list-style: none; /* Remove default bullets */
    margin: 0;
    padding: 0;
    display: flex; /* Align items horizontally */
    justify-content: center; /* Center the navigation links */
    gap: 25px; /* Add even spacing between links */
}

nav ul li {
    display: inline; /* Ensure links are inline */
}

nav ul li a {
    text-decoration: none; /* Remove underline from links */ 
    font-weight: bold; /* Bold text for emphasis */
    padding: 10px 20px; /* Add padding inside the links */
    border-radius: 5px; /* Slightly rounded corners for links */
    transition: background-color 0.3s ease; /* Smooth hover effect */
}

nav ul li a:hover {
    background-color: #34495e; /* Slightly lighter background on hover */
    border-radius: 5px;
}


/* Footer */
footer {
    text-align: center;
    background-color: #2c3e50;
    color: white;
    padding: 15px;
    margin-top: 20px;
    box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
}

/* Form */
form {
    max-width: 500px;
    margin: auto;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

form label {
    font-weight: bold;
    display: block;
    margin-top: 10px;
}

form input, form button {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

form button {
    background-color: #4CAF50;
    color: white;
    font-size: 16px;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease;
}

form button:hover {
    background-color: #45a049;
}

/* List Styling */
ul {
    list-style: none;
    padding: 0;
}

ul li {
    background: #fff;
    margin-bottom: 10px;
    padding: 15px;
    border-radius: 8px; /* Increased border-radius for smoother look */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Improved shadow */
}

/* Responsive Design */
@media (max-width: 768px) {
    nav ul {
        flex-direction: column; /* Stack links on smaller screens */
        gap: 10px; /* Adjust spacing for compact view */
    }
}
</style>
</head>
<body>
<header>
    <h1 style=" background-color: #2c3e50; padding: 15px;">Inventory</h1>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="expenses.php">Expenses</a></li>
            <li><a href="budget.php">Budget</a></li>
            <li><a href="report.php">Reports</a></li>
            <li><a href="profile.php">Account</a></li>
        </ul>
    </nav>
</header>

  <main>
    <!-- Display any feedback message -->
    <?php if (!empty($message)): ?>
      <p style="text-align: center; color: green;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Inventory Form -->
    <form method="POST" action="">
      <label for="itemName">Item Name:</label>
      <input type="text" id="itemName" name="name" placeholder="Item Name" required>

      <label for="itemQuantity">Item Quantity:</label>
      <input type="number" id="itemQuantity" name="quantity" placeholder="Quantity" required>

      <label for="itemPrice">Price:</label>
      <input type="number" id="itemPrice" name="price" placeholder="Price" required>

      <button type="submit">Add Item</button>
    </form>

    <!-- Inventory List -->
    <h2 style="text-align: center;">Inventory List</h2>
    <ul>
      <?php if (!empty($items)): ?>
        <?php foreach ($items as $item): ?>
          <li>
            <strong><?= htmlspecialchars($item['name']); ?></strong> - 
            <?= htmlspecialchars($item['quantity']); ?> pcs - 
            $<?= htmlspecialchars(number_format($item['price'], 2)); ?>
          </li>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align: center;">No items in inventory yet.</p>
      <?php endif; ?>
    </ul>
  </main>

  <footer>
    <p>&copy; 2025 Money Tracker</p>
  </footer>
</body>
</html>
