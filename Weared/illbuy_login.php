<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>I'll Buy - Sign In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sometype+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="illbuy_login.css">
    <style>
        .error {
            color: red;
            margin-top: 5px;
        }

        .create-account-link {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="main_content">
        <div class="left_images">
            <img src="Assets/Buy - SignUp/LeftImage.png" alt="">
        </div>

        <div class="title_homebuttons">
            <div class="weared">
                <h1>Sign In</h1>
            </div>

            <div class="register">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <div class="username">
                        <p>username</p>
                        <input type="text" name="boxusername" class="boxusername" placeholder="Enter your username"
                            required>
                    </div>
                    <div class="password">
                        <p>password</p>
                        <input type="password" name="boxpassword" class="boxpassword" placeholder="Enter your password"
                            required>
                    </div>
                    <div class="signinbutton">
                        <input type="submit" value="Sign In">
                    </div>
                </form>
                <?php
                    $servername = "localhost";
                    $dbname = "weareddata";
                    $user = "postgres";
                    $app_password = "Dandy8899";

                    session_start();

                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $username = $_POST['boxusername'];
                        $password = $_POST['boxpassword'];

                        try {
                            $conn = pg_connect("host=$servername dbname=$dbname user=$user password=$app_password");

                            if (!$conn) {
                                die("Failed to connect to the database: " . pg_last_error());
                            }

                            $query = "SELECT * FROM users WHERE username = '$username'";
                            $result = pg_query($conn, $query);

                            if (!$result) {
                                die("Error in SQL query: " . pg_last_error($conn));
                            }

                            if ($user_data = pg_fetch_assoc($result)) {
                                if (password_verify($password, $user_data['pass'])) {
                                    $_SESSION['user_id'] = $user_data['id'];
                                    $_SESSION['username'] = $user_data['username'];

                                    header("Location: buyer_dashboard.php");
                                    exit();
                                } else {
                                    echo '<p class="error">Invalid password</p>';
                                }
                            } else {
                                echo '<p class="error">User not found</p>';
                                echo '<p class="create-account-link"><a href="illbuy_createaccount.php">Create an account</a></p>';
                            }

                            pg_close($conn);
                        } catch (Exception $e) {
                            echo '<p class="error">Error: ' . $e->getMessage() . '</p>';
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</body>

</html>
