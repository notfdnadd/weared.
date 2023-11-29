<?php
session_start();

// Check if the user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
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

    // Get the item_id from the URL parameter
    $item_id = $_GET['id'];

    // Retrieve item details based on item_id
    $query = "SELECT * FROM items WHERE id = $1";
    $result = pg_query_params($conn, $query, array($item_id));

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    // Fetch the item details
    $item = pg_fetch_assoc($result);

    // Display the item edit form
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Edit Item</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        form {
            max-width: 500px; /* Enlarged the max-width */
            width: 100%;
            background-color: #ffffff;
            padding: 30px; /* Enlarged the padding */
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #534340;
            text-align: center;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin: 10px 0 5px 0;
            color: #555;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        input[type='submit'] {
            background-color: #534340;
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type='submit']:hover {
            background-color: #423230;
        }
    </style>
</head>
<body>
    <form action='update_item.php' method='post'>
        <h2>weared. edit item</h2>
        <label for='item_name'>Item Name:</label>
        <input type='text' name='item_name' value='<?= $item['item_name'] ?>' required>
        <label for='item_type'>Item Type:</label>
        <input type='text' name='item_type' value='<?= $item['item_type'] ?>' required>
        <label for='item_size'>Item Size:</label>
        <input type='text' name='item_size' value='<?= $item['item_size'] ?>' required>
        <label for='item_color'>Item Color:</label>
        <input type='text' name='item_color' value='<?= $item['item_color'] ?>' required>
        <label for='item_price'>Item Price:</label>
        <input type='number' name='item_price' value='<?= $item['item_price'] ?>' required>
        <label for='item_quantity'>Item Quantity:</label>
        <input type='number' name='item_quantity' value='<?= $item['item_quantity'] ?>' required>
        <input type='hidden' name='item_id' value='<?= $item_id ?>'>
        <input type='submit' value='Update Item'>
    </form>
</body>
</html>

<?php
    pg_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
