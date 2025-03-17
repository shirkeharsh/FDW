<?php
include('db/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $cart = json_decode($_POST['cart'], true);
    
    $orderItems = "";
    $totalAmount = 0;
    
    foreach ($cart as $item) {
        $orderItems .= $item['name'] . " (x" . $item['quantity'] . "), ";
        $totalAmount += ($item['price'] * $item['quantity']);
    }
    
    $orderQuery = "INSERT INTO orders (customer_name, phone, address, items, total_amount) 
                   VALUES ('$name', '$phone', '$address', '$orderItems', '$totalAmount')";
                   
    if (mysqli_query($conn, $orderQuery)) {
        echo "Order Placed Successfully!";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
