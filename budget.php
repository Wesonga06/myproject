<?php
// Enable error reporting for debugging purposes
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
$message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST["amount"];
    $startDate = $_POST["start-date"];
    $endDate = $_POST["end-date"];
    $description = $_POST["description"] ?? '';

    // Validate inputs
    if (!empty($amount) && !empty($startDate) && !empty($endDate)) {
        // Insert the budget into the database, including user_id
        $sql = "INSERT INTO budget (user_id, amount, start_date, end_date, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("idsss", $user_id, $amount, $startDate, $endDate, $description);

            if ($stmt->execute()) {
                $message = "Budget created successfully!";
            } else {
                $message = "Failed to create budget: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "Failed to prepare the SQL statement.";
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Fetch all budgets for the logged-in user
$budgets = [];
$sql = "SELECT * FROM budget WHERE user_id = ? ORDER BY start_date DESC";
$stmt = $db->prepare($sql);

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $budgets[] = $row;
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Budget - Money Tracker</title>
  <link rel="icon" href="MART.png" type="image/png">
  <style>
    /* General Styles */
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 20px;
    }

    /* Header */
    header {
      background-color: #2c3e50;
      color: white;
      padding: 15px;
      text-align: center;
    }

    nav ul {
      list-style: none;
      padding: 0;
      display: flex;
      justify-content: center;
      gap: 20px;
    }

    nav ul li a {
      text-decoration: none;
      color: white;
      font-weight: bold;
      padding: 10px 15px;
      transition: 0.3s;
    }

    nav ul li a:hover {
      background-color: #34495e;
      border-radius: 5px;
    }

    form {
      background-color: white;
      padding: 20px;
      margin: 20px auto;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      max-width: 600px;
    }

    form label {
      font-weight: bold;
      margin-top: 10px;
      display: block;
    }

    form input, form select, form button, form textarea {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    form button {
      background-color: #3498db;
      color: white;
      border: none;
    }

    form button:hover {
      background-color: #2980b9;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    table th, table td {
      text-align: left;
      padding: 10px;
      border: 1px solid #ddd;
    }

    table th {
      background-color: #2c3e50;
      color: white;
    }

    .message {
      text-align: center;
      font-size: 1rem;
      color: green;
    }
  </style>
</head>
<body>
  <header>
    <h1>Budget Management</h1>
    <nav>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="inventory.php">Inventory</a></li>
            <li><a href="expenses.php">Expenses</a></li>
            <li><a href="budget.php">Budget</a></li>
            <li><a href="report.php">Report</a></li>
            <li><a href="profile.php">Account</a></li>
        </ul>
    </nav>
  </header>

  <main>
    <section>
      <h2>Create Budget</h2>
      <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
      <form method="POST" action="">
        <label for="amount">Budget Amount:</label>
        <input type="number" id="amount" name="amount" required>

        <label for="start-date">Start Date:</label>
        <input type="date" id="start-date" name="start-date" required>

        <label for="end-date">End Date:</label>
        <input type="date" id="end-date" name="end-date" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="3"></textarea>

        <button type="submit">Create Budget</button>
      </form>
    </section>

    <section>
      <h2>Budget List</h2>
      <table>
        <thead>
          <tr>
            <th>Amount</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Description</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($budgets)): ?>
            <?php foreach ($budgets as $budget): ?>
              <tr>
                <td><?= htmlspecialchars(number_format($budget['amount'], 2)) ?></td>
                <td><?= htmlspecialchars($budget['start_date']) ?></td>
                <td><?= htmlspecialchars($budget['end_date']) ?></td>
                <td><?= htmlspecialchars($budget['description']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" style="text-align: center;">No budgets created yet.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <footer>
    <p>&copy; 2025 Money Tracker</p>
  </footer>
</body>
</html>


