<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ilsell_login.php");
    exit();
}

$servername = "localhost";
$dbname = "weareddata";
$user = "postgres";
$app_password = "Dandy8899";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $item_name = $_POST['item_name'];
    $item_type = $_POST['item_type'];
    $item_size = $_POST['item_size'];
    $item_color = $_POST['item_color'];
    $item_price = $_POST['item_price'];
    $item_quantity = $_POST['item_quantity']; 

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["item_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }

    
    if ($_FILES["item_picture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    
    $allowed_formats = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_formats)) {
        echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $unique_filename = uniqid() . "_" . $_FILES["item_picture"]["name"];
        $target_file = $target_dir . $unique_filename;

        if (move_uploaded_file($_FILES["item_picture"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars($unique_filename) . " has been uploaded.";

            
            try {
                $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

                if (!$conn) {
                    die("Failed to connect to the database: " . pg_last_error());
                }

                $user_id = $_SESSION['user_id'];

                $query = "INSERT INTO items (seller_id, item_name, item_type, item_size, item_color, item_price, item_quantity, item_picture, status) 
                          VALUES ($1, $2, $3, $4, $5, $6, $7, $8, 'available')";
                $result = pg_query_params($conn, $query, array(
                    $user_id, $item_name, $item_type, $item_size, $item_color, $item_price, $item_quantity, $target_file
                ));

                if (!$result) {
                    die("Error in SQL query: " . pg_last_error($conn));
                }

                echo "Item details and image path saved to the database.";
                
                header("Location: dashboard.php");
                exit();
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            } finally {
                pg_close($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
} else {
    echo "Invalid request.";
}
?>
