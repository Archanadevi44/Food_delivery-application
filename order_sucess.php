<?php
include 'session_handler.php';
include 'config.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the confirmed order details
$order_sql = "SELECT id, status FROM orders WHERE user_id = $user_id AND status = 'Confirmed'";
$order_result = mysqli_query($conn, $order_sql);

if (mysqli_num_rows($order_result) == 0) {
    echo "<p>No confirmed orders found. <a href='restaurants.php'>Go back to the menu</a></p>";
    exit();
}

$order = mysqli_fetch_assoc($order_result);
$order_id = $order['id'];

// Fetch the order items
$order_items_sql = "SELECT oi.quantity, d.name AS dish_name, d.price
                    FROM order_items oi
                    INNER JOIN dishes d ON oi.dish_id = d.id
                    WHERE oi.order_id = $order_id";
$order_items_result = mysqli_query($conn, $order_items_sql);

// Calculate grand total
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
    <title>Order Summary</title>
    <link rel="stylesheet" href="css/order_success.css">
</head>
<body>
    <header>
        <h1>Order Summary</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </header>

    <h2>Your Confirmed Order</h2>
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
        mysqli_data_seek($order_items_result, 0); // Reset pointer

        while ($row = mysqli_fetch_assoc($order_items_result)) {
            $totalPrice = $row['price'] * $row['quantity'];
            echo "<tr>
                    <td>" . htmlspecialchars($row['dish_name']) . "</td>
                    <td>" . $row['quantity'] . "</td>
                    <td>$" . $row['price'] . "</td>
                    <td>$" . $totalPrice . "</td>
                  </tr>";
        }
        ?>
        </tbody>
    </table>

    <h3>Grand Total: $<?php echo $grand_total; ?></h3>
    <p>Your order has been confirmed successfully!</p>

    <a href="restaurants.php">Continue Shopping</a> | <a href="order_history.php">View Order History</a>
</body>
</html>