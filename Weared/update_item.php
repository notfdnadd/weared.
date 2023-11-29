<?php
session_start();

$servername = "localhost";
$dbname = "weareddata";
$user = "postgres";
$app_password = "Dandy8899";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id'])) {
    try {
        $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

        if (!$conn) {
            die("Failed to connect to the database: " . pg_last_error());
        }

        $item_id = $_POST['item_id'];
        $item_quantity = $_POST['item_quantity'];

        if ($item_quantity > 0) {
            $update_status_query = "UPDATE items SET status = 'available' WHERE id = $1";
        } else {
            $update_status_query = "UPDATE items SET status = 'sold out' WHERE id = $1";
        }

        $update_status_result = pg_prepare($conn, "update_status", $update_status_query);

        if (!$update_status_result) {
            die("Error preparing update_status query: " . pg_last_error($conn));
        }

        $update_status_result = pg_execute($conn, "update_status", array($item_id));

        if (!$update_status_result) {
            die("Error updating status: " . pg_last_error($conn));
        }
        
        $update_item_query = "UPDATE items SET 
            item_name = $2,
            item_type = $3,
            item_size = $4,
            item_color = $5,
            item_price = $6,
            item_quantity = $7
            WHERE id = $1";

        $update_item_result = pg_prepare($conn, "update_item", $update_item_query);

        if (!$update_item_result) {
            die("Error preparing update_item query: " . pg_last_error($conn));
        }

        $update_item_result = pg_execute($conn, "update_item", array(
            $item_id,
            $_POST['item_name'],
            $_POST['item_type'],
            $_POST['item_size'],
            $_POST['item_color'],
            $_POST['item_price'],
            $item_quantity
        ));

        if (!$update_item_result) {
            die("Error updating item details: " . pg_last_error($conn));
        }

        echo "Item updated successfully!";
        header("Location: dashboard.php");

        pg_close($conn);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
