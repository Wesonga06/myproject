<?php
// Enable error reporting for debugging purposes
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connect.php'; // Include database connection script
session_start();

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'] ?? null;

// Check if the user is logged in
if (!$user_id) {
    header("Location: login.php"); // Redirect to login page if user isn't logged in
    exit();
}

// Initialize data
$summary = [];
$expenses = [];

// Fetch summary data
if ($stmt = $db->prepare("
    SELECT 
        (SELECT SUM(amount) FROM expenses WHERE user_id = ?) AS total_expenses,
        (SELECT SUM(amount) FROM budget WHERE user_id = ?) AS total_budget,
        (SELECT COUNT(*) FROM inventory WHERE user_id = ?) AS total_items
")) {
    $stmt->bind_param("sss", $user_id, $user_id, $user_id); // Treat user_id as VARCHAR
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $summary = $result->fetch_assoc();
    } else {
        $summary = ['total_expenses' => 0, 'total_budget' => 0, 'total_items' => 0];
    }
    $stmt->close();
}

// Fetch expenses breakdown
if ($stmt = $db->prepare("
    SELECT category, SUM(amount) AS total_amount 
    FROM expenses 
    WHERE user_id = ?
    GROUP BY category
")) {
    $stmt->bind_param("s", $user_id); // Treat user_id as VARCHAR
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $expenses[] = $row;
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Tracker Report</title>
    <link rel="icon" href="MART.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        /* General Styling */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
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
            gap: 15px;
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
        footer {
      text-align: center;
      background-color: #2c3e50;
      color: white;
      padding: 10px;
      margin-top: 20px;
    }
    </style>
</head>
<body>
<header>
    <h1>Money Tracker Report</h1>
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
            <tbody>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><?= htmlspecialchars($expense['category']) ?></td>
                            <td><?= number_format($expense['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">No expenses data available.</td>
                    </tr>
                <?php endif; ?>
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

