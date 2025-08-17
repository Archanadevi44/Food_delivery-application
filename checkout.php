<?php
session_start();
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's name from the session (set during login)
$username = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Guest'; // Default to 'Guest' if not set

// Fetch the user's pending order
$order_sql = "SELECT id FROM orders WHERE user_id = $user_id AND status = 'Pending'";
$order_result = mysqli_query($conn, $order_sql);

// If no pending order exists, redirect back to the menu
if (mysqli_num_rows($order_result) == 0) {
    echo "<p>No orders found. <a href='menu.php'>Go back to the menu</a></p>";
    exit();
} else {
    $order = mysqli_fetch_assoc($order_result);
    $order_id = $order['id'];

    // Fetch the order items
    $order_items_sql = "SELECT oi.quantity, d.name AS dish_name, d.price 
                        FROM order_items oi
                        INNER JOIN dishes d ON oi.dish_id = d.id
                        WHERE oi.order_id = $order_id";
    $order_items_result = mysqli_query($conn, $order_items_sql);

    // If no items found in the order, show message and link to the menu
    if (mysqli_num_rows($order_items_result) == 0) {
        echo "<p>Your cart is empty. <a href='restaurants.php'>Go back to the menu</a></p>";
        exit();
    }
}

// Proceed to checkout, calculate total amount
$grand_total = 0;
while ($row = mysqli_fetch_assoc($order_items_result)) {
    $grand_total += $row['price'] * $row['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        // Function to show the popup with confirmation message
        function showConfirmationPopup(message) {
            if (confirm(message)) {
                window.location.href = 'index.php'; // Redirect to index.php after confirmation
            }
        }

        // Function to confirm the order using AJAX
        function confirmOrder() {
            // Send AJAX request to confirm the order
            $.ajax({
                url: 'confirm_order.php', // The PHP script that handles the order confirmation
                type: 'POST',
                data: {
                    order_id: <?php echo $order_id; ?>,
                    grand_total: <?php echo $grand_total; ?>
                },
                success: function(response) {
                    // After a successful order confirmation, show the confirmation message
                    showConfirmationPopup(response);
                },
                error: function() {
                    alert("There was an error confirming your order. Please try again.");
                }
            });
        }
    </script>
</head>
<body>
    <!-- Display the personalized welcome message -->
    <h1 style="color:black">Welcome to the Restaurant Page, <?php echo htmlspecialchars($username); ?>!</h1>

    <h2 style="color:black">Your Order</h2>
    
    <table border="1">
        <thead>
            <tr>
                <th>Dish</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php
        // Reset the result pointer to the beginning to display items
        mysqli_data_seek($order_items_result, 0);

        while ($row = mysqli_fetch_assoc($order_items_result)) {
            $totalPrice = $row['price'] * $row['quantity'];
        ?>
            <tr>
                <td><?php echo htmlspecialchars($row['dish_name']); ?></td>
                <td><?php echo $row['quantity']; ?></td>
                <td><?php echo $row['price']; ?></td>
                <td><?php echo $totalPrice; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <h3>Total: $<?php echo $grand_total; ?></h3>

    <!-- Confirm Order Button -->
    <button type="button" onclick="confirmOrder()">Confirm Order</button>

    <link rel="stylesheet" href="css/checkout.css">
</body>
</html>
