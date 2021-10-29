<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zork";

$msg = "";
$userInfo;

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

// check if _POST is set then set up sql connection and query
if (isset($_POST['submitBtn'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // query to check if username and hashed password match
    $selectUsernames = "SELECT * FROM `users` WHERE `username`='$username'";
    $res = mysqli_query($conn, $selectUsernames) or die("Query failed: " . mysqli_error($conn));

    // if $sql returns nothing then $msg is set to error message
    if (mysqli_num_rows($res) == 0) {
        $msg = "Username or password is incorrect";
    } else {
        // if sql returns something then $userInfo is set to the result of the query
        $userInfo = mysqli_fetch_array($res);
        // if the hashed password matches the hashed password in the database then the user is logged in
        if (password_verify($password, $userInfo['password'])) {
            $_SESSION['user'] = $userInfo;
            header("Location: index.php");
        } else {
            $msg = "Username or password is incorrect";
        }
    }

    $_POST = [];
}
?>
<html>

<head>
    <title>Log In To Zork</title>
    <link rel="stylesheet" href="https://unpkg.com/axist@latest/dist/axist.min.css" />
</head>

<body>
    <div>
        <h1>Log In To Zork</h1>
        <p><?php echo $msg ?></p>

        <form action="login.php" method="POST">
            <label for="username">Username</label>
            <input type="text" name="username" required>

            <label for="firstName">Password</label>
            <input type="password" name="password" required>

            <input type="submit" value="Submit" name="submitBtn" style="margin-top: 15px;">
        </form>

        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>

</html>