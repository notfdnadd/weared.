<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: illsell_login.php");
    exit();
}

$servername = "localhost";
$dbname = "weareddata";
$user = "postgres";
$app_password = "Dandy8899";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {
        $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

        if (!$conn) {
            die("Failed to connect to the database: " . pg_last_error());
        }

        $item_id = $_GET['id'];

        $delete_purchases_query = "DELETE FROM purchases WHERE item_id = $1";
        $delete_purchases_result = pg_query_params($conn, $delete_purchases_query, array($item_id));

        if (!$delete_purchases_result) {
            die("Error deleting purchases: " . pg_last_error($conn));
        }

        $delete_item_query = "DELETE FROM items WHERE id = $1";
        $delete_item_result = pg_query_params($conn, $delete_item_query, array($item_id));

        if (!$delete_item_result) {
            die("Error deleting item: " . pg_last_error($conn));
        }

        echo "Item deleted successfully.";

        header("Location: dashboard.php");
        exit();

        pg_close($conn);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
