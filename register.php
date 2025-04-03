<?php
require 'db_connect.php'; // Include your database connection script
session_start(); // Start session to handle user data

// Initialize error message
$errorMessage = "";

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $user_name = trim($_POST['user_name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];
    $role = $_POST['role'] ?? 'user'; // Default role is 'user'

    // Password validation
    if ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/\d/', $password) || !preg_match('/[@#$%^&+=!]/', $password)) {
        $errorMessage = "Password must be at least 8 characters long, contain one uppercase letter, one number, and one special character.";
    } else {
        // Check for duplicate email
        $email_check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $email_check->bind_param("s", $email);
        $email_check->execute();
        $email_check->store_result();

        if ($email_check->num_rows > 0) {
            $errorMessage = "An account with this email already exists.";
        } else {
            // Hash the password and insert data into database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (user_name, email, password, role) VALUES (?, ?, ?, ?)");

            if ($stmt) {
                $stmt->bind_param("ssss", $user_name, $email, $hashedPassword, $role);

                if ($stmt->execute()) {
                    // Save user ID and role into session
                    $_SESSION['user_id'] = $db->insert_id;
                    $_SESSION['role'] = $role;

                    // Redirect based on user role
                    header("Location: " . ($role === 'admin' ? "admin.php" : "dashboard.php"));
                    exit();
                } else {
                    $errorMessage = "Registration failed: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $errorMessage = "Failed to prepare the SQL statement.";
            }
        }
        $email_check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const registerForm = document.getElementById('register-form');
            const namePattern = /^[A-Za-z\s]+$/;
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!]).{8,}$/;

            registerForm.addEventListener('submit', function (event) {
                if (!validateForm()) {
                    event.preventDefault();
                }
            });

            function validateForm() {
                return validateUsername() && validateEmail() && validatePassword() && validateConfirmPassword();
            }

            function validateUsername() {
                const usernameInput = document.getElementById('user_name');
                const error = document.getElementById('username-error');
                if (!namePattern.test(usernameInput.value)) {
                    error.textContent = "Username must only contain letters.";
                    return false;
                }
                error.textContent = "";
                return true;
            }

            function validateEmail() {
                const emailInput = document.getElementById('email');
                const error = document.getElementById('email-error');
                if (!emailPattern.test(emailInput.value)) {
                    error.textContent = "Enter a valid email address.";
                    return false;
                }
                error.textContent = "";
                return true;
            }

            function validatePassword() {
                const passwordInput = document.getElementById('password');
                const error = document.getElementById('password-error');
                if (!passwordPattern.test(passwordInput.value)) {
                    error.textContent = "Password must include at least one uppercase letter, one number, and one special character.";
                    return false;
                }
                error.textContent = "";
                return true;
            }

            function validateConfirmPassword() {
                const passwordInput = document.getElementById('password');
                const confirmPasswordInput = document.getElementById('confirm-password');
                const error = document.getElementById('confirm-password-error');
                if (passwordInput.value !== confirmPasswordInput.value) {
                    error.textContent = "Passwords do not match.";
                    return false;
                }
                error.textContent = "";
                return true;
            }
        });
    </script>
</head>
<body>
    <form id="register-form" method="POST" action="">
        <h2>Create Your Account</h2>
        <?php if (!empty($errorMessage)): ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>
        <label for="user_name">Username:</label>
        <input type="text" id="user_name" name="user_name" required>
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

        <label for="role">Role:</label>
        <select id="role" name="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Register</button>
    </form>
</body>
</html>

