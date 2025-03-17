<?php
session_start(); // Start session to access session variables
$conn = mysqli_connect('13.200.73.247', 'jerry', 'admin', 'hvh') or die('Connection could not be established');

// Fetch locations from the dashboard table
$locationQuery = "SELECT location FROM dashboard"; // Assuming your table has a column `location`
$result = mysqli_query($conn, $locationQuery);

$locations = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $locations[] = $row['location'];
    }
}
// Check if the form is submitted and store phone number in session
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phone'])) {
    $_SESSION['phone'] = $_POST['phone']; // Store phone number in session
}

$phone = isset($_SESSION['phone']) ? $_SESSION['phone'] : 'Not Provided'; // Get phone number from session


// Fetch wallet balance from users table based on phone number
$walletBalanceQuery = "SELECT wallet_balance FROM users WHERE phone = '$phone' LIMIT 1"; 
$walletBalanceResult = mysqli_query($conn, $walletBalanceQuery);

if ($walletBalanceResult && mysqli_num_rows($walletBalanceResult) > 0) {
    $wallet_balance = mysqli_fetch_assoc($walletBalanceResult)['wallet_balance'];
} else {
    $wallet_balance = 0; // Default to 0 if no wallet balance found
}

if (isset($_GET['cart'])) {
    $cartData = json_decode(urldecode($_GET['cart']), true);
} else {
    $cartData = [];
}
// Fetch fname, lname, and phone number from users table based on phone number
$userQuery = "SELECT fname, lname, phone FROM users WHERE phone = '$phone' LIMIT 1";
$userResult = mysqli_query($conn, $userQuery);
$userData = mysqli_fetch_assoc($userResult);

$firstName = isset($userData['fname']) ? $userData['fname'] : ''; // Check if fname exists
$lastName = isset($userData['lname']) ? $userData['lname'] : ''; // Check if lname exists

$fullName = trim($firstName . ' ' . $lastName); 

$phoneNumber = ($userData) ? $userData['phone'] : 'Not Provided';
// Fetch payment settings from the dashboard table
$paymentSettingsQuery = "SELECT cod_enabled, online_payment_enabled FROM dashboard LIMIT 1"; 
$paymentSettingsResult = mysqli_query($conn, $paymentSettingsQuery);
$paymentSettings = mysqli_fetch_assoc($paymentSettingsResult);

// Get COD and Online Payment status
$codEnabled = $paymentSettings['cod_enabled'] ?? 1; // Default to 1 (enabled) if not set
$onlinePaymentEnabled = $paymentSettings['online_payment_enabled'] ?? 1; // Default to 1 (enabled) if not set

$totalPrice = 0;
$originalPrice = 0;

// Calculate the total price and original price
foreach ($cartData as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
    $originalPrice += ($item['original_price'] ?? $item['price']) * $item['quantity'];
}
$uniqueItemCount = count($cartData); // Define this variable before using it

$savings = 0; // Default savings



// Fetch address data (flat_no, area, landmark, and location) based on the phone number
$addressQuery = "SELECT flat_no, area, landmark, location FROM users WHERE phone = '$phone' LIMIT 1";
$addressResult = mysqli_query($conn, $addressQuery);

// Initialize default values for the fields
$flatNo = $area = $landmark = $location = '';

