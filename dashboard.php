<?php
session_start();

// Simulating dynamic user data for the dashboard
$user_name = $_SESSION['user_name'] ?? 'Guest';
$dashboard_message = "Welcome to your Money Tracking Dashboard! Manage your finances efficiently.";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Money Tracking Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            position: sticky;
            top: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }
        .header-title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .navbar {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            font-size: 1rem;
            font-weight: bold;
            padding: 8px 15px;
            border-radius: 5px;
            background: #34495e;
            transition: 0.3s;
        }
        .navbar a:hover {
            background: #1abc9c;
        }
        .sub-header {
            font-size: 1.2rem;
            text-align: center;
            margin: 20px auto;
            color: #444;
            max-width: 80%;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }
        .card {
            background: #C6DEF1;
            padding: 20px;
            width: 260px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }
        .card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        .card h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #444;
        }
        .card p {
            font-size: 1rem;
            color: #555;
            line-height: 1.5;
        }
        .logo {
            width: 120px;
            height: auto;
            display: block;
            margin: 15px auto;
        }
    </style>
</head>
<body>
    <header>
        <img src="MART.png" alt="Money Accounting & Resource Tracking Logo" class="logo">
        <div class="header-title">Money Tracking Dashboard</div>
        <div class="navbar">
            <a href="dashboard.php">Dashboard</a>
            <a href="inventory.php">Inventory</a>
            <a href="expenses.php">Expenses</a>
            <a href="budget.php">Budget</a>
            <a href="report.php">Reports</a>
            <a href="profile.php">Profile</a>
        </div>
    </header>

    <div class="sub-header">
        <p><?php echo htmlspecialchars($dashboard_message); ?></p>
        <p>Hello, <strong><?php echo htmlspecialchars($user_name); ?></strong>. Explore tools to track your expenses, savings, and financial goals effectively.</p>
    </div>

    <div class="card-container">
        <div class="card">
            <img src="https://th.bing.com/th/id/OIP.Yd718ONEFY1ITLivju_9tAHaE8?rs=1&pid=ImgDetMain" alt="Expense Tracking">
            <h3>Expense Tracking</h3>
            <p>Monitor your daily spending and ensure financial discipline.</p>
        </div>
        <div class="card">
            <img src="https://static.vecteezy.com/system/resources/previews/002/712/505/non_2x/bundle-of-saving-management-icons-and-saving-management-lettering-vector.jpg" alt="Savings Management">
            <h3>Savings Management</h3>
            <p>Plan your savings with detailed insights and reports.</p>
        </div>
        <div class="card">
            <img src="https://cdn4.vectorstock.com/i/1000x1000/24/58/finance-and-banking-budget-planning-concept-vector-25102458.jpg" alt="Budget Planning">
            <h3>Budget Planning</h3>
            <p>Set budgets and stick to your financial goals.</p>
        </div>
        <div class="card">
            <img src="https://th.bing.com/th/id/OIP.dRHESRQnUQuon1e5Iufs1AHaG2?rs=1&pid=ImgDetMain" alt="Secure & Private">
            <h3>Secure & Private</h3>
            <p>Your financial data is protected with top security standards.</p>
        </div>
        <div class="card">
            <img src="https://static.vecteezy.com/system/resources/previews/026/740/313/original/financial-growth-and-enrichment-business-and-investment-value-increase-income-and-wage-growth-economic-development-economic-and-market-stability-happy-man-at-the-top-of-the-graph-with-a-coin-vector.jpg" alt="Track Progress">
            <h3>Track Progress</h3>
            <p>Visualize your financial journey through detailed analytics.</p>
        </div>
    </div>
</body>
</html>
