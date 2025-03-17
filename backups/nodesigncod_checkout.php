<?php
$host = "13.200.73.247";
$username = "jerry";
$password = "admin";
$dbname = "hvh";

// Connect to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed"]));
}

// Initialize phone variable
$phone = "N/A";

// Process the AJAX request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header("Content-Type: application/json");

    // Read JSON input from JavaScript
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Get phone number from session data
    $phone = isset($data["phone"]) ? trim($data["phone"]) : "N/A";

    // Default values if phone is not found in the database
    $fname = "Unknown";
    $lname = "User";

    // Check if phone exists and fetch user details
    if ($phone !== "N/A") {
        $stmt = $conn->prepare("SELECT fname, lname FROM users WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $stmt->bind_result($db_fname, $db_lname);
        
        if ($stmt->fetch()) {
            $fname = $db_fname;
            $lname = $db_lname;
        }
        $stmt->close();
    }

    $cartItems = isset($data["cartData"]) ? json_decode($data["cartData"], true) : [];

    $formattedCart = [];
    if (is_array($cartItems)) {
        foreach ($cartItems as $key => $item) {
            $formattedCart[] = [
                "name" => $item["name"] ?? $key,
                "price" => $item["price"] ?? 0,
                "quantity" => $item["quantity"] ?? 1,
                "total" => ($item["price"] ?? 0) * ($item["quantity"] ?? 1)
            ];
        }
    }

    // Calculate final amount
    $originalPrice = $data["originalPrice"] ?? "0";
    $totalAmount = $data["total_amount"] ?? "";
    $finalAmount = !empty($totalAmount) ? $totalAmount : $originalPrice;

    $sessionArray = [
        "Phone" => $phone,
        "Customer Name" => "$fname $lname",
        "Flat" => $data["flat"] ?? "N/A",
        "Area" => $data["area"] ?? "N/A",
        "Landmark" => $data["landmark"] ?? "N/A",
        "Location" => $data["location"] ?? "N/A",
        "Original Price" => $originalPrice,
        "Applied Coupon" => $data["applied_coupon"] ?? "None",
        "Applied Discount (%)" => $data["applied_discount_percent"] ?? "0",
        "Applied Discount Amount" => $data["applied_discount_amount"] ?? "0",
        "Existing Wallet Balance" => $data["existing_wallet_balance"] ?? "0",
        "Updated Wallet Balance" => $data["updated_wallet_balance"] ?? "0",
        "Total Amount" => $totalAmount,
        "Final Amount" => $finalAmount,
        "Cart Items" => $formattedCart
    ];

    echo json_encode(["status" => "success", "sessionData" => $sessionArray]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt</title>
</head>
<body>
    <h2>🛒 Order Receipt</h2>

    <div>
        <p><strong>👤 Customer:</strong> <span id="customerName">N/A</span></p>
        <p><strong>📞 Phone:</strong> <span id="phone">N/A</span></p>
        <p><strong>🏠 Address:</strong> <span id="address">N/A</span></p>

        <h3>🛍 Cart Items</h3>
        <table id="cartTable">
            <thead>
                <tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr>
            </thead>
            <tbody id="cartTableBody"></tbody>
        </table>

        <h3>💳 Payment Details</h3>
        <table id="paymentSummary"></table>

        <h3>📢 Applied Discounts & Wallet</h3>
        <table id="walletSummary"></table>
    </div>

    <div>📌 Order will be delivered soon!</div>

    <script>
        function loadReceipt() {
            let cartData = sessionStorage.getItem("cartData") || "{}"; 
            try { cartData = JSON.parse(cartData); } catch (e) { cartData = {}; }

            const sessionData = {
                phone: sessionStorage.getItem("phone") || "N/A",
                flat: sessionStorage.getItem("flat") || "N/A",
                area: sessionStorage.getItem("area") || "N/A",
                landmark: sessionStorage.getItem("landmark") || "N/A",
                location: sessionStorage.getItem("location") || "N/A",
                originalPrice: sessionStorage.getItem("originalPrice") || "0",
                cartData: JSON.stringify(cartData),
                applied_coupon: sessionStorage.getItem("applied_coupon") || "None",
                applied_discount_percent: sessionStorage.getItem("applied_discount_percent") || "0",
                applied_discount_amount: sessionStorage.getItem("applied_discount_amount") || "0",
                existing_wallet_balance: sessionStorage.getItem("existing_wallet_balance") || "0",
                updated_wallet_balance: sessionStorage.getItem("updated_wallet_balance") || "0",
                total_amount: sessionStorage.getItem("total_amount") || "0"
            };

            fetch(window.location.href, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(sessionData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "success") {
                    document.getElementById("customerName").innerText = data.sessionData["Customer Name"];
                    document.getElementById("phone").innerText = data.sessionData["Phone"];
                    document.getElementById("address").innerText = `${data.sessionData["Flat"]}, ${data.sessionData["Area"]}, ${data.sessionData["Landmark"]}, ${data.sessionData["Location"]}`;

                    let cartTableBody = document.getElementById("cartTableBody");
                    cartTableBody.innerHTML = "";

                    let cartItems = data.sessionData["Cart Items"];
                    if (Array.isArray(cartItems) && cartItems.length > 0) {
                        cartItems.forEach(item => {
                            const row = `<tr>
                                <td>${item.name}</td>
                                <td>${item.quantity}</td>
                                <td>₹${item.price}</td>
                                <td>₹${item.total}</td>
                            </tr>`;
                            cartTableBody.innerHTML += row;
                        });
                    }

                    let paymentSummary = document.getElementById("paymentSummary");
                    paymentSummary.innerHTML = `
                        <tr><td>Subtotal:</td><td>₹${data.sessionData["Original Price"]}</td></tr>
                        ${data.sessionData["Applied Discount Amount"] !== "0" ? `<tr><td>Discount:</td><td>-₹${data.sessionData["Applied Discount Amount"]}</td></tr>` : ""}
                        <tr><td>Final Amount:</td><td>₹${data.sessionData["Final Amount"]}</td></tr>
                    `;

                    let walletSummary = document.getElementById("walletSummary");
                    walletSummary.innerHTML = `
                        ${data.sessionData["Existing Wallet Balance"] !== "0" ? `<tr><td>Existing Wallet Balance:</td><td>₹${data.sessionData["Existing Wallet Balance"]}</td></tr>` : ""}
                        ${data.sessionData["Updated Wallet Balance"] !== "0" ? `<tr><td>Updated Wallet Balance:</td><td>₹${data.sessionData["Updated Wallet Balance"]}</td></tr>` : ""}
                    `;
                }
            });
        }

        window.onload = loadReceipt;
    </script>
</body>
</html>
