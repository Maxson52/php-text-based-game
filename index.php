<?php
session_start();
if (isset($_SESSION['user'])) {
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Index</title>

        <!-- import axist css cdn from unpkg -->
        <link rel="stylesheet" href="https://unpkg.com/axist@latest/dist/axist.min.css" />
    </head>

    <body>
        <h1>Welcome <?php echo $_SESSION['user']['name'] ?></h1>

        <a href="logout.php">Logout</a>
    </body>

    </html>
<?php
} else {
    header('Location: login.php');
}
