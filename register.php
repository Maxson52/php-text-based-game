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
        $msg = "Username taken";
    } else {
        // generate hashed password to put in db
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // insert into db query
        $insertQuery = "INSERT INTO `users` (`user_id`, `name`, `username`, `password`) VALUES (NULL, '$name', '$username', '$hashedPassword')";
        // run query
        mysqli_query($conn, $insertQuery) or die("Query Error: " . mysqli_error($conn));
        // close connection
        header("Location: login.php");
    }

    // close connection
    $_POST = [];
}
?>
<html>

<head>
    <title>Create Account | Zork: The Island Of The Lost</title>

    <link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css"> <!-- import colors -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- import font awesome -->

    <!-- import styles -->
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/form.css">
</head>

<body>
    <div class="content">
        <h1>Create account</h1>
        <p>Already have an account? <a href="login.php">Sign in</a></p>

        <p class="red"><?php echo $msg ?></p>

        <form class="column" action="register.php" method="POST">
            <input type="text" name="name" placeholder="Name" required>

            <input type="text" name="username" placeholder="Username" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="submitBtn">Sign up <i class="fas fa-arrow-right"></i></button>
        </form>
    </div>

</body>

</html>