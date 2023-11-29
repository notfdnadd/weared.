<?php
session_start();

$servername = "localhost";
$dbname = "weareddata";
$user = "postgres";
$app_password = "Dandy8899";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id']) && isset($_POST['quantity'])) {
    try {
        $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

        if (!$conn) {
            die("Failed to connect to the database: " . pg_last_error());
        }

        
        $item_id = (int)$_POST['item_id'];
        $quantity = (int)$_POST['quantity'];
        $buyer_id = (int)$_SESSION['user_id'];

        
        if ($quantity <= 0) {
            die("Invalid quantity.");
        }

        
        $check_availability_query = "SELECT item_quantity, item_price, seller_id FROM items WHERE id = $1 AND status = 'available'";
        $check_availability_result = pg_query_params($conn, $check_availability_query, array($item_id));

        if (!$check_availability_result || pg_num_rows($check_availability_result) == 0) {
            echo "Item is not available for purchase.";
            exit();
        }

        $item_row = pg_fetch_assoc($check_availability_result);
        $item_quantity = $item_row['item_quantity'];
        $seller_id = $item_row['seller_id'];

        
        if ($item_quantity < $quantity) {
            echo "Not enough quantity available for purchase.";
            exit();
        }

        
        $new_quantity = $item_quantity - $quantity;

        
        $update_quantity_query = "UPDATE items SET item_quantity = $1 WHERE id = $2";
        $update_quantity_result = pg_query_params($conn, $update_quantity_query, array($new_quantity, $item_id));

        if (!$update_quantity_result) {
            die("Error updating quantity: " . pg_last_error($conn));
        }

        
        if ($new_quantity == 0) {
            $update_status_query = "UPDATE items SET status = 'sold out' WHERE id = $1";
        } else {
            $update_status_query = "UPDATE items SET status = 'available' WHERE id = $1";
        }

        $update_status_result = pg_query_params($conn, $update_status_query, array($item_id));

        if (!$update_status_result) {
            die("Error updating status: " . pg_last_error($conn));
        }

        
        if (isset($_FILES['proof_of_payment'])) {
            $proof_of_payment = $_FILES['proof_of_payment'];

            
            $uploadDir = 'uploads/';  
            $uploadFile = $uploadDir . basename($proof_of_payment['name']);

            if (move_uploaded_file($proof_of_payment['tmp_name'], $uploadFile)) {
                
                $total_price = $quantity * $item_row['item_price'];

                $insert_purchase_query = "INSERT INTO purchases (buyer_id, item_id, quantity, total_price, proof_of_payment, timestamp) VALUES ($1, $2, $3, $4, $5, CURRENT_TIMESTAMP)";
                $insert_purchase_result = pg_query_params($conn, $insert_purchase_query, array($buyer_id, $item_id, $quantity, $total_price, $uploadFile));

                if (!$insert_purchase_result) {
                    die("Error inserting purchase record: " . pg_last_error($conn));
                }

                echo "Purchase successful!";
                header("Location: buyer_dashboard.php");
                pg_close($conn);
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Proof of payment file is required.";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
