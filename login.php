<?php
// Enable error reporting to display any PHP errors or warnings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Initialize the $client variable to null
$client = null;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Replace these with your actual MongoDB credentials
    $connectionString = "mongodb+srv://shariqawan0:awanawan123@cluster0.mongodb.net/daily_check_log_db";
    
    try {
        // Create a MongoDB client
        $client = new MongoDB\Driver\Manager($connectionString);

        // Specify the database and collection
        $db = "daily_check_log_db";
        $collection = "users";

        // Get the submitted username and password
        $submittedUsername = $_POST['username'];
        $submittedPassword = $_POST['password'];

        // Prepare the MongoDB query
        $filter = ['username' => $submittedUsername];
        $options = [];
        $query = new MongoDB\Driver\Query($filter, $options);

        // Execute the query
        $result = $client->executeQuery("$db.$collection", $query);

        // Check if a user with the submitted username exists in the database
        if (count($result->toArray()) === 1) {
            $user = current($result->toArray());
            $storedPassword = $user->password;

            // Verify the submitted password against the stored password
            if (password_verify($submittedPassword, $storedPassword)) {
                // If the credentials are correct, store the username and full name in the session and redirect
                $_SESSION['username'] = $submittedUsername;
                $_SESSION['full_name'] = $user->full_name; // Set the full name in the session
                $_SESSION['role'] = 'admin';

                header("Location: daily_check_log.php");
                exit();
            } else {
                // If the credentials are incorrect, set the error message
                $_SESSION['error_message'] = "Invalid password. Please try again.";
            }
        } else {
            // If the user does not exist, set the error message
            $_SESSION['error_message'] = "User not found. Please try again.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    } finally {
        // Close the MongoDB client
        if ($client !== null) {
            $client = null;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Daily Check Log</title>
   
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
    }

    .login-container {
        width: 300px;
        margin: 100px auto;
        padding: 20px;
        background-color: #ffffff;
        color: #333;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .login-container h1 {
        text-align: center;
        color: #343a40;
    }

    .login-container label {
        font-weight: bold;
    }

    .login-container input[type="text"],
    .login-container input[type="password"] {
        width: 90%;
        padding: 8px 12px;
        margin-top: 6px;
        margin-bottom: 12px;
        border: 1px solid black;
        border-radius: 4px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .login-container input[type="text"]:focus,
    .login-container input[type="password"]:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .login-container input[type="submit"] {
        background-color: #007bff; 
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        transition: background-color 0.3s ease;
    }

    .login-container input[type="submit"]:hover {
        background-color: #0056b3; 
    }

    .links {
        text-align: center;
        margin-top: 10px;
    }

    .links a {
        color: #007bff;
        text-decoration: none;
    }

    .links a:hover {
        text-decoration: underline;
    }
</style>
<body>
    <div class="login-container">
        <h1>Login to Daily Check Log</h1>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div style="color: red;"><?php echo $_SESSION['error_message']; ?></div>
            <?php unset($_SESSION['error_message']); // Clear the error message after displaying it ?>
        <?php endif; ?>
        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <br><br>

            <input type="submit" value="Login">
            <br>
            <div class="links">
                Don't have an account? <a href="signup.php">Sign up Now</a>
            </div>
        </form>
    </div>
</body>
</html>
