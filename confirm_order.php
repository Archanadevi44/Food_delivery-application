<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ensure the order ID and grand total are provided
    if (isset($_POST['order_id']) && isset($_POST['grand_total'])) {
        $order_id = $_POST['order_id'];
        $grand_total = $_POST['grand_total'];

        // Update the order status to 'Confirmed'
        $confirm_order_sql = "UPDATE orders SET status = 'Confirmed' WHERE id = $order_id";
        if (mysqli_query($conn, $confirm_order_sql)) {
            // Return success message
            echo "Order confirmed! Your total is: $" . number_format($grand_total, 2);
        } else {
            echo "Error confirming the order. Please try again.";
        }
    } else {
        echo "Missing order details.";
    }
}
?>
