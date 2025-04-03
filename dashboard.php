<?php



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
            justify-content: start;
            height: 100vh;
            text-align: center;
        }
        .header {
            font-size: 2.5rem;
            font-weight: 600;
            margin-top: 20px;
            background-color:  #2c3e50;
            color: #fff;
        }
        .sub-header {
            font-size: 1.2rem;
            margin-bottom: 20px;
            max-width: 80%;
            margin: 0 auto;
            color: #444;
        }
        header{
          background-color:  #2c3e50;  
        }
        .navbar {
            background: rgba(255, 255, 255, 0.3);
            padding: 10px;
            border-radius: 10px;
            margin-top: 15px;
            display: inline-block;
        }
        .navbar a {
            color: #333;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            padding: 8px 15px;
            border-radius: 5px;
            background: #fff;
            transition: 0.3s;
        }
        .navbar a:hover {
            background: #ddd;
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
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
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
            width: 150px;
            height: auto;
            display: block;
            margin: 15px auto;
        }
    </style>
</head>
<body>
  <header>
    <div class="header">Money Tracking Dashboard</div>
    <img src="MART.png" alt="Money Accounting & Resource Tracking Logo" class="logo">
    <div class="navbar">
        <a href="register.php">Click here to register</a>
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
    </div>
    </header>
    <div class="sub-header">
        Easily manage your expenses, savings, and financial goals. Join MART to boost your savings and track your financial spending efficiently.
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
    </div>

</body>
</html>
