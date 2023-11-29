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

try {
    $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

    if (!$conn) {
        die("Failed to connect to the database: " . pg_last_error());
    }

    $user_id = $_SESSION['user_id'];

    $query = "SELECT id, item_name, item_type, item_size, item_color, item_price, item_quantity, item_picture, status FROM items WHERE seller_id = $1";
    $result = pg_prepare($conn, "get_items", $query);

    if (!$result) {
        die("Error in SQL query preparation: " . pg_last_error($conn));
    }

    $result = pg_execute($conn, "get_items", array($user_id));

    if (!$result) {
        die("Error in SQL query execution: " . pg_last_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seller Dashboard</title>
    <style>
        /* CS RESET */
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            overflow-x: hidden;
            font-family: 'Sometype Mono', monospace;
        }

        body {
            background: #D9D9D9;
        }

        .container {
            max-width: 1300px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }

        h1,
        h2 {
            color: #000;
            text-align: center;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }

        label {
            margin-bottom: 5px;
        }

        input {
            padding: 8px;
            margin-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            max-width: 100px;
            height: auto;
        }

        .action-column {
            text-align: center;
        }

        .action-column a {
            display: inline-block;
            margin: 5px;
            padding: 8px 12px;
            text-decoration: none;
            color: #fff;
            background-color: #534340;
            border-radius: 4px;
        }

        .action-column a:hover {
            background: #423230;
        }

        .action-button {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 5px 0 15px 0;
            background-color: #534340;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none; 
        }

        .container a {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0 0 0;
            background-color: #534340;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none; 
        }

        .action-button:hover {
            background: #423230;
        }

        .itemquantity {
        text-align: center;
        }

    </style>
</head>

<body>
    <div class="container">
    <?php
        echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>";
        echo "<h2>Upload Item for Sale</h2>";

        echo "<a href='seller_dashboard.php'>Go to Seller Dashboard</a>";

        echo "<form action='upload_item.php' method='post' enctype='multipart/form-data'>";
        echo "<label for='item_name'>Item Name:</label>";
        echo "<input type='text' name='item_name' required>";

        echo "<label for='item_type'>Item Type:</label>";
        echo "<input type='text' name='item_type' required>";

        echo "<label for='item_size'>Item Size:</label>";
        echo "<input type='text' name='item_size' required>";

        echo "<label for='item_color'>Item Color:</label>";
        echo "<input type='text' name='item_color' required>";

        echo "<label for='item_price'>Item Price:</label>";
        echo "<input type='number' name='item_price' required>";

        echo "<label for='item_quantity'>Item Quantity:</label>";
        echo "<input type='number' name='item_quantity' required>";

        echo "<label for='item_picture'>Item Picture:</label>";
        echo "<input type='file' name='item_picture' accept='image/*' required>";

        echo "<input type='submit' value='Upload Item' class='action-button'>";
        echo "</form>";

        echo "<h2>Item(s)</h2>";
        echo "<table>";
        echo "<tr><th>Item Name</th><th>Item Type</th><th>Item Size</th><th>Item Color</th><th>Item Price</th><th>Item Quantity</th><th>Item Picture</th><th>Status</th><th>Actions</th></tr>";

        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['item_name'] . "</td>";
            echo "<td>" . $row['item_type'] . "</td>";
            echo "<td>" . $row['item_size'] . "</td>";
            echo "<td>" . $row['item_color'] . "</td>";

            $itemPriceIDR = number_format($row['item_price'], 0, ',', '.') . " IDR</td>";
            echo "<td>" . $itemPriceIDR . "</td>";

            echo "<td class='itemquantity'>" . $row['item_quantity'] . "</td>";
            echo "<td><img src='" . $row['item_picture'] . "' alt='Item Picture'></td>";

            $status = ($row['item_quantity'] == 0) ? 'sold out' : 'available';

            echo "<td>" . $status . "</td>";

            echo "<td class='action-column'>
                    <a href='edit_item.php?id=" . $row['id'] . "'>Edit</a>
                    <a href='delete_item.php?id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this item?\")'>Delete</a>
                </td>";

            echo "</tr>";
        }

        echo "</table>";

        ?>
    </div>
</body>

</html>

<?php
    pg_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
