<?php
session_start();
include 'config.php';

// Check if session or cookies exist for the user
if (!isset($_SESSION['user_id']) && (!isset($_COOKIE['user_id']) || !isset($_COOKIE['user_email']))) {
    header("Location: login.php");
    exit;
}

// If cookies exist but session is not set, populate session using cookie data
if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id']) && isset($_COOKIE['user_email'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['user_email'] = $_COOKIE['user_email'];
    $_SESSION['user_name'] = $_COOKIE['user_name'];  // Populate user name from cookies
}

// Renew cookies on activity (extend for 30 minutes)
if (isset($_COOKIE['user_id']) && isset($_COOKIE['user_email']) && isset($_COOKIE['user_name'])) {
    setcookie("user_id", $_COOKIE['user_id'], time() + (30 * 60), "/");
    setcookie("user_email", $_COOKIE['user_email'], time() + (30 * 60), "/");
    setcookie("user_name", $_COOKIE['user_name'], time() + (30 * 60), "/");
}

// Get user details from session or cookies
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];
$username = $_SESSION['user_name'];  // Get the username from session

// Display the welcome message with the user's name
echo "<h1>Welcome to the Restaurant Page, " . htmlspecialchars($username) . "!</h1>";

// Track the restaurant the user is viewing
$restaurant_id = isset($_GET['restaurant_id']) ? $_GET['restaurant_id'] : '';

// Set a cookie for the last visited restaurant (optional)
if ($restaurant_id) {
    setcookie("last_visited_restaurant", $restaurant_id, time() + (30 * 60), "/");
}

// Check if the restaurant_id is not empty
if ($restaurant_id != '') {
    // Perform the SQL query to get restaurant details based on the restaurant_id
    $result = mysqli_query($conn, "SELECT * FROM dishes WHERE restaurant_id='$restaurant_id'");

    // Check if the query was successful
    if ($result) {
        echo "<h1>Menu</h1>";
        echo "<div class='menu-container'>"; // Added container for the menu
        while ($row = mysqli_fetch_assoc($result)) {
            // Image path logic for Pizza Palace (assuming restaurant_id 1 is Pizza Palace)
            if ($restaurant_id == 1) {
                if ($row['name'] == "Pepperoni Pizza") {
                    $image_path = "pizza_images/Pepperoni.jfif";
                } elseif ($row['name'] == "Veggie Pizza") {
                    $image_path = "pizza_images/veggie.jfif";
                } elseif ($row['name'] == "Margherita Pizza") {
                    $image_path = "pizza_images/marghrita.jfif";
                } else {
                    $image_path = "pizza_images/default_pizza.jpg";
                }
            }
            // Image path logic for Sushi World (assuming restaurant_id 2 is Sushi World)
            elseif ($restaurant_id == 2) {
                if ($row['name'] == "Salmon Nigiri") {
                    $image_path = "sushi_images/salmon.jfif";
                } elseif ($row['name'] == "California Roll") {
                    $image_path = "sushi_images/californiaroll.jfif";
                } elseif ($row['name'] == "Spicy Tuna Roll") {
                    $image_path = "sushi_images/spicytuna.jfif";
                } else {
                    $image_path = "sushi_images/default_sushi.jpg"; // Default image for other sushi types
                }
            } else {
                $image_path = "images/restaurant_default.jpg"; // Default image for other restaurants
            }

            // Display the dish with its image
            echo "<div class='dish-card'>";
            echo "<img src='$image_path' alt='" . htmlspecialchars($row['name']) . "'>";
            echo "<h2>" . htmlspecialchars($row['name']) . "</h2>";
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            echo "<p>Price: $" . htmlspecialchars($row['price']) . "</p>";       
            echo "<a href='your_order.php?dish_id=" . $row['id'] . "&restaurant_id=$restaurant_id' class='add-to-order-button'><button>Add to Order</button></a>";
            echo "</div>";
        }
        echo "</div>";
    } else {
        echo "Error: " . mysqli_error($conn); // Display query error if failed
    }
} else {
    echo "Restaurant ID is missing! Please check the URL."; // Provide more clarity
}
?>
    <link rel="stylesheet" href="css/dishes.css">
