<?php
    session_start();
    ob_start();
    include_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #333;
        }
        .signup-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 50px;
            margin-top: 7%;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <?php
            if (isset($_POST["submit"])) {
                $username = $_POST["username"];
                $email = $_POST["email"];
                $password = $_POST["password"];
                $repeat_password = $_POST["repeat_password"];
                
                $error = array();
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($error,"Invalid email format");
                }

                if (strlen($password) < 8) {
                    array_push($error,"Password must be at least 8 characters long");
                }

                if ($password !== $repeat_password) {
                    array_push($error, "Passwords do not match");
                }
                $server = server;
                $dbusername = dbusername;
                $dbpassword = dbpassword;
                $db = db;
                $conn = new mysqli($server, $dbusername, $dbpassword, $db);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                  }
                $stmt_email = $conn->prepare("SELECT * FROM users WHERE email = ?");
                $stmt_email->bind_param("s", $email);
                $stmt_email->execute();
                $result_email = $stmt_email->get_result();

                $stmt_user = $conn->prepare("SELECT * FROM users WHERE username = ?");
                $stmt_user->bind_param("s", $username);
                $stmt_user->execute();
                $result_user = $stmt_user->get_result();

                if ($result_email->num_rows > 0) {
                    array_push($error, "Email already exists");
                }

                if ($result_user->num_rows > 0) {
                    array_push($error, "Username already exists");
                }
                
                if (count($error) > 0) {
                    foreach ($error as $e) {
                        echo "<div class='alert alert-danger' role='alert'>$e</div>";
                    }
                } else {
                    $query = "INSERT INTO users (username, email, password) VALUES ( ?, ?, ?)";

                    $stmt = mysqli_stmt_init($conn);
                    $prep = mysqli_stmt_prepare($stmt, $query);

                    if ($prep === false) {
                        die("Prepare failed");
                    }
                    mysqli_stmt_bind_param($stmt,"sss", $username, $email, $passwordHash);

                    // Execute the prepared statement
                    if (mysqli_stmt_execute($stmt)) {
                        $conn->close();
                        echo "<div class='alert alert-success' role='alert'>User created successfully</div>";
                        header("Location: login.php");
                    } else {
                        die(" Something went wrong. Please try again later.");
                    }
                }   
            }
        ?>

        <h1>Sign Up</h1>
        <form action="signup.php" method="post">
            <div class="form-class">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="form-class">
                <input type="text" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="form-class">
                <input type="password" class="form-control" name="password" placeholder="New Password" required>
            </div>
            <div class="form-class">
                <input type="password" class="form-control" name="repeat_password" placeholder="Repeat Password" required>
            </div>
            <div class="form-btn">
                <input type="submit" name="submit" class="btn btn-primary" value="Sign Up">
            </div>
        </form>
        <div class="returnHome">
            <p><a href="index.php">Return to Home</a></p>
        </div>
    </div>
</body>
</html>