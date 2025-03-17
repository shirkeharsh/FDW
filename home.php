<?php
session_start(); // Start session to access session variables

// Check if the user is logged in
if (isset($_SESSION['phone'])) {
    $phone = $_SESSION['phone']; // Get phone number from session
    echo "v2.0.1";

    // Database connection
    $conn = mysqli_connect('13.200.73.247', 'jerry', 'admin', 'hvh') or die('Connection could not be established');

    // Fetch user details from 'users' table
    $query = "SELECT fname, lname, phone, wallet_balance FROM users WHERE phone = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Store user details and wallet balance in session
        $_SESSION['user_details'] = [
            'fname' => $row['fname'],
            'lname' => $row['lname'],
            'phone' => $row['phone'],
            'wallet_balance' => $row['wallet_balance']
        ];

        // Remove separate wallet balance session variables
        unset($_SESSION['existing_wallet_balance']);
        unset($_SESSION['updated_wallet_balance']);
    }

    mysqli_close($conn);
} else {
    echo "You are not logged in.";
}

// Database connection to fetch website details
$conn = mysqli_connect('13.200.73.247', 'jerry', 'admin', 'hvh') or die('Connection could not be established');

// Fetch title and trending items from the 'website' table
$query = "SELECT title, trending FROM website";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$title = '';  
$trending_items = [];  

while ($row = mysqli_fetch_assoc($result)) {
    if (!$title) {
        $title = $row['title'];  
    }
    $trending_items[] = $row['trending'];
}

mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - Trending</title>

    <!-- Bulma CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">


    <link rel="stylesheet" href="cs/home.css">
<style> 
:root {
    --primary-color: #ff9800;
    --secondary-color: #222;
    --background-color: #1A1A1A;
    --text-color: #fff;
}

/* Global Styling */
body {
    background-color: var(--background-color);
    color: var(--text-color);
    font-family: Arial, sans-serif;
    overflow: hidden;
}

/* Section Styling */
.section {
    padding: 20px;
}

/* Title */
.title {
    color: var(--primary-color);
    text-transform: uppercase;
    font-weight: bold;
}

/* Divider */
.divider {
    border: 1px solid var(--primary-color);
    margin: 20px 0;
}

/* Account Buttons */
.buttons-container {
    text-align: center;
}

.custom-btn {
    background: var(--primary-color);
    color: black;
    border: none;
    padding: 10px 20px;
    margin: 5px;
    cursor: pointer;
    font-weight: bold;
    border-radius: 5px;
}

.custom-btn:hover {
    background: white;
    color: black;
}

/* Dropdown Menu */
.dropdown-container {
    display: none;
    text-align: center;
    background: var(--secondary-color);
    padding: 10px;
    border-radius: 10px;
}

.dropdown-buttons button {
    display: block;
    width: 80%;
    margin: 10px auto;
}

/* User Info Card */
.user-info-card {
    background: var(--primary-color);
    padding: 15px;
    border-radius: 10px;
    text-align: center;
    width: 80%;
    margin: 10px auto;
}

/* User Title */
.user-title {
    color: #000;
    font-weight: bold;
    font-size: 15px;
    font-family: 'Poppins', sans-serif;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* User Details */
.user-detail {
    margin: 8px 0;
    font-size: 13px;
    font-family: 'Poppins', sans-serif;
    color: #3E2723;
    font-weight: 500;
}

/* Order Links */
.food-order-container, .your-orders-container {
    text-align: center;
    margin-top: 20px;
}

.order-link {
    background: var(--primary-color);
    padding: 10px;
    display: inline-block;
    border-radius: 5px;
    color: black;
    font-weight: bold;
    text-decoration: none;
}

.order-link:hover {
    background: white;
    color: black;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .item {
        padding: 8px;
    }
}

#imageCarousel {
    max-width: 400px;
    margin: auto;
}

.carousel-img {
    width: 100%;
    max-width: 400px;
    height: 100px;
    object-fit: cover;
    border-radius: 10px;
}

@media (max-width: 576px) {
    #imageCarousel {
        max-width: 100%;
    }
    .carousel-img {
        height: 180px;
    }
}

