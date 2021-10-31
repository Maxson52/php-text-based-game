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
    <title>Sign In | Zork: The Island Of The Lost</title>

    <link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css"> <!-- import colors -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- import font awesome -->

    <!-- import auth.css -->
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>
    <div>
        <h1>Sign in to continue</h1>
        <p>Don't have an account? <a href="register.php">Register</a></p>

        <p class="red"><?php echo $msg ?></p>

        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="submitBtn">Sign in <i class="fas fa-arrow-right"></i></button>
        </form>
    </div>
</body>

</html>