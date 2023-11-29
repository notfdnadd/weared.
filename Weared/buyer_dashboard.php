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

    // Query to get the latest user information
    $userQuery = "SELECT username FROM users WHERE id = " . $_SESSION['user_id'];
    $userResult = pg_query($conn, $userQuery);

    if (!$userResult) {
        die("Error in SQL query: " . pg_last_error($conn));
    }

    $userRow = pg_fetch_assoc($userResult);
    $username = $userRow['username'];

    $query = "SELECT id, item_name, item_type, item_size, item_color, item_price, item_quantity, item_picture, seller_id, status FROM items WHERE status = 'available'";
    $result = pg_query($conn, $query);

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>weared. Buyer Dashboard</title>
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
            max-width: 1200px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin: 20px auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #534340;
            border-radius: 4px;
        }

        header h1 {
            color: #ffffff;
        }

        header button {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 10px 0 10px 0;
            background-color: #534340;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .accountbutton {
            background-color: #534340;
            color: #fff;
        }

        h1, h2 {
            color: #000;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
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

        .action-column form {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 5px;
        }

        .action-column input {
            margin-bottom: 5px;
        }

        .action-column input[type="submit"] {
            background-color: #534340;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .action-column input[type="submit"]:hover {
            background-color: #423230;
        }

        a.purchaseHistory {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
            background-color: #534340;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }

        .itemquantity {
        text-align: center;
        }

        .itemseller {
        text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>weared.</h1>
            <button onclick="window.location.href='account.php'" class="accountbutton">Go to Account</button>
        </header>

        <?php
        echo "<h1>Welcome, " . $username . "!</h1>";
        echo "<h2>This is your buyer dashboard. Browse and purchase items from sellers:</h2>";

        echo "<a class='purchaseHistory' href='purchase_history.php'>View Purchase History</a>";

        echo "<table>";
        echo "<tr><th>Item Name</th><th>Item Type</th><th>Item Size</th><th>Item Color</th><th>Item Price</th><th>Item Quantity</th><th>Item Picture</th><th>Seller</th><th>Action</th></tr>";

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
            echo "<td class='itemseller'>" . $row['seller_id'] . "</td>";

            echo "<td class='action-column'>
                    <form action='confirmationpurchase_item.php' method='post'>
                        <input type='hidden' name='item_id' value='" . $row['id'] . "'>
                        <input type='submit' value='Purchase'>
                    </form>
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
