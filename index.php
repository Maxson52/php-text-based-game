<?php
session_start();
if (isset($_SESSION['user'])) {
    // all logic in here
    require('scripts/commands.php');

    if (isset($_POST['submitBtn'])) {
        onCommand($_POST['command']);


        
        // empty $_POST array
        $_POST = [];
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Zork: The Island Of The Lost</title>

        <link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css"> <!-- import colors -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- import font awesome -->

        <!-- import styles -->
        <link rel="stylesheet" href="css/global.css">
        <link rel="stylesheet" href="css/game.css">
        <link rel="stylesheet" href="css/form.css">
    </head>

    <body>
        <div class="content">
            <h1>Welcome <?php echo $_SESSION['user']['name'] ?></h1>

            <a href="logout.php">Logout</a>

            <div class="game">
                <?php
                for ($i = 0; $i < 2; $i++) {
                    echo "<span>Hi my fav number is " . rand(1, 100) . "</span>";
                }
                ?>
            </div>

            <form id="commandForm" class="row" action="index.php" method="POST">
                <input type="text" name="command" placeholder="Enter a command" autocomplete="off" autofocus>
                <button type="submit" name="submitBtn"><i class="fas fa-arrow-right"></i></button>
            </form>
        </div>

    </body>

    </html>
<?php
} else {
    header('Location: login.php');
}
