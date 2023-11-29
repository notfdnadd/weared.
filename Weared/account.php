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

    $user_id = $_SESSION['user_id'];

    $query = "SELECT id, name, username, email, address FROM users WHERE id = $1";
    $result = pg_prepare($conn, "get_user", $query);

    if (!$result) {
        die("Error in SQL query preparation: " . pg_last_error($conn));
    }

    $result = pg_execute($conn, "get_user", array($user_id));

    if (!$result) {
        die("Error in SQL query execution: " . pg_last_error($conn));
    }

    $user_data = pg_fetch_assoc($result);

    pg_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>weared. account</title>
    <style>
        /* CS RESET */
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

        .container {
            max-width: 400px;
            width: 100%;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #534340;
            text-align: center;
            margin: -10px 0 20px 0; 
        }

        p {
            margin-bottom: 15px;
            color: #555;
        }

        a {
            display: block;
            text-align: center;
            padding: 12px;
            background-color: #534340;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        a:hover {
            background-color: #423230;
        }

        .edit-account-button {
            display: block;
            text-align: center;
            padding: 12px;
            background-color: #534340;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .edit-account-button:hover {
            background-color: #423230;
        }

        .back-button {
            display: block;
            text-align: center;
            padding: 12px;
            background-color: #534340;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #423230;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>weared. account</h1>

        <?php
        echo "<p><strong>Name:</strong> " . $user_data['name'] . "</p>";
        echo "<p><strong>Username:</strong> " . $user_data['username'] . "</p>";
        echo "<p><strong>Email:</strong> " . $user_data['email'] . "</p>";
        echo "<p><strong>Address:</strong> " . $user_data['address'] . "</p>";
        ?>

        <a class="edit-account-button" href="edit_account.php">Edit Account</a>
        <a class="back-button" href="buyer_dashboard.php">Back to Buyer Dashboard</a>
    </div>
</body>

</html>