/* Navbar Styling */
.navbar {
    background-color: var(--background-color);
    padding: 5px 15px;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

.navbar-brand img {
    height: 50px;
}

.navbar-nav .nav-link {
    color: var(--text-color);
    font-weight: bold;
    transition: 0.3s;
}

.navbar-nav .nav-link:hover {
    color: var(--primary-color);
}

/* User Icon Styling */
.user-icon {
    font-size: 26px;
    cursor: pointer;
    color: var(--text-color);
    transition: transform 0.2s ease-in-out;
}

.user-icon:hover {
    transform: scale(1.2);
    color: var(--primary-color);
}

/* Dropdown Menu */
.dropdown-menu {
    background-color: var(--secondary-color);
    border: none;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

.dropdown-item {
    color: var(--text-color);
    transition: background 0.3s;
}

.dropdown-item:hover {
    background-color: var(--primary-color);
    color: #000;
}

/* Footer */
footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 15px;
    text-align: center;
    background: #0E0E0E;
    font-size: 10px;
}

/* Scrollable Container */
.scrollable-container {
    max-height: 80vh;
    overflow-y: auto;
    padding: 10px;
}
.food-order-container,
.your-orders-container {
    margin-bottom: 5px; /* Adjust as needed */
    padding: 0; /* Remove unnecessary padding */
}

.food-order-container + .your-orders-container {
    margin-top: 0; /* Remove any top margin */
}


.login-btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    color: #fff;
    background-color: #28a745; /* Green color */
    border-radius: 5px;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.login-btn:hover {
    background-color: #218838; /* Darker green */
    transform: scale(1.05);
}

.login-btn:active {
    transform: scale(0.98);
}


</style>
    
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <!-- Left Side: Logo -->
        <a class="navbar-brand" href="#">
            <img src="images/icon.png" alt="Logo">
        </a>

        <!-- Center Menu Links -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                
            </ul>
        </div>

        <!-- Right Side: User Icon with Dropdown -->
        <div class="dropdown">
            <span class="user-icon" id="userDropdown" data-bs-toggle="dropdown">👤</span>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="profile/user.php">Profile</a></li>
                <li><a class="dropdown-item" href="checkout/orders.php">Your Orders</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>


    <hr class="divider">
  
    <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://www.tastingtable.com/img/gallery/what-makes-restaurant-burgers-taste-different-from-homemade-burgers-upgrade/intro-1662064407.webp" class="d-block w-100 rounded" alt="Image 1">
        </div>
        <div class="carousel-item">
            <img src="https://www.tastingtable.com/img/gallery/what-makes-restaurant-burgers-taste-different-from-homemade-burgers-upgrade/intro-1662064407.webp" class="d-block w-100 rounded" alt="Image 2">
        </div>
        <div class="carousel-item">
            <img src="https://www.tastingtable.com/img/gallery/what-makes-restaurant-burgers-taste-different-from-homemade-burgers-upgrade/intro-1662064407.webp" class="d-block w-100 rounded" alt="Image 3">
        </div>
        <div class="carousel-item">
            <img src="https://www.tastingtable.com/img/gallery/what-makes-restaurant-burgers-taste-different-from-homemade-burgers-upgrade/intro-1662064407.webp" class="d-block w-100 rounded" alt="Image 4">
        </div>
    </div>
</div>


    <hr class="divider">

    <div class="scrollable-container">
    <div class="user-info-card">
        <div class="card-content">
            <?php
            if (isset($_SESSION['phone'])) {
                // Fetch user details from session
                $user_details = $_SESSION['user_details'];
                echo "<p class='user-title'>Welcome, " . htmlspecialchars($user_details['fname'] . " " . $user_details['lname']) . "</p>";
                echo "<p class='user-detail'><strong class='phone-title'>Phone:</strong> <span class='phone-color'>" . htmlspecialchars($user_details['phone']) . "</span></p>";
                echo "<p class='user-detail'><strong class='wallet-title'>Wallet Balance:</strong> ₹" . number_format($user_details['wallet_balance'], 2) . "</p>";
                
            
                // Store wallet balance in JavaScript sessionStorage
              

                // Fetch coupon details from 'coupons' table
                $phone = $user_details['phone'];
                $conn = mysqli_connect('13.200.73.247', 'jerry', 'admin', 'hvh') or die('Connection could not be established');

                $couponQuery = "SELECT couponCode, discount FROM coupons WHERE phone = ?";
                $couponStmt = mysqli_prepare($conn, $couponQuery);
                mysqli_stmt_bind_param($couponStmt, "s", $phone);
                mysqli_stmt_execute($couponStmt);
                $couponResult = mysqli_stmt_get_result($couponStmt);

                if ($couponRow = mysqli_fetch_assoc($couponResult)) {
                    echo "<p class='user-detail'><strong class='coupon-title'>Coupon:</strong> " . htmlspecialchars($couponRow['couponCode']) . " - " . htmlspecialchars($couponRow['discount']) . "% discount</p>";
                }

                mysqli_close($conn);
            } else {
                echo "<p class='user-title'>Login to View Details</p>";
                echo "<a href='login/login.php' class='button login-btn'>Login</a>";
            }
            ?>
        </div>
    </div>
    <!-- New Container for Food Order -->
<div class="food-order-container">
    <div class="food-order-card">
        <div class="card-content">
            <a href="menu.php" class="order-link">
                <p class="order-text">Place your order here</p>
            </a>
        </div>
    </div>
</div>
<div class="your-orders-container">
    <div class="your-orders-card">
        <div class="card-content">
            <a href="checkout/orders.php" class="order-link">
                <p class="order-text">📦 View Your Orders</p>
            </a>
        </div>
    </div>
</div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
    // Fetch wallet balance from PHP session
    let walletBalance = "<?php echo isset($_SESSION['user_details']) ? $_SESSION['user_details']['wallet_balance'] : ''; ?>";

    // Store wallet balance in sessionStorage
    sessionStorage.setItem('wallet_balance', walletBalance);

    console.log(walletBalance); // Check the value stored in sessionStorage

    let phpPhone = "<?php echo isset($_SESSION['phone']) ? $_SESSION['phone'] : ''; ?>";

    if (phpPhone) {
        sessionStorage.setItem("phone", phpPhone);
    }

    let phone = sessionStorage.getItem("phone");
    let restrictedButtons = document.querySelectorAll(".restricted-btn");

    if (!phone) {
        restrictedButtons.forEach(button => {
            button.disabled = true;
            button.classList.add("is-disabled");
        });
    }

    document.getElementById("logoutBtn").addEventListener("click", function() {
        sessionStorage.removeItem("phone"); // Clear sessionStorage on logout
        sessionStorage.removeItem("wallet_balance"); // Clear wallet balance
        window.location.href = "logout.php"; // Redirect to logout handler
    });

    // Toggle Dropdown Animation
    document.getElementById("toggleDropdown").addEventListener("click", function() {
        document.getElementById("dropdownMenu").classList.toggle("show");
    });
});

    </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Enable Swipe on Mobile -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const myCarousel = document.querySelector("#imageCarousel");
    const carousel = new bootstrap.Carousel(myCarousel, { interval: 2000 });

    let touchStartX = 0;
    let touchEndX = 0;

    myCarousel.addEventListener("touchstart", (e) => {
        touchStartX = e.changedTouches[0].screenX;
    });

    myCarousel.addEventListener("touchend", (e) => {
        touchEndX = e.changedTouches[0].screenX;
        if (touchEndX < touchStartX) {
            carousel.next();
        } else if (touchEndX > touchStartX) {
            carousel.prev();
        }
    });
});
</script>
<footer style="text-align: center; padding: 10px; background: #222; color: white;">
    <a href="#" style="margin: 0 15px; font-size: 20px; color: #ffffff; text-decoration: none;">
        <i class="fa fa-home"></i>
    </a>
    <a href="menu.php" style="margin: 0 15px; font-size: 20px; color: #FF9800; text-decoration: none;">
        <i class="fa fa-utensils"></i>
    </a>
    <a href="https://www.instagram.com" target="_blank" style="margin: 0 15px; font-size: 20px; color: #C13584; text-decoration: none;">
        <i class="fab fa-instagram"></i>
    </a>
    <a href="https://wa.me/yourwhatsappnumber" target="_blank" style="margin: 0 15px; font-size: 20px; color: #25D366; text-decoration: none;">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="#" style="margin: 0 15px; font-size: 20px; color: #00AEEF; text-decoration: none;">
        <i class="fa fa-question-circle"></i>
    </a>
</footer>




</body>
</html>
