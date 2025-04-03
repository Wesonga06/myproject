<?php
require 'db_connect.php'; // Include your database connection script
session_start();

// Initialize error message
$errorMessage = "";

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email']; // Retrieve email from the form
    $password = $_POST['password']; // Retrieve password from the form

    // Prepare statement to fetch user details
    $stmt = $db->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Save user ID and role to session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin.php"); // Redirect to admin panel
            } else {
                header("Location: dashboard.php"); // Redirect to user dashboard
            }
            exit();
        } else {
            $errorMessage = "Invalid password.";
        }
    } else {
        $errorMessage = "User not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Money Tracker</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input, button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            cursor: pointer;
            margin-top: 15px;
        }
        button:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            font-size: 0.85rem;
            text-align: center;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('login-form');

            // Validation patterns
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const passwordPattern = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%^&+=!]).{8,}$/;

            loginForm.addEventListener('submit', function(event) {
                if (!validateLogin()) {
                    event.preventDefault(); // Stop form submission if validation fails
                }
            });

            function validateLogin() {
                return validateEmail() && validatePassword();
            }

            function validateEmail() {
                const email = document.getElementById('email').value;
                const errorMessage = document.getElementById('email-error');

                if (!emailPattern.test(email)) {
                    errorMessage.textContent = 'Enter a valid email address.';
                    errorMessage.style.color = 'red';
                    return false;
                }
                errorMessage.textContent = '';
                return true;
            }

            function validatePassword() {
                const password = document.getElementById('password').value;
                const errorMessage = document.getElementById('password-error');

                if (!passwordPattern.test(password)) {
                    errorMessage.textContent = 'Password must contain at least one uppercase, one lowercase, one digit, and one special character.';
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
    <form id="login-form" action="login.php" method="POST">
        <h2>Login to Your Account</h2>

        <!-- Display error message -->
        <div class="error-message">
            <?php if (!empty($errorMessage)) echo htmlspecialchars($errorMessage); ?>
        </div>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <span id="email-error" class="error-message"></span>

        <!-- Password -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <span id="password-error" class="error-message"></span>

        <!-- Submit -->
        <button type="submit">Login</button>
        <p>New user? <a href="register.php">Register here</a></p>
    </form>
</body>
</html>
