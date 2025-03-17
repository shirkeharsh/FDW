<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart = json_decode($_POST['cart'], true);
    file_put_contents("cart.json", json_encode($cart));
    echo json_encode(["status" => "success", "message" => "Cart saved!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
