<?php
session_start();
include 'config.php';

// Handle the login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Query to fetch user data by email
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Check if the stored password is hashed or plain text
        $stored_password = $user['password'];

        // Verify the password (hashed or plain text)
        if (
            password_verify($password, $stored_password) || 
            $password === $stored_password
        ) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name']; // Store the name from the database

            // Set cookies for user ID and email (expire in 7 days)
            setcookie("user_id", $user['id'], time() + (7 * 24 * 60 * 60), "/");
            setcookie("user_email", $user['email'], time() + (7 * 24 * 60 * 60), "/");

            // Redirect to restaurants page
            header("Location: restaurants.php");
            exit;
        } else {
            echo "<p style='color:red;'>Invalid credentials (Password mismatch).</p>";
        }
    } else {
        echo "<p style='color:red;'>No user found with that email.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <header>
        <h1>Login to Food Delivery</h1>
    </header>
    <div class="container">
        <form method="POST" action="login.php">
            <label>Email:</label>
            <input type="email" name="email" required><br>
            <label>Password:</label>
            <input type="password" name="password" required><br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Sign up here</a></p>
    </div>
</body>
</html>
