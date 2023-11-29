<?php
session_start();

$servername = "localhost";
$dbname = "weareddata";
$user = "postgres";
$app_password = "Dandy8899";

$purchaseId = $_POST['purchase_id'];
$newStatus = $_POST['status'];

try {
    $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

    if (!$conn) {
        die("Failed to connect to the database: " . pg_last_error());
    }

    $updateStatusQuery = "UPDATE purchases SET status = $1 WHERE id = $2"; 
    $updateStatusResult = pg_query_params($conn, $updateStatusQuery, array($newStatus, $purchaseId));

    if (!$updateStatusResult) {
        die("Error updating status: " . pg_last_error($conn));
    }

    echo "Status updated successfully";

    pg_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
