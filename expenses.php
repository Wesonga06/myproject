<?php
require 'db_connect.php'; // Include your database connection script
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login
    exit();
}
$user_id = $_SESSION['user_id'];

// Initialize variables
$message = "";

// Handle form submission for adding expenses
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $amount = $_POST["amount"];
    $date = $_POST["date"];
    $category = $_POST["category"];
    $description = $_POST["description"] ?? "";

    // Validate inputs
    if (!empty($amount) && !empty($date) && !empty($category)) {
        $stmt = $db->prepare("INSERT INTO expenses (user_id, amount, date, category, description) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("idsss", $user_id, $amount, $date, $category, $description);

            if ($stmt->execute()) {
                $message = "Expense added successfully!";
            } else {
                $message = "Failed to add expense: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $message = "Failed to prepare the SQL statement.";
        }
    } else {
        $message = "Please fill in all required fields.";
    }
}

// Fetch all expenses
$expenses = [];
$stmt = $db->prepare("SELECT * FROM expenses WHERE user_id = ? ORDER BY date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }
}
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Expenses - Money Tracker</title>
  <link rel="icon" href="MART.png" type="image/png">

  <style>
    /* General Styles */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
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

    /* Main Section */
    .expense-section {
      max-width: 500px;
      margin: 20px auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
      text-align: center;
      color: #2c3e50;
    }

    /* Form Styles */
    form label {
      font-weight: bold;
      margin-top: 10px;
    }

    form input, form select, form textarea, form button {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 5px;
      font-size: 16px;
    }

    form button {
      margin-top: 15px;
      background-color: #3498db;
      color: white;
      border: none;
      font-size: 16px;
      cursor: pointer;
    }

    form button:hover {
      background-color: #2980b9;
    }

    /* Expense Table */
    table {
      width: 80%;
      margin: 20px auto;
      border-collapse: collapse;
      background: white;
      border-radius: 8px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }

    table th, table td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    table th {
      background-color: #2c3e50;
      color: white;
    }

    /* Footer */
    footer {
      text-align: center;
      background-color: #2c3e50;
      color: white;
      padding: 10px;
      margin-top: 20px;
    }

    /* Feedback Message */
    .message {
      text-align: center;
      color: green;
      font-size: 1rem;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <header>
    <h1>Expenses</h1>
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
    <section class="expense-section">
      <h2>Add Expense</h2>
      <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
      <?php endif; ?>
      <form method="POST" action="">
      <label for="category">Category:</label>
        <select id="category" name="category" required>
          <option value="">Select Category</option>
          <option value="food">Food</option>
          <option value="transport">Transport</option>
          <option value="rent">Rent</option>
          <option value="utilities">Utilities</option>
          <option value="entertainment">Entertainment</option>
          <option value="other">Other</option>
        </select>
        <label for="amount">Amount:</label>
        <input type="number" id="amount" name="amount" required>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="3"></textarea>

        <button type="submit">Add Expense</button>
      </form>
    </section>

    <h2 style="text-align: center;">Expense List</h2>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Category</th>
          <th>Amount (KES)</th>
          <th>Description</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($expenses)): ?>
          <?php foreach ($expenses as $expense): ?>
            <tr>
              <td><?= htmlspecialchars($expense['date']) ?></td>
              <td><?= htmlspecialchars($expense['category']) ?></td>
              <td><?= htmlspecialchars(number_format($expense['amount'], 2)) ?></td>
              <td><?= htmlspecialchars($expense['description']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="4" style="text-align: center;">No expenses recorded yet.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>

  <footer>
    <p>&copy; 2025 Money Tracker</p>
  </footer>
</body>
</html>

