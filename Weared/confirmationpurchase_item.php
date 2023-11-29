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

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['item_id'])) {
        $item_id = $_POST['item_id'];

        $query = "SELECT id, item_name, item_type, item_size, item_color, item_price, item_quantity, item_picture, seller_id FROM items WHERE id = $item_id AND status = 'available'";
        $result = pg_query($conn, $query);

        if (!$result) {
            die("Error in SQL query: " . pg_last_error($conn));
        }

        $item = pg_fetch_assoc($result);

        if (isset($_POST['quantity']) && isset($_FILES['proof_of_payment'])) {
            $quantity = $_POST['quantity'];

            $uploadDir = 'uploads/'; 
            $uploadFile = $uploadDir . basename($_FILES['proof_of_payment']['name']);

            if (move_uploaded_file($_FILES['proof_of_payment']['tmp_name'], $uploadFile)) {
                $insertQuery = "INSERT INTO purchases (item_id, quantity, proof_of_payment) VALUES ($item_id, $quantity, '$uploadFile')";
                $insertResult = pg_query($conn, $insertQuery);

                if (!$insertResult) {
                    die("Error inserting data into the database: " . pg_last_error($conn));
                }

                echo "Purchase successful!";
            } else {
                echo "Error uploading file.";
            }
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Purchase</title>
    <style>
        /* CS RESET */
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: 'Sometype Mono', monospace;
        }

        body {
            background: #D9D9D9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        div {
            max-width: 800px;
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        div > div {
            flex: 1;
            padding-right: 20px;
        }

        div img {
            margin: 25px 0 0 0;
            max-width: 200px;
            border-radius: 8px;
        }

        h2 {
            color: #000;
            margin-bottom: 20px;
        }

        p {
            color: #333;
            margin-bottom: 10px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-top: 20px;
        }

        label {
            margin-bottom: 5px;
            color: #000;
        }

        input {
            padding: 8px;
            margin-bottom: 10px;
            width: 100%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #534340;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #423230;
        }
    </style>
    <script>
        function updateTotalPrice() {
            var itemPrice = <?php echo $item['item_price']; ?>;
            var buyQuantity = document.getElementById('quantity').value;
            var total = itemPrice * buyQuantity;
            document.getElementById('total_price').innerText = 'Total Price: Rp ' + total.toLocaleString('en-ID');
        }
    </script>
</head>

<body>
    <div>
        <?php
        if (isset($item)) {
            echo "<div>";
            echo "<div>";
            echo "<h2>Item Details:</h2>";
            echo "<p><strong>Item Name:</strong> {$item['item_name']}</p>";
            echo "<p><strong>Item Type:</strong> {$item['item_type']}</p>";
            echo "<p><strong>Item Size:</strong> {$item['item_size']}</p>";
            echo "<p><strong>Item Color:</strong> {$item['item_color']}</p>";
            echo "<p><strong>Item Price:</strong> Rp " . number_format($item['item_price'], 0, ',', '.') . "</p>";
            echo "<p><strong>Item Stock:</strong> {$item['item_quantity']}</p>";
            echo "<p><strong>Seller ID:</strong> {$item['seller_id']}</p>";

            $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
            echo "</div>";

            echo "<img src='{$item['item_picture']}' alt='Item Picture'>";
            echo "</div>";

            echo "<form action='purchase_item.php' method='post' enctype='multipart/form-data'>";
            echo "<input type='hidden' name='item_id' value='{$item['id']}'>";
            echo "<label for='quantity'>Quantity:</label>";
            echo "<input type='number' id='quantity' name='quantity' value='{$quantity}' min='1' max='{$item['item_quantity']}' required onchange='updateTotalPrice()'>";

            $itemTotal = $item['item_price'] * $quantity;
            echo "<p id='total_price'><strong>Total Price:</strong> Rp " . number_format($itemTotal, 0, ',', '.') . "</p>";
            echo "<label for='proof_of_payment'>Proof of Payment:<br>8888 9999 0000 - Weared - Bank Of Indonesia</label>";
            echo "<input type='file' name='proof_of_payment' accept='image/*' required>";
            echo "<input type='submit' value='Purchase'>";
            echo "</form>";
        } else {
            echo "<p>Error: Item details not found.</p>";
        }
        ?>
    </div>
</body>

</html>
