<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: illbuy_login.php");
    exit();
}

$servername = "localhost";
$dbname = "weareddata";
$user = "postgres";
$app_password = "Dandy8899";

try {
    $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

    if (!$conn) {
        die("Failed to connect to the database: " . pg_last_error());
    }

    $user_id = $_SESSION['user_id'];

    $query = "SELECT id, name, username, email, address FROM users WHERE id = $1";
    $result = pg_prepare($conn, "get_user", $query);

    if (!$result) {
        die("Error in SQL query preparation: " . pg_last_error($conn));
    }

    $result = pg_execute($conn, "get_user", array($user_id));

    if (!$result) {
        die("Error in SQL query execution: " . pg_last_error($conn));
    }

    $user_data = pg_fetch_assoc($result);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_name = $_POST['name'];
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $new_address = $_POST['address'];
        
        $update_query = "UPDATE users SET name = $1, username = $2, email = $3, address = $4 WHERE id = $5";
        $update_result = pg_prepare($conn, "update_user", $update_query);

        if (!$update_result) {
            die("Error in SQL query preparation: " . pg_last_error($conn));
        }

        $update_result = pg_execute($conn, "update_user", array($new_name, $new_username, $new_email, $new_address, $user_id));

        if (!$update_result) {
            die("Error in SQL query execution: " . pg_last_error($conn));
        }

        header("Location: account.php");
        exit();
    }

    
    pg_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
