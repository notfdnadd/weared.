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
    <title>weared. edit account</title>
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
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
            color: #555;
        }

        input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 12px;
            background-color: #534340;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #423230;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>weared. edit account</h1>

        <form action="update_account.php" method="post">
            <label for="name">name:</label>
            <input type="text" id="name" name="name" value="<?php echo $user_data['name']; ?>" required>

            <label for="username">username:</label>
            <input type="text" id="username" name="username" value="<?php echo $user_data['username']; ?>" required>

            <label for="email">email:</label>
            <input type="email" id="email" name="email" value="<?php echo $user_data['email']; ?>" required>

            <label for="address">address:</label>
            <input type="text" id="address" name="address" value="<?php echo $user_data['address']; ?>" required>

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>

</html>
