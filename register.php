<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zork";

$msg = "";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());

// check if _POST is set then set up sql connection and query
if (isset($_POST['submitBtn'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $selectQuery = "SELECT * FROM `users` WHERE `username`='$username'";
    $users = mysqli_query($conn, $selectQuery) or die("Query failed: " . mysqli_error($conn));
    if (mysqli_num_rows($users) > 0) {
        // if username already exists, throw error
        $msg = "Username already exists";
    } else {
        // generate hashed password to put in db
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // insert into db query
        $insertQuery = "INSERT INTO `users` (`user_id`, `name`, `username`, `password`) VALUES (NULL, '$name', '$username', '$hashedPassword')";
        // run query
        mysqli_query($conn, $insertQuery) or die("Query Error: " . mysqli_error($conn));
        // close connection
        $msg = "Registration successful";
    }

    // close connection
    $_POST = [];
}
?>
<html>

<head>
    <title>Sign Up For Zork</title>
    <link rel="stylesheet" href="https://unpkg.com/axist@latest/dist/axist.min.css" />
</head>

<body>
    <div>
        <h1>Sign Up For Zork</h1>
        <p><?php echo $msg ?></p>

        <form action="register.php" method="POST">
            <label for="name">Name</label>
            <input type="text" name="name" required>

            <label for="username">Username</label>
            <input type="text" name="username" required>

            <label for="firstName">Password</label>
            <input type="password" name="password" required>

            <input type="submit" value="Submit" name="submitBtn" style="margin-top: 15px;">
        </form>
    </div>

    <p>Already have an account? <a href="login.php">Log In</a></p>

</body>

</html>