if ($addressResult && mysqli_num_rows($addressResult) > 0) {
    $addressData = mysqli_fetch_assoc($addressResult);
    $flatNo = $addressData['flat_no'];
    $area = $addressData['area'];
    $landmark = $addressData['landmark'];
    $location = $addressData['location'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveAddress'])) {
    $newFlatNo = isset($_POST['flat']) ? mysqli_real_escape_string($conn, $_POST['flat']) : '';
    $newArea = isset($_POST['area']) ? mysqli_real_escape_string($conn, $_POST['area']) : '';
    $newLandmark = isset($_POST['landmark']) ? mysqli_real_escape_string($conn, $_POST['landmark']) : '';
    $newLocation = isset($_POST['location']) ? mysqli_real_escape_string($conn, $_POST['location']) : '';

    $updateQuery = "UPDATE users SET flat_no = '$newFlatNo', area = '$newArea', landmark = '$newLandmark', location = '$newLocation' WHERE phone = '$phone'";

    if (mysqli_query($conn, $updateQuery)) {
        
    } else {
        echo "Error updating address: " . mysqli_error($conn);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f5f5f5;
        }
        .app-container {
            width: 100%;
            max-width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: #ff5722;
            color: white;
            padding: 16px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="app-container">
    <div class="header">Checkout</div>

    <div class="p-6">
        <div class="flex justify-between font-semibold text-lg">
            <span>Wallet Balance</span>
            <span id="walletBalance">₹<?= number_format($wallet_balance, 2) ?></span>
        </div>

        <div class="mt-4">
            <?php if ($uniqueItemCount > 3): ?>
                <!-- Show dropdown button if unique items are more than 3 -->
                <button class="toggle-btn w-full bg-gray-300 text-black py-2 rounded-lg hover:bg-gray-400" data-target="cartItems">View Cart Items</button>
                <div id="cartItems" class="dropdown-content hidden mt-2 border p-3 rounded-lg bg-gray-50">
            <?php else: ?>
                <!-- Show cart items directly if 3 or fewer unique items -->
                <div id="cartItems" class="mt-2 border p-3 rounded-lg bg-gray-50">
            <?php endif; ?>

                <?php foreach ($cartData as $item): ?>
                    <div class="border-b p-2 flex justify-between">
                        <div>
                            <h3 class="font-medium"><?= htmlspecialchars($item['name']) ?></h3>
                            <p class="text-gray-500">₹<?= $item['price'] ?> x <?= $item['quantity'] ?></p>
                        </div>
                        <p class="font-semibold">₹<?= $item['price'] * $item['quantity'] ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="mt-4 p-3 border-t">
            <div class="flex justify-between font-semibold text-lg">
                <span>Total</span>
                <span id="totalAmount">₹<?= number_format($totalPrice, 2) ?></span>
            </div>
            <?php if ($savings > 0): ?>
                <div class="flex justify-between text-sm text-green-500">
                    <span>Discount Applied</span>
                    <span>₹<?= number_format($savings, 2) ?> Off</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex items-center mt-4 space-x-2">
            <input type="text" id="couponCode" class="flex-1 border rounded-lg p-2" placeholder="Enter coupon">
            <button class="toggle-btn bg-gray-300 text-black px-3 py-2 rounded-lg hover:bg-gray-400" data-target="couponList">View Discounts</button>
        </div>

        <div id="couponList" class="dropdown-content hidden mt-2 border p-3 rounded-lg bg-gray-50">
            <p class="cursor-pointer hover:bg-gray-200 p-2 rounded" onclick="applyDiscount('SAVE10')">
                <strong>SAVE10</strong> - Get 10% off on orders above ₹500
            </p>
            <p class="cursor-pointer hover:bg-gray-200 p-2 rounded" onclick="applyDiscount('FREESHIP')">
                <strong>FREESHIP</strong> - Free shipping on orders above ₹300
            </p>
        </div>

        <button class="w-full bg-blue-500 text-white py-2 mt-2 rounded-lg hover:bg-blue-600" onclick="applyCoupon()">Apply</button>

        <button class="toggle-btn w-full bg-green-500 text-white py-2 mt-4 rounded-lg hover:bg-green-600" data-target="addressForm">Add Delivery Information</button>
        <div id="addressForm" class="dropdown-content hidden mt-4 p-4 border bg-gray-50 rounded-lg">
            <h3 class="font-semibold">Enter Delivery Information</h3>

            <form method="POST">
                <input type="text" id="name" class="w-full border rounded-lg p-2 mt-2" placeholder="Full Name" value="<?= $fullName ?>" readonly>
                <input type="text" id="phone" class="w-full border rounded-lg p-2 mt-2" placeholder="Phone" value="<?= $phoneNumber ?>" readonly>
                <input type="text" id="flat" name="flat" class="w-full border rounded-lg p-2 mt-2" placeholder="Flat/Building" value="<?= htmlspecialchars($flatNo) ?>">
                <input type="text" id="area" name="area" class="w-full border rounded-lg p-2 mt-2" placeholder="Area" value="<?= htmlspecialchars($area) ?>">
                <input type="text" id="landmark" name="landmark" class="w-full border rounded-lg p-2 mt-2" placeholder="Landmark" value="<?= htmlspecialchars($landmark) ?>">
                <select id="location" name="location" class="w-full border rounded-lg p-2 mt-2">
                    <option value="">Select Location</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= htmlspecialchars($loc) ?>" <?= $location == $loc ? 'selected' : '' ?>><?= htmlspecialchars($loc) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="saveAddress" class="w-full bg-blue-500 text-white py-2 mt-4 rounded-lg hover:bg-blue-600">Save Address</button>
            </form>
        </div>


        <div class="mt-4">
            <h3 class="font-semibold">Payment Method</h3>
            <div class="flex items-center mt-2">
                <?php if ($codEnabled): ?>
                    <input type="radio" id="cod" name="paymentMethod" value="COD" checked>
                    <label for="cod">Cash on Delivery</label>
                <?php else: ?>
                    <p>Cash on Delivery is disabled.</p>
                <?php endif; ?>
            </div>
            <div class="flex items-center mt-2">
                <?php if ($onlinePaymentEnabled): ?>
                    <input type="radio" id="online" name="paymentMethod" value="Online">
                    <label for="online">Online Payment</label>
                <?php else: ?>
                    <p>Online Payment is disabled.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4">
            <h3 class="font-semibold">Order Notes</h3>
            <textarea id="orderNotes" class="w-full border rounded-lg p-2 mt-2" placeholder="Any special instructions?"></textarea>
        </div>

        <button id="placeOrder" class="w-full bg-red-500 text-white py-3 mt-4 rounded-lg hover:bg-red-600">Place Order</button>
    </div>
</div>

<script>
$(document).ready(function () {
    $(".toggle-btn").click(function () {
        let target = $(this).data("target");

        // Close all dropdowns except the one being toggled
        $(".dropdown-content").not("#" + target).slideUp();

        // Toggle the clicked dropdown
        $("#" + target).slideToggle();
    });

    $("#saveAddress").click(function () {
    let address = $("#name").val();
    if (address.trim() !== "") {
        console.log("Address: " + address); // Log the address for verification
        alert("Address Saved: " + address);
        $("#addressForm").slideUp();
    } else {
        alert("Please enter an address.");
    }
});

    // Function to select a coupon and display it in the textbox
    $(".dropdown-content p").click(function () {
        let selectedCoupon = $(this).find("strong").text(); // Extracts coupon code
        $("#couponCode").val(selectedCoupon);
        $("#couponList").slideUp(); // Hide coupon list after selection
    });
});


function applyCoupon() {
    var couponCode = $("#couponCode").val().toUpperCase().trim();
    var totalPrice = <?= $totalPrice ?>; // Original total price from PHP
    var originalPrice = <?= $originalPrice ?>; // Original price from PHP
    var wallet_balance = <?= $wallet_balance ?>; // Original wallet balance from PHP

    // Check if the coupon is valid
    if (couponCode === 'SAVE10') {
        // Apply 10% discount without checking for a minimum cart value
        var discount = totalPrice * 0.10; // 10% discount
        var discountedPrice = totalPrice - discount;

        // Deduct the discount from wallet balance
        if (wallet_balance >= discount) {
            wallet_balance -= discount; // Deduct the discount from the wallet
            totalPrice = discountedPrice; // Update the total price after discount
            $('#walletBalance').text('₹' + wallet_balance.toFixed(2)); // Update wallet balance
            $('#totalAmount').text('₹' + totalPrice.toFixed(2)); // Update total amount
            alert('Coupon applied: 10% off and ₹' + discount.toFixed(2) + ' deducted from wallet balance');

            // Save updated total and wallet balance to local storage
            localStorage.setItem('originalPrice', originalPrice.toFixed(2));
            //localStorage.setItem('wallet_balance', totalPrice.toFixed(2));
            localStorage.setItem('updated_wallet_balance', wallet_balance.toFixed(2));
            localStorage.setItem('updated_total', totalPrice.toFixed(2));
        } else {
            alert('Insufficient wallet balance to apply the discount');
        }
    } else {
        alert('Invalid coupon code');
    }
}


</script>

</body>
</html>
