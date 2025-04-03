<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connect.php'; // Include database connection script
session_start();

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'] ;

// Check if the user is logged in
if (!$user_id) {
    header("Location: login.php"); // Redirect to login if the user is not logged in
    exit();
}

// Fetch summary data for the logged-in user
$summary = [];
$result = $db->prepare("
    SELECT 
        (SELECT SUM(amount) FROM expenses WHERE user_id = ?) AS total_expenses,
        (SELECT SUM(amount) FROM budget WHERE user_id = ?) AS total_budget,
        (SELECT COUNT(*) FROM inventory WHERE user_id = ?) AS total_items
");
$result->bind_param("iii", $user_id, $user_id, $user_id);
$result->execute();
$queryResult = $result->get_result();

if ($queryResult->num_rows > 0) {
    $summary = $queryResult->fetch_assoc();
}

$result->close();

// Fetch expenses breakdown data for the logged-in user
$expenses = [];
$result = $db->prepare("
    SELECT category, SUM(amount) AS total_amount 
    FROM expenses 
    WHERE user_id = ?
    GROUP BY category
");
$result->bind_param("i", $user_id);
$result->execute();
$queryResult = $result->get_result();

if ($queryResult->num_rows > 0) {
    while ($row = $queryResult->fetch_assoc()) {
        $expenses[] = $row;
    }
}

$result->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reports - Money Tracker</title>
  <link rel="icon" href="MART.png" type="image/png">
  <style>
    /* General Styling */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
    }
    header, footer {
      background-color: #2c3e50;
      color: white;
      text-align: center;
      padding: 10px 0;
    }
    nav ul {
      list-style: none;
      padding: 0;
      display: flex;
      justify-content: center;
      gap: 20px;
    }
    nav ul li {
      display: inline;
    }
    nav ul li a {
      text-decoration: none;
      color: white;
      font-weight: bold;
      padding: 10px 15px;
      transition: background-color 0.3s;
    }
    nav ul li a:hover {
      background-color: #34495e;
      border-radius: 5px;
    }
    main {
      max-width: 900px;
      margin: 20px auto;
      padding: 20px;
    }
    h2 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      margin-top: 20px;
      border-collapse: collapse;
      background-color: white;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    table th, table td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
    }
    table th {
      background-color: #2c3e50;
      color: white;
    }
    .chart-container {
      margin-top: 30px;
      text-align: center;
    }
  </style>
</head>
<body>
<header>
  <h1>Reports</h1>
  <nav>
       <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="inventory.php">Inventory</a></li>
                <li><a href="expenses.php">Expenses</a></li>
                <li><a href="budget.php">Budget</a></li>
                <li><a href="report.php">Report</a></li>
                <li><a href="account.html">Account</a></li>
            </ul>
  </nav>
</header>

<main>
  <!-- Summary Section -->
  <section>
    <h2>Summary</h2>
    <table>
      <thead>
        <tr>
          <th>Total Expenses (KES)</th>
          <th>Total Budget (KES)</th>
          <th>Total Inventory Items</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= number_format($summary['total_expenses'] ?? 0, 2) ?></td>
          <td><?= number_format($summary['total_budget'] ?? 0, 2) ?></td>
          <td><?= $summary['total_items'] ?? 0 ?></td>
        </tr>
      </tbody>
    </table>
  </section>

  <!-- Expenses Breakdown Section -->
  <section>
    <h2>Expenses Breakdown</h2>
    <table>
      <thead>
        <tr>
          <th>Category</th>
          <th>Total Amount (KES)</th>
        </tr>
      </thead>
      <tbody id="expenses-list">
        <?php foreach ($expenses as $expense): ?>
          <tr>
            <td><?= htmlspecialchars($expense['category']) ?></td>
            <td><?= number_format($expense['total_amount'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <!-- Chart Container -->
  <section class="chart-container">
    <h2>Expenses Chart</h2>
    <canvas id="expenses-chart" width="400" height="200"></canvas>
  </section>
</main>

<footer>
  <p>&copy; 2025 Money Tracker</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('expenses-chart').getContext('2d');
    const expenseData = <?php echo json_encode($expenses); ?>;

    const labels = expenseData.map(expense => expense.category);
    const data = expenseData.map(expense => expense.total_amount);

    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Expenses (KES)',
          data: data,
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  });
</script>
</body>
</html>
