<?php
session_start();
include 'config.php';

$error_message = ""; // Variable to hold error or success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];

    // Validate phone number (must be exactly 10 digits and not a repeating sequence)
    if (!preg_match("/^\d{10}$/", $phone)) {
        $error_message = "Phone number must be exactly 10 digits.";
    }

    // Check for repeating digits like 1111111111, 2222222222, etc.
    if (preg_match("/^(\d)\1{9}$/", $phone)) {
        $error_message = "Phone number cannot contain repeating digits.";
    }

    // Validate password (at least 1 special character, 1 number, min 6 characters)
    if (!preg_match("/^(?=.*[0-9])(?=.*[\W_]).{6,}$/", $password)) {
        $error_message = "Password must contain at least one special character, one number, and be at least 6 characters long.";
    }

    // Check if phone number already exists in the database
    $check_phone = "SELECT * FROM users WHERE phone = '$phone'";
    $result_phone = mysqli_query($conn, $check_phone);
    if (mysqli_num_rows($result_phone) > 0) {
        $error_message = "Phone number already registered. Please log in.";
    }

    // Check if email already exists in the database
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result_email = mysqli_query($conn, $check_email);
    if (mysqli_num_rows($result_email) > 0) {
        $error_message = "Email already registered. Please log in.";
    }

    // Check if fname and lname already exist in the database
    $check_user = "SELECT * FROM users WHERE fname = '$fname' AND lname = '$lname'";
    $result_user = mysqli_query($conn, $check_user);
    if (mysqli_num_rows($result_user) > 0) {
        $error_message = "Welcome back $fname! Please log in with your phone and password.";
    }

    // If no error, proceed with registration
    if (empty($error_message)) {
        // Hash the password before storing
        $hashed_password = $password;  // Instead of: $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate username (fname + last 4 digits of phone)
        $username = $fname . substr($phone, -4);

        // Set the time zone to Indian Standard Time (IST)
        date_default_timezone_set('Asia/Kolkata');
        $reg_time = date('Y-m-d h:i:s');  // 12-hour format with AM/PM
        $reg_date = date('Y-m-d');           // Date in YYYY-MM-DD format

        // Insert into database
        $query = "INSERT INTO users (fname, lname, email, password, phone, reg_time, reg_date, username) 
                  VALUES ('$fname', '$lname', '$email', '$hashed_password', '$phone', '$reg_time', '$reg_date', '$username')";

        if (mysqli_query($conn, $query)) {
            // Redirect to home.php after successful registration
            $_SESSION['phone'] = $phone;
            header("Location: ../home.php");
            exit();
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
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
    <title>Register</title>
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

    <div class="background-atropos">
        <div class="star" style="animation-delay: 0s; top: 20%; left: 15%;"></div>
        <div class="star" style="animation-delay: 1s; top: 40%; left: 55%;"></div>
        <div class="star" style="animation-delay: 2s; top: 70%; left: 75%;"></div>
    </div>

    <div class="container">
        <h2>Sign Up</h2>

        <form method="POST">
            <input type="text" name="fname" placeholder="Enter Your First Name" required>
            <input type="text" name="lname" placeholder="Enter Your Last Name" required>
            <input type="email" name="email" placeholder="Enter Your Email Address" required>
            <input type="text" name="phone" placeholder="Enter A Valid Phone Number" maxlength="10" required>
            <input type="password" name="password" placeholder="Password (1 special character & 1 number)" required>
            <button type="submit">Register</button>
        </form>

        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Log In</a></p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </div>
    <footer>
        © 2025 FoodJI | All Rights Reserved
    </footer>
</body>
</html>
