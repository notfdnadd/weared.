<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I'll Buy - Create Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sometype+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="illbuy_createaccount.css">
</head>

<body>
    <div class="main_content">
        <div class="left_images">
            <img src="Assets/Buy - SignUp/LeftImage.png" alt="">
        </div>

        <div class="title_homebuttons">
            <div class="weared">
                <h1>Sign Up</h1>
            </div>

            <div class="register">
                <?php
                session_start();
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']);
                }

                if (isset($_SESSION['username_taken'])) {
                    echo '<div class="notice-message">' . $_SESSION['username_taken'] . '</div>';
                    unset($_SESSION['username_taken']);
                }
                ?>

                <form action="createaccount.php" method="post">
                    <div class="name">
                        <p>name</p>
                        <input type="text" name="boxname" class="boxname" placeholder="Enter your name" required>
                    </div>
                    <div class="username">
                        <p>username</p>
                        <input type="text" name="boxusername" class="boxusername" placeholder="Enter your username" required>
                    </div>
                    <div class="email">
                        <p>email</p>
                        <input type="email" name="boxemail" class="boxemail" placeholder="Enter your email" required>
                    </div>
                    <div class="address">
                        <p>address</p>
                        <input type="text" name="boxaddress" class="boxaddress" placeholder="Enter your address" required>
                    </div>
                    <div class="password">
                        <p>password</p>
                        <input type="password" name="boxpassword" class="boxpassword" placeholder="Enter your password" required>
                    </div>
                    <div class="retypepassword">
                        <p>re-type password</p>
                        <input type="password" name="boxretypepassword" class="boxretypepassword"
                            placeholder="Retype your password" required>
                    </div>
                    <div class="signupbutton">
                        <input type="submit" value="Sign Up">
                    </div>
                </form>
            </div>

            <div class="line">
                <img src="Assets/Buy - SignUp/Line 3.png" alt="">
            </div>

            <div class="login-option">
                <p>already have an account? <a href="illbuy_login.php">login here</a></p>
            </div>
        </div>
    </div>
</body>

</html>


<?php
$servername = "localhost";
$dbname = "weareddata";
$user = "postgres";
$password = "Dandy8899";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['boxname'];
    $username = $_POST['boxusername'];
    $email = $_POST['boxemail'];
    $password = $_POST['boxpassword'];
    $retypePassword = $_POST['boxretypepassword'];

    if ($password !== $retypePassword) {
        die("Error: Passwords do not match");
    }

    try {
        $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$password");

        if (!$conn) {
            die("Failed to connect to the database: " . pg_last_error());
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (name, username, email, address, pass) VALUES ('$name', '$username', '$email', '$address', '$hashedPassword')";
        $result = pg_query($conn, $query);

        if (!$result) {
            die("Error in SQL query: " . pg_last_error());
        }

        pg_close($conn);

        header("Location: illbuy_login.php");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<style>
    /* CS RESET */
* {
    padding: 0;
    margin: 0;
    box-sizing: border-box;
    font-family: 'Sometype Mono', monospace;
}

html {
    scroll-behavior: smooth;
    background: #D9D9D9;
    background-repeat: no-repeat;
    background-size: cover;
    background-position-x: center;
    background-position-y: center;
}

body {
    overflow: hidden;
}

.main_content {
    display: flex;
    height: 100vh;
    align-items: center;
}

.title_homebuttons {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: auto;
    height: 100%;
}

.boxname,
.boxusername,
.boxemail,
.boxaddress,
.boxpassword,
.boxretypepassword,
.signupbutton input {
    width: 269px;
    padding: 10px;
    border-radius: 12px;
    background: var(--White, #FFF);
    border: 1px solid #ccc;
    box-sizing: border-box;
}

.name,
.username,
.email,
.address,
.password,
.retypepassword {
    margin: 10px 0 0 0;
}

.signupbutton {
    margin: 17px 0 0 0;
}

.signupbutton input {
    width: 100%;
    padding: 10px;
    border-radius: 12px;
    background: var(--Terniary, #534340);
    color: #FFF;
    cursor: pointer;
    border: none;
    transition: background 0.3s ease-in-out;
}

.signupbutton input:hover {
    background: #423230;
}

.or {
    margin: 0 0 0 0px;
    width: 25px;
    display: flex;
    border-radius: 10px;
    background: #FFF;
    padding: 1px;
    justify-content: center;
    position: relative;
    bottom: 15px;
}

.left_images {
    flex-shrink: 0;
    background: url(Assets/Home/LeftImage.png), lightgray 50% / cover no-repeat;
    display: flex;
    background-repeat: no-repeat;
}

.left_images img {
    width: 960px;
    height: 1200px;
}

.login-option a {
    color: #534340;
    font-weight: bold;
    transition: color 0.3s ease-in-out;
}

.login-option a:hover {
    color: #423230;
}

.login-option a:visited {
    color: #534340;
}

.login-option a:focus {
    outline: none;
}

.login-option a:active {
    color: #534340;
}

</style>