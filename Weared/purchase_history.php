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

    
    $buyer_id = $_SESSION['user_id'];
    $query = "SELECT p.id, i.item_name, i.item_type, i.item_size, i.item_color, p.quantity, p.total_price, p.timestamp, p.status
          FROM purchases p
          JOIN items i ON p.item_id = i.id
          WHERE p.buyer_id = $1";
    $result = pg_query_params($conn, $query, array($buyer_id));

    if (!$result) {
        die("Error in SQL query: " . pg_last_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>
    <style>
       
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

        .purchase-history-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            background-color: #534340;
            color: #fff;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
        
        echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>";
        echo "<h2>Your Purchase History:</h2>";

        echo "<table>";
        echo "<tr><th>Item Name</th><th>Item Type</th><th>Item Size</th><th>Item Color</th><th>Quantity</th><th>Total Price</th><th>Status</th><th>Timestamp</th></tr>";
        
        while ($row = pg_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['item_name'] . "</td>";
            echo "<td>" . $row['item_type'] . "</td>";
            echo "<td>" . $row['item_size'] . "</td>";
            echo "<td>" . $row['item_color'] . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "<td>" . number_format($row['total_price'], 0, ',', '.') . " IDR</td>";
            echo "<td>" . $row['status'] . "</td>"; 
            echo "<td>" . $row['timestamp'] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        
        ?>

        <a class="purchase-history-link" href='buyer_dashboard.php'>Go Back to Buyer Dashboard</a>
    </div>
</body>

</html>

<?php
    pg_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
