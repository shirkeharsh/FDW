<?php
session_start(); // Start the session

include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Validate phone number (must be exactly 10 digits and not a repeating sequence)
    if (!preg_match("/^\d{10}$/", $phone)) {
        $error_message = "Phone number must be exactly 10 digits.";
    }

    // Check for repeating digits like 1111111111, 2222222222, etc.
    if (preg_match("/^(\d)\1{9}$/", $phone)) {
        echo "Phone number cannot contain repeating digits.";
        exit;
    }

    // Query to check if the phone and password match a record in the database
    $query = "SELECT * FROM users WHERE phone = '$phone' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // Login successful, store phone in session and redirect
        $_SESSION['phone'] = $phone; // Store the phone number in session
        header("Location: /HVH/home.php");  // Redirect to the index.php page
        exit;
    } else {
        $error_message = "Invalid phone number or password.";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Atropos CSS -->
    <link href="https://cdn.jsdelivr.net/npm/atropos@5.0.0/dist/atropos.min.css" rel="stylesheet">
</head>
<style>
    body {
    background-color:#000000;
    font-family: Arial, sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    position: relative;
}

/* Background Circles */
body::before {
    content: "";
    position: absolute;
    top: -50px;
    left: -30px;
    width: 150px;
    height: 150px;
    background-color: #FF9900;
    border-radius: 50%;
}

body::after {
    content: "";
    position: absolute;
    top: 60px;
    right: 30px;
    width: 100px;
    height: 100px;
    background-color: #FF9900;
    border-radius: 50%;
}

.container {
    background-color: #0E0E0E ;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(255, 153, 0, 0.8);
    text-align: center;
    width: 300px;
    position: relative;
}

h2 {
    color: #FF9900;
    margin-bottom: 20px;
}

input {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: none;
    border-radius: 5px;
    background-color: #222;
    color: #fff;
    font-size: 16px;
}

button {
    background-color: #FF9900;
    color: #000;
    padding: 10px;
    border: none;
    width: 100%;
    cursor: pointer;
    border-radius: 5px;
    font-weight: bold;
    font-size: 16px;
}

button:hover {
    background-color: #e68a00;
}

.form-footer {
    margin-top: 10px;
}

.form-footer a {
    color: #FF9900;
    text-decoration: none;
    font-weight: bold;
}
body, .container {
    background-color: #0E0E0E !important; /* Force uniform black */
}

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
</style>
<body>

    <!-- Background with moving, shining stars -->
    <div class="background-atropos" data-atropos-offset="2" data-atropos-rotation="10">
        <!-- Three small moving stars -->
        <div class="star" style="animation-delay: 0s; top: 20%; left: 15%;"></div>
        <div class="star" style="animation-delay: 1s; top: 40%; left: 55%;"></div>
        <div class="star" style="animation-delay: 2s; top: 70%; left: 75%;"></div>
    </div>

    <div class="container">
        <h2>Log In</h2>

        <form method="POST">
            <input type="text" name="phone" placeholder="Phone (10 digits)" maxlength="10" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Log In</button>
        </form>

        <div class="form-footer">
            <p>Don't have an account? <a href="register.php">Sign Up</a></p>
        </div>

        <?php
        if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>
    </div>

    <!-- Atropos JS -->
    <script src="https://cdn.jsdelivr.net/npm/atropos@5.0.0/dist/atropos.min.js"></script>
    <script>
        window.onload = function() {
            Atropos.init();  // Initialize Atropos after the window is loaded
        };
    </script>
 <footer>
        © 2025 FoodJI | All Rights Reserved
    </footer>

</body>
</html>
