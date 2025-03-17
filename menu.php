<?php
session_start(); // Start session to access session variables
include('db/config.php'); // Database connection

// Fetch website title from database
$title = "FOODJI"; // Default title
$titleQuery = "SELECT title FROM website LIMIT 1";
$titleResult = mysqli_query($conn, $titleQuery);

if ($titleResult && mysqli_num_rows($titleResult) > 0) {
    $row = mysqli_fetch_assoc($titleResult);
    $title = htmlspecialchars($row['title']);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['phone'])) {
    $_SESSION['phone'] = $_POST['phone']; // Store the phone number in the session
}

if (isset($_SESSION['phone'])) {
    $phone = $_SESSION['phone']; // Get phone number from session
    echo "Welcome! Your phone number is: " . htmlspecialchars($phone);
} else {
    echo "You are not logged in.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/checkout.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@shadcn/ui@latest/dist/index.css">

    <script src="scripts/cart.js"></script>
  
    <script src="scripts/main.js"></script>
    <script src="scripts/checkout.js"></script>
    
</head>
<body>
<script>
    var isLoggedIn = <?php echo isset($_SESSION['phone']) ? 'true' : 'false'; ?>;
</script>

<!-- Header -->
<header class="bg-dark text-white py-3 shadow">
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="h3"><?php echo $title; ?></h1>
        <button id="cart-toggle-btn" class="btn btn-warning">
            🛒 Cart <span id="cart-count" class="badge bg-danger">0</span>
 </button>
 
    </div>
</header>
<div class="mb-3">
    <input type="text" id="search-box" class="form-control" placeholder="Search items...">
</div>

<div id="cart-sidebar" class="position-fixed top-0 end-0 bg-light p-4 shadow-lg">
    <div class="d-flex justify-content-between align-items-center">
        <h4>🛍️ Your Cart</h4>
        <button id="close-cart-btn" class="btn btn-sm btn-danger">X</button>
    </div>
    <div id="cart-items"></div>
    <hr>
    <h5>Total: ₹<span id="cart-total">0</span></h5>
    <button id="checkout-btn" class="btn btn-success w-100">Checkout</button>
</div>


<!-- Main Content -->
<div class="container my-4">
    <?php
    $productQuery = "SELECT * FROM product ORDER BY category, item_name";
    $productResult = mysqli_query($conn, $productQuery);
    $categories = [];

    while ($row = mysqli_fetch_assoc($productResult)) {
        $categoryName = htmlspecialchars($row['category']);
        if (!isset($categories[$categoryName])) {
            $categories[$categoryName] = [];
        }
        $categories[$categoryName][] = $row;
    }

    if (!empty($categories)) {
        foreach ($categories as $categoryName => $products) {
            echo '<div class="category-section">
                    <button class="category-toggle">' . $categoryName . '</button>
                    <div class="product-container" style="display: none;">';
                    foreach ($products as $row) {
                        echo '<div class="product-card">
                                <div class="product-info">
                                    <h3 class="item-name">' . htmlspecialchars($row['item_name']) . '</h3>
                                    <p class="item-description">' . htmlspecialchars($row['item_description']) . '</p>
                                    <p class="item-price">
                                        <span class="item-pricecut" style="text-decoration: line-through; color: red; margin-right: 10px;">
                                            ₹' . htmlspecialchars($row['item_pricecut']) . '
                                        </span>
                                        ₹' . htmlspecialchars($row['item_price']) . '
                                    </p>
                                </div>
                                <div class="product-right">
                                    <div class="product-image">
                                        <img src="images/' . htmlspecialchars($row['item_img']) . '" alt="' . htmlspecialchars($row['item_name']) . '">
                                    </div>
                                    <div class="quantity-container" style="display: none;">
                                        <button class="quantity-btn minus">-</button>
                                        <span class="quantity-display">1</span>
                                        <button class="quantity-btn plus">+</button>
                                    </div>
                                    <button class="add-to-cart-btn" 
                                        data-name="' . htmlspecialchars($row['item_name']) . '" 
                                        data-price="' . htmlspecialchars($row['item_price']) . '">
                                        Add Item
                                    </button>
                                </div>
                            </div>';
                    }
                    echo '</div></div>';
        }
    } else {
        echo '<p>No products found.</p>';
    }
    ?>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3 mt-4">
    <p>&copy; <?php echo date("Y"); ?> <?php echo $title; ?>. All rights reserved.</p>
</footer>


<script>
    window.onload = function () {
        localStorage.clear();
    };
</script>
</body>
</html>
