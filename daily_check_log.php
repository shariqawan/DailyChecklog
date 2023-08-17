<?php

// Start the session
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$username = isset($_POST['username']) ? $_POST['username'] : $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Replace with your MongoDB Atlas connection string
    $username = "shariqawan0"; // Your MongoDB Atlas username
    $password = "awanawan123"; // Your MongoDB Atlas password
    $clusterName = "cluster0"; // Your MongoDB Atlas cluster name
    $databaseName = "daily_check_log_db"; // Your database name
    
    $connectionString = "mongodb+srv://username:password@cluster.mongodb.net/database";

    try {

        // Create a MongoDB client
        $client = new MongoDB\Driver\Manager($connectionString);

        // Specify the database and collection
        $collection = "Users";   // Replace with your collection name

        $tasks = [
            'task1' => 'Have You checked all tasks?',
            'task2' => 'Have you replied your emails?',
            'task3' => 'Have you done all assigned tasks which were assigned to you on the last day?',
            'task4' => 'Have you done task 2?',
            'task5' => 'Have you checked the database?'
        ];

        $date = date("Y-m-d");
        $time = date("H:i:s");
        date_default_timezone_set('UTC');

        $bulk = new MongoDB\Driver\BulkWrite();

        foreach ($tasks as $task => $question) {
            $task_answer = isset($_POST[$task]) ? $_POST[$task] : '';

            $document = [
                'username' => $username,
                'task' => $question,
                'answer' => $task_answer,
                'date' => $date,
                'time' => $time
            ];

            $bulk->insert($document);
        }

        $result = $client->executeBulkWrite("$databaseName.$collection", $bulk);

        if ($result->getInsertedCount() > 0) {
            $_SESSION['success_message'] = "Data inserted successfully for all tasks";
        } else {
            $_SESSION['error_message'] = "Error inserting data";
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error inserting data: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css">
    <style>
        .username-label {
            text-align: center;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
        }

        input[type="submit"]:hover {
            background-color: cadetblue; 
        }

        .btn-danger {
            background-color: darkcyan;
            width: 100%;
            margin-top: 15px;
        }

        .btn-danger:hover {
            background-color: lightblue;
        }

        /* Apply hover effect to submit button */
        input[type="submit"]:hover {
            background-color: darkcyan;
            transition: background-color 0.3s ease;
        }

        /* Apply hover effect to logout button */
        .btn-danger:hover {
            background-color: darkcyan;
            transition: background-color 0.3s ease;
        }

        body {
            background-color: white;
        }

        .notes {
            border-top-left-radius: 42px;
        }

        .dot {
            height: 6px;
            width: 6px;
            margin-left: 8px;
            margin-right: 3px;
            margin-top: 2px;
            background-color: rgb(91, 92, 91);
            border-radius: 50%;
            display: inline-block;
        }

        .dot-red {
            background-color: red !important;
            height: 6px;
            width: 6px;
            margin-left: 8px;
            margin-right: 3px;
            margin-top: 2px;
            border-radius: 50%;
            display: inline-block;
        }

        @keyframes click-wave {
            0% {
                height: 40px;
                width: 40px;
                opacity: 0.15;
                position: relative;
            }
            100% {
                height: 200px;
                width: 200px;
                margin-left: -80px;
                margin-top: -80px;
                opacity: 0;
            }
        }

        .option-input {
            -webkit-appearance: none;
            -moz-appearance: none;
            -ms-appearance: none;
            -o-appearance: none;
            appearance: none;
            position: relative;
            top: 10.3px;
            right: 0;
            bottom: 0;
            left: 0;
            height: 30px;
            width: 30px;
            transition: all 0.15s ease-out 0s;
            background: #cbd1d8;
            border: none;
            color: #fff;
            cursor: pointer;
            display: inline-block;
            margin-right: 0.5rem;
            outline: none;
            position: relative;
            z-index: 1000;
        }

        .option-input:hover {
            background: #9faab7;
        }

        .option-input:checked {
            background: red;
        }

        .option-input:checked::before {
            height: 30px;
            width: 30px;
            position: absolute;
            content: "\f111";
            font-family: "Font Awesome 5 Free";
            display: inline-block;
            font-size: 20.7px;
            text-align: center;
            line-height: 30px;
        }

        .option-input:checked::after {
            -webkit-animation: click-wave 0.25s;
            -moz-animation: click-wave 0.25s;
            animation: click-wave 0.25s;
            background: red;
            content: '';
            display: block;
            position: relative;
            z-index: 100;
        }

        .option-input.radio {
            border-radius: 50%;
        }

        .option-input.radio::after {
            border-radius: 50%;
        }

        .completed {
            color: gray;
            text-decoration-line: line-through;
        }

        .label-text {
            font-size: 18px; /* Adjust the font size as desired */
            margin-left: 10px; /* Adding some margin for spacing */
        }
        input[type="text"] {
    width: 50%; /* You can adjust the width as desired */
    /* Add any other styling you need */
}
        </style>
        <script>
    document.addEventListener("DOMContentLoaded", function() {
        const checkboxes = document.querySelectorAll(".option-input.radio");
        const labels = document.querySelectorAll(".label-text");

        checkboxes.forEach((checkbox, index) => {
            checkbox.addEventListener("change", function() {
                if (this.checked) {
                    labels[index].classList.add("completed");
                } else {
                    labels[index].classList.remove("completed");
                }
            });
        });
    });
</script>

</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-center">
            <div class="col-md-6">
                <h1 class="text-center">Daily Check Log</h1>
                <?php
                   
                if (isset($_SESSION['full_name'])) {
                    echo '<h4>Welcome, ' . $_SESSION['full_name'] . '!</h4>';
                }
                
                
                if (isset($_SESSION['success_message'])) {
                    echo '<p style="color: green;">' . $_SESSION['success_message'] . '</p>';
                    unset($_SESSION['success_message']); 
                }
            ?>
                   
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    
                    <label>
                        User Name:
                        <input type="text" name="username" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" required>
                    </label>
                    <br>

                    
                    <div class="p-3 bg-white">
    <!-- Task 1 -->
    <div class="d-flex align-items-center task-label">
        <label>
            <input type="checkbox" class="option-input radio" name="task1_status" value="completed">
            <span class="label-text">Have You checked all tasks?</span>
        </label>
    </div>
    <label class="additional-info-label"></label>
    <input type="text" name="task1" required>
    
    <!-- Task 2 -->
    <div class="d-flex align-items-center task-label">
        <label>
            <input type="checkbox" class="option-input radio" name="task2_status" value="completed">
            <span class="label-text">Have you replied your emails?</span>
        </label>
    </div>
    <label class="additional-info-label"></label>
    <input type="text" name="task2" required>
    
    <!-- Task 3 -->
    <div class="d-flex align-items-center task-label">
        <label>
            <input type="checkbox" class="option-input radio" name="task3_status" value="completed">
            <span class="label-text">Have you done all assigned tasks which were assigned to you on the last day?</span>
        </label>
    </div>
    <label class="additional-info-label"></label>
    <input type="text" name="task3" required>
    
    <!-- Task 4 -->
    <div class="d-flex align-items-center task-label">
        <label>
            <input type="checkbox" class="option-input radio" name="task4_status" value="completed">
            <span class="label-text">Have you done task 2?</span>
        </label>
    </div>
    <label class="additional-info-label"></label>
    <input type="text" name="task4" required>
    
    <!-- Task 5 -->
    <div class="d-flex align-items-center task-label">
        <label>
            <input type="checkbox" class="option-input radio" name="task5_status" value="completed">
            <span class="label-text">Have you checked the database?</span>
        </label>
    </div>
    <label class="additional-info-label"></label>
    <input type="text" name="task5" required>
</div>


                    <input type="submit" value="Submit">
                </form>
                

                <form action="logout.php" method="post">
                    <button type="submit" class="btn btn-danger mt-3">Logout</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

