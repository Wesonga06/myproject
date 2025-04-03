<?php
require 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; // Save user ID in session
            echo "Login successful!";
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" type="css" href="MART.png">
    <link rel="stylesheet" href="Indexcss.css">
    <script>
    const loginForm = document.getElementById('login-form');

    // Validation patterns
    const namePattern = /^[A-Za-z\s]+$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!]).{8,}$/;

    if (loginForm) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            if (validateLogin()) {
                alert('Login successful! Redirecting to home page...');
                window.location.href = 'transaction.php';
            }
        });
    }

function validateLoginEmail() {
        const errorMessage = document.getElementById('login-email-error');
        if (!emailPattern.test(loginForm.email.value)) {
            errorMessage.textContent = 'Enter a valid email address.';
            errorMessage.style.color = 'red';
            return false;
        }
        errorMessage.textContent = '';
        return true;
    }

    function validateLoginPassword() {
        const errorMessage = document.getElementById('login-password-error');
        if (!passwordPattern.test(loginForm.password.value)) {
            errorMessage.textContent = 'Invalid password format.';
            errorMessage.style.color = 'red';
            return false;
        }
        errorMessage.textContent = '';
        return true;
    }
    </script>

<style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        header {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: center;
        }
        .logo {
            width: 10px;
            margin-top: 10px;
        }
        main {
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            margin-top: 20px;
        }
        h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            margin: 5px 0;
        }
        input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }
        .error-message {
            color: red;
            font-size: 0.85rem;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            margin-top: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }
        button:hover {
            background-color: #45a049;
        }
        .register-link {
            text-align: center;
            margin-top: 10px;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<header>
        <h1>Login</h1>
        <img src="MART.png" alt="Money Accounting & Resource Tracking">
    </header>
    <main>
        <!-- Login Form -->
        <section id="login">
            <h2>Login</h2>
            <form id="login-form">
                <label for="login-email">Email:</label>
                <input type="email" id="login-email" name="email" required>
                <span id="login-email-error" class="error-message"></span>

                <label for="login-password">Password:</label>
                <input type="password" id="login-password" name="password" required>
                <span id="login-password-error" class="error-message"></span>

                <button type="submit">Login</button>
            </form>
            <p>New user? <a href="register.php">Register here</a></p>
        </section>
    </main>
    <footer>
        <p>&copy; 2025 Money Tracker</p>
      </footer>
</body>
</html>