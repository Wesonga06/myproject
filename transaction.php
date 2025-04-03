<?php
// Database connection
require 'db_connect.php';
session_start();

$user_id = $_SESSION['user_id']; // Retrieve the logged-in user's ID from the session

$errorMessages = [];
$isFormValid = true;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $category = $_POST["category"] ?? "";
    $type = $_POST["type"] ?? "";
    $amount = $_POST["amount"] ?? "";
    $name = $_POST["name"] ?? "";
    $date = date("Y-m-d H:i:s");

    // Validation
    if (empty($category)) {
        $errorMessages["category"] = "Category is required.";
        $isFormValid = false;
    }
    if (empty($type)) {
        $errorMessages["type"] = "Type is required.";
        $isFormValid = false;
    }
    if (empty($amount) || !is_numeric($amount)) {
        $errorMessages["amount"] = "Amount is required and must be a valid number.";
        $isFormValid = false;
    }
    if (empty($name)) {
        $errorMessages["name"] = "Name is required.";
        $isFormValid = false;
    }

    // Insert into database
    if ($isFormValid && $user_id) {
        $sql = "INSERT INTO transactions (user_id, category, type, amount, name, date) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("issdss", $user_id, $category, $type, $amount, $name, $date);
            $stmt->execute();
            $stmt->close();
            $successMessage = "Transaction added successfully!";
        } else {
            $errorMessages["database"] = "Failed to prepare the SQL statement: " . $db->error;
        }
    }
}

// Fetch all transactions for the logged-in user
$transactions = [];
if ($user_id) {
    $sql = "SELECT * FROM transactions WHERE user_id = ? ORDER BY id DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    $stmt->close();
}
?>
  
  

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Money Tracker</title>
    <link rel="icon" href="MART.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
            text-align: center;
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
            background: rgba(255, 255, 255, 0.9);
            padding: 50px;
            box-sizing: border-box;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .error-message {
            color: red;
            font-size: 0.85rem;
            display: none;
        }
        button {
            background: #4CAF50;
            color: white;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        table {
            width: 80%;
            margin: auto;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background: #4CAF50;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Transactions</h1>
        <img src="MART.png" alt="Money Accounting & Resource Tracking" width="100">
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
    <section id="transactions">
            <h2>Add Transaction</h2>
            <form id="transaction-form" method="POST" action="transaction.php" onsubmit="resetform()">
                <label for="type">Transaction Type:</label>
                <select id="type" name="type">
                    <option value="">--Select one--</option>
                    <option value="expense" <?= isset($_POST["type"]) && $_POST["type"] == "expense" ? "selected" : "" ?>>Expense</option>
                    <option value="savings" <?= isset($_POST["type"]) && $_POST["type"] == "savings" ? "selected" : "" ?>>Savings</option>
                </select>
                <div class="error-message"><?= $errorMessages["type"] ?? '' ?></div>

                <label for="amount">Amount (KES):</label>
                <input type="number" id="amount" name="amount" value="<?= htmlspecialchars($_POST["amount"] ?? '') ?>">
                <div class="error-message"><?= $errorMessages["amount"] ?? '' ?></div>

                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST["name"] ?? '') ?>">
                <div class="error-message"><?= $errorMessages["name"] ?? '' ?></div>

                <label for="category">Category:</label>
                <select id="category" name="category">
                    <option value="">--Select a category--</option>
                    <option value="groceries" <?= isset($_POST["category"]) && $_POST["category"] == "groceries" ? "selected" : "" ?>>Groceries</option>
                    <option value="utilities" <?= isset($_POST["category"]) && $_POST["category"] == "utilities" ? "selected" : "" ?>>Utilities</option>
                    <option value="entertainment" <?= isset($_POST["category"]) && $_POST["category"] == "entertainment" ? "selected" : "" ?>>Entertainment</option>
                    <option value="savings" <?= isset($_POST["category"]) && $_POST["category"] == "savings" ? "selected" : "" ?>>Savings</option>
                </select>
                <div class="error-message"><?= $errorMessages["category"] ?? '' ?></div>

                <button type="submit">Add Transaction</button>
            </form>
            <script>
                function resetForm(){
                    document.getElementById("transaction-form").reset();
                }
                </script>
        </section>
    
        <section id="transaction-list">
    <h2>Transaction List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Category</th>
                <th>Amount (KES)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody id="transaction-table-body">
            <?php if (count($transactions) > 0): ?>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction["id"]) ?></td>
                        <td><?= htmlspecialchars($transaction["name"]) ?></td>
                        <td><?= htmlspecialchars($transaction["type"]) ?></td>
                        <td><?= htmlspecialchars($transaction["category"]) ?></td>
                        <td><?= htmlspecialchars($transaction["amount"]) ?></td>
                        <td><?= htmlspecialchars($transaction["date"]) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No transactions found</td>
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
