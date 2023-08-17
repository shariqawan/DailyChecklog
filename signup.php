<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$error_message = ""; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dbHost = "localhost";
    $dbUsername = "root";
    $dbPassword = "";
    $dbName = "daily_check_log";
    $tbl_name = "users";
    $dbConn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    if ($dbConn->connect_error) {
        die("Connection failed: " . $dbConn->connect_error);
    }

    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    
    if (strpos($username, ' ') !== false) {
        $error_message = "Error: Username must not contain spaces.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (full_name, username, email, password, created_at)
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = $dbConn->prepare($query);

        if ($stmt === false) {
            die("Error preparing statement: " . $dbConn->error);
        }

        $stmt->bind_param("ssss", $full_name, $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            
            $_SESSION['full_name'] = $full_name;

            header("Location: login.php");
            exit();
        } else {
            $error_message = "Error executing statement: " . $stmt->error;
        }

        $stmt->close();
        $dbConn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Daily Check Log</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .signup-container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .signup-container h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #343a40;
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 8px 12px;
            width: 100%;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .mb-3 {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff; 
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3; 
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 0.75rem 1.25rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <div class="signup-container">
        <h1>Signup</h1>
         
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <form action="" method="post" onsubmit="return validateForm()">
            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="full_name" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required pattern="[^\s]+" title="Spaces are not allowed in the username field">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label> 
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Signup</button>
        </form>
    </div>
    <script>
        function validateForm() {
            var usernameInput = document.querySelector('input[name="username"]');
            var username = usernameInput.value.trim(); 
            
            if (username.indexOf(' ') !== -1) {
                alert("Error: Username must not contain spaces.");
                return false; 
            }
            
            return true;
        }
    </script>
</body>
</html>
