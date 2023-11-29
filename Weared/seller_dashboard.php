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
    
    $seller_id = $_SESSION['user_id'];
    $query = "SELECT
        p.id AS purchase_id,
        i.item_name,
        i.item_type,
        i.item_price,
        p.quantity AS items_bought,
        p.timestamp AS latest_purchase_timestamp,
        u.username AS buyer_username,
        u.address AS buyer_address,
        p.proof_of_payment,
        p.status
    FROM
        purchases p
    LEFT JOIN
        items i ON p.item_id = i.id
    LEFT JOIN
        users u ON p.buyer_id = u.id  
    WHERE
        i.seller_id = $1
    ORDER BY
        latest_purchase_timestamp ASC";
    $result = pg_query_params($conn, $query, array($seller_id));

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
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
            max-width: 1500px;
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

        .earnings-column {
            text-align: center;
        }

        .container button {
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

        .container a{
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 5px 0 -20px 0;
            background-color: #534340;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none; 
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        
        echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>";
        echo "<h2>This is your seller dashboard. View buyers and earnings for your items:</h2>";

        echo "<table>";
        echo "<tr><th>Item Name</th><th>Item Type</th><th>Item Price</th><th>Items Bought</th><th>Buyer</th><th>Buyer Address</th><th>Status</th><th>Earnings</th><th>Latest Purchase Timestamp</th></tr>";

        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['item_name'] . "</td>";
            echo "<td>" . $row['item_type'] . "</td>";

            
            $itemPriceIDR = number_format($row['item_price'], 0, ',', '.') . " IDR</td>";
            echo "<td>" . $itemPriceIDR . "</td>";

            
            echo "<td>" . $row['items_bought'] . "</td>";

            echo "<td>";
            echo $row['buyer_username'] . "<br>";
            
            echo "<a href='" . $row['proof_of_payment'] . "' target='_blank'>View Proof of Payment</a><br>";
            echo "</td>";

            
            echo "<td>";
            if (isset($row['buyer_address'])) {
                echo $row['buyer_address'];
            } else {
                echo "N/A"; 
            }
            echo "</td>";

            echo "<td>";
            
            echo "<select class='status-select' onchange='updateStatus(" . $row['purchase_id'] . ", this.value)'>";
            $statusOptions = ['Not Confirmed', 'Confirmed', 'Packed', 'Delivered', 'Done'];
            foreach ($statusOptions as $option) {
                echo "<option value='$option' " . ($row['status'] == $option ? 'selected' : '') . ">$option</option>";
            }
            echo "</select>";
            echo "</td>";

            
            $earnings = $row['item_price'] * $row['items_bought'];
            $earnings_formatted = number_format($earnings, 0, ',', '.') . " IDR</td>";
            echo "<td class='earnings-column'>" . $earnings_formatted . "</td>";

            
            echo "<td>" . $row['latest_purchase_timestamp'] . "</td>";

            echo "</tr>";
        }

        echo "</table>";
        ?>

        <button onclick="window.location.href='dashboard.php'">Go Back to Dashboard</button>
    </div>
</body>

</html>

<?php
    pg_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<script>
function updateStatus(purchaseId, newStatus) {
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'update_status.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status == 200) {
            
            alert(xhr.responseText); 
        } else {
            
            alert("Error updating status");
        }
    };
    xhr.send('purchase_id=' + purchaseId + '&status=' + newStatus);
}
</script>
