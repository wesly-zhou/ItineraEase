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
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #333;
        }

        p {
            text-align: center;
            margin-top: 1rem;
        }
        .signup-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 50px;
            margin-top: 10%;
            box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <?php
            if (isset($_POST["login"])) {
                $_SESSION["test"] = "abc";
                $userDetail = $_POST["username"];   
                $password = $_POST["password"];
                $server = server;
                $dbusername = dbusername;
                $dbpassword = dbpassword;
                $db = db;
                $conn = new mysqli($server, $dbusername, $dbpassword, $db);
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                  }
                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                $stmt->bind_param("ss", $userDetail, $userDetail);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                if ($user) {
                    if (password_verify($password, $user["password"])) {
                        $_SESSION["user"] = $user;
                        $conn->close();
                        header("Location: index.php");
                        die();
                    } else {
                        echo "<div class='alert alert-danger'>Incorrect password</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger'>Invalid username or email</div>";
                }
            }
        ?>
        <h1>Login</h1>
        <form action="login.php" method="post">
            <div class="form-class">
                <input type="text" class="form-control" name="username" placeholder="Username or Email" required>
            </div>
            <div class="form-class">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="form-btn">
                <input type="submit" name="login" class="btn btn-primary" value="Login">
            </div>
        </form>
        <div>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
            <p><a href="index.php">Return to Home</a></p>
        </div>
    </div>   

</body>
</html>