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

        $query = "INSERT INTO seller (name, username, email, pass) VALUES ($1, $2, $3, $4)";
        $result = pg_query_params($conn, $query, array($name, $username, $email, $hashedPassword));

        if (!$result) {
            die("Error in SQL query: " . pg_last_error());
        }

        pg_close($conn);

        header("Location: illsell_login.php");
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I'll Sell - Create Account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sometype+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="illsell_createaccount.css">
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
                if (isset($_SESSION['error_message'])) {
                    echo '<div class="error-message">' . $_SESSION['error_message'] . '</div>';
                    unset($_SESSION['error_message']);
                }
                ?>

                <form action="createaccountseller.php" method="post" class="datas">
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

                <div class="line">
                <img src="Assets/Buy - SignUp/Line 3.png" alt="">
            </div>

            <div class="login-option">
                <p>already have an account? <a href="illsell_login.php">login here</a></p>
            </div>
        </div>
    </div>
</body>

</html>
