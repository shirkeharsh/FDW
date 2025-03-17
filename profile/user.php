<?php
session_start();

// Database connection
$host = '13.200.73.247';  // Database host
$db = 'hvh';  // Database name
$user = 'jerry';  // Database username
$pass = 'admin';  // Database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['phone'])) {
    echo "Please log in first.";
    exit;
}

// Get the phone from session
$phone = $_SESSION['phone'];

// Fetch user details
$sql = "SELECT * FROM users WHERE phone = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update user details
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];

    // Initialize password update variables
    $new_password = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Password change logic
    if (!empty($new_password) && $new_password === $confirm_password) {
        // If passwords match, no hashing, just store the plain text password
        $update_sql = "UPDATE users SET fname = ?, lname = ?, email = ?, password = ? WHERE phone = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssss", $fname, $lname, $email, $new_password, $phone);

        if ($update_stmt->execute()) {
            echo "Profile updated successfully!";
            // Refresh the user details
            $user['fname'] = $fname;
            $user['lname'] = $lname;
            $user['email'] = $email;
        } else {
            echo "Error updating profile: " . $conn->error;
        }
    } elseif (!empty($new_password) || !empty($confirm_password)) {
        echo "Passwords do not match or are empty.";
    } else {
        // If no password is provided, just update other fields
        $update_sql = "UPDATE users SET fname = ?, lname = ?, email = ? WHERE phone = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssss", $fname, $lname, $email, $phone);

        if ($update_stmt->execute()) {
            echo "Profile updated successfully!";
            // Refresh the user details
            $user['fname'] = $fname;
            $user['lname'] = $lname;
            $user['email'] = $email;
        } else {
            echo "Error updating profile: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            background-color: #1c1c1c; /* Dark background */
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #FF9800; /* Yellow header */
            color: black;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 30px;
        }

        .container {
            flex: 1;
        }

        .form-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 10px;
            background-color: #333333; /* Dark background for form */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-group label {
            color: #fff;
        }

        .form-control {
            background-color: #555;
            color: #fff;
            border: 1px solid #777;
        }

        .form-control:focus {
            border-color: #ffcc00; /* Highlighted border on focus */
            background-color: #444;
        }

        .eye-icon {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 10px;
            color: #fff;
        }

        .footer {
            background-color: #FF9800; /* Yellow footer */
            color: black;
            padding: 4px 0;
            text-align: center;
            margin-top: auto;
            font-size: 12px;
        }

        header h1 {
            font-size: 24px;
            margin: 0;
        }
        h2 {
            color: #FF9800; /* Yellow heading */
        }
        .btn-primary {
            background-color: #FF9800; /* Yellow button */
            border-color: #ffcc00;
        }

        .btn-primary:hover {
            background-color: #e6b800; /* Darker yellow on hover */
            border-color: #e6b800;
        }
    </style>
</head>
<body>

<header>
    <h1>Profile</h1>
</header>

<div class="container">
    <div class="card form-container">
        <h2>Edit Your Profile</h2>
        <form method="POST">
            <div class="form-group">
                <label for="fname">First Name:</label>
                <input type="text" class="form-control" id="fname" name="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required>
            </div>

            <div class="form-group">
                <label for="lname">Last Name:</label>
                <input type="text" class="form-control" id="lname" name="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="new_password">New Password:</label>
                <div style="position: relative;">
                    <input type="password" class="form-control" id="new_password" name="new_password">
                    <i class="eye-icon" id="togglePassword" onclick="togglePassword()">👁️</i>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <div style="position: relative;">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    <i class="eye-icon" id="toggleConfirmPassword" onclick="toggleConfirmPassword()">👁️</i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
        </form>
    </div>
</div>

<div class="footer">
    <p>&copy; 2025 FOODJI. All rights reserved.</p>
</div>

<script>
    // Function to toggle the visibility of the password
    function togglePassword() {
        var passwordField = document.getElementById("new_password");
        var icon = document.getElementById("togglePassword");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.textContent = "🙈"; // Change icon to show hidden
        } else {
            passwordField.type = "password";
            icon.textContent = "👁️"; // Change icon to show visible
        }
    }

    // Function to toggle the visibility of the confirm password
    function toggleConfirmPassword() {
        var passwordField = document.getElementById("confirm_password");
        var icon = document.getElementById("toggleConfirmPassword");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.textContent = "🙈"; // Change icon to show hidden
        } else {
            passwordField.type = "password";
            icon.textContent = "👁️"; // Change icon to show visible
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
