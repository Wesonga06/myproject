<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $stmt = $db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo "User registered successfully!";
    } else {
        echo "Registration failed: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Money Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}"> <!-- External CSS -->
    <link rel="icon" href="MART.png">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            align-items: center;
            min-height: 100vh;
        }
         /* Header */
    header {
        background-color: #2c3e50;
      color: white;
      padding: 15px;
      text-align: center;
    }

        .logo {
            width: 120px;
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
        .form {
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
        .login-link {
            text-align: center;
            margin-top: 10px;
        }
        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }
    </style>

<script>
      document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('register-form');

    // Validation patterns
    const namePattern = /^[A-Za-z\s]+$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!]).{8,}$/;

    if (registerForm) {
        registerForm.addEventListener('submit', function(event) {
            event.preventDefault();
            if (validateRegistration()) {
                alert('Registration successful! Redirecting to home page...');
                window.location.href = 'transaction.php';
            }
        });
    }

    function validateRegistration() {
        return validateUsername() && validateEmail() && validatePassword() && validateConfirmPassword() && validateDOB();
    }

    function validateLogin() {
        return validateLoginEmail() && validateLoginPassword();
    }

    function validateUsername() {
        const errorMessage = document.getElementById('username-error');
        if (!namePattern.test(registerForm.username.value)) {
            errorMessage.textContent = 'Username must contain only alphabetic characters.';
            errorMessage.style.color = 'red';
            return false;
        }
        errorMessage.textContent = '';
        return true;
    }

    function validateEmail() {
        const errorMessage = document.getElementById('email-error');
        if (!emailPattern.test(registerForm.email.value)) {
            errorMessage.textContent = 'Enter a valid email address.';
            errorMessage.style.color = 'red';
            return false;
        }
        errorMessage.textContent = '';
        return true;
    }

    function validatePassword() {
        const errorMessage = document.getElementById('password-error');
        if (!passwordPattern.test(registerForm.password.value)) {
            errorMessage.textContent = 'Password must contain at least one uppercase, one lowercase, one digit, and one special character.';
            errorMessage.style.color = 'red';
            return false;
        }
        errorMessage.textContent = '';
        return true;
    }

    function validateConfirmPassword() {
        const errorMessage = document.getElementById('confirm-password-error');
        if (registerForm.password.value !== registerForm['confirm-password'].value) {
            errorMessage.textContent = 'Passwords do not match.';
            errorMessage.style.color = 'red';
            return false;
        }
        errorMessage.textContent = '';
        return true;
    }

    function validateDOB() {
        const errorMessage = document.getElementById('dob-error');
        const today = new Date();
        const dobDate = new Date(registerForm.dob.value);
        const age = today.getFullYear() - dobDate.getFullYear();
        if (age < 18) {
            errorMessage.textContent = 'You must be at least 18 years old to register.';
            errorMessage.style.color = 'red';
            return false;
        }
        errorMessage.textContent = '';
        return true;
    }
});
    </script>
</head>
<body>
<header>
        <h1>Register</h1>
        <img src="MART.png" alt="Money Accounting & Resource Tracking" class="logo">
    </header>

    <main>
        <h2>Create Your Account</h2>
        <form class="form" id="register-form" action="{{ route('register') }}" method="POST">

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <span id="username-error" class="error-message"></span>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <span id="email-error" class="error-message"></span>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <span id="password-error" class="error-message"></span>

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
            <span id="confirm-password-error" class="error-message"></span>

            <label for="dob">Date of Birth:</label>
            <input type="date" id="dob" name="dob" required>
            <span id="dob-error" class="error-message"></span>

            <button type="submit">Register</button>
        </form>
        <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
    </main>

    <footer>
        <p>&copy; 2025 Money Tracker</p>
    </footer>
</body>
</html>
