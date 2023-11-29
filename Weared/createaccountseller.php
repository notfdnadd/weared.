<?php
session_start();

$servername = "localhost";
$dbname = "weareddata";
$user = "postgres";
$app_password = "Dandy8899";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['boxname'];
    $username = $_POST['boxusername'];
    $email = $_POST['boxemail'];
    $password = $_POST['boxpassword'];
    $retypePassword = $_POST['boxretypepassword'];

    if ($password !== $retypePassword) {
        $_SESSION['error_message'] = "Error: Passwords do not match";
        header("Location: illsell_createaccount.php");
        exit();
    }

    try {
        $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

        if (!$conn) {
            $_SESSION['error_message'] = "Failed to connect to the database: " . pg_last_error();
            header("Location: illsell_createaccount.php");
            exit();
        }

        $checkQuery = "SELECT * FROM seller WHERE username = '$username'";
        $checkResult = pg_query($conn, $checkQuery);

        if (!$checkResult) {
            $_SESSION['error_message'] = "Error in username check query: " . pg_last_error($conn);
            header("Location: illsell_createaccount.php");
            exit();
        }

        if (pg_num_rows($checkResult) > 0) {
            $_SESSION['error_message'] = "Error: username is already taken";
            header("Location: illsell_createaccount.php");
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO seller (name, username, email, pass) VALUES ('$name', '$username', '$email', '$hashedPassword')";
        $result = pg_query($conn, $query);

        if (!$result) {
            $_SESSION['error_message'] = "Error in SQL query: " . pg_last_error($conn);
            header("Location: illsell_createaccount.php");
            exit();
        }

        pg_close($conn);

        header("Location: illsell_login.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
        header("Location: illsell_createaccount.php");
        exit();
    }
}
?>
