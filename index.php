<?php
session_start();
if (isset($_SESSION['user'])) {
    // default vars
    $yourCommand = "";
    $doesCommandExist['error'] = "";
    $isCommandValid[0] = '';
    $isCommandValid[1] = '';
    $commandErrorMsg = '';

    // game vars
    if (!isset($_SESSION['game_save'])) {
        $_SESSION['game_save'] = [];
        $_SESSION['game_save']['location'] = 9;
        $_SESSION['game_save']['vertLocation'] = 0;
        $_SESSION['game_save']['items'] = [
            'volleyball' => ['pos' => '(0,2,0)', 'name' => ['volleyball', 'ball']],
            'shovel' => ['pos' => '(0,0,0)', 'name' => ['shovel']],
            'food' => ['pos' => '(3,0,1)', 'name' => ['food']],
            'key' => ['pos' => '(3,1,-1)', 'name' => ['key']],
        ];
        $_SESSION['game_save']['isHilly'] = true;
        $_SESSION['game_save']['doorLocked'] = true;
    }

    require('scripts/commands.php');

    // all logic in here
    if (isset($_POST['submitBtn'])) {
        $yourCommand = $_POST['command'];
        $doesCommandExist = doesCommandExist($yourCommand);

        // check if user typed in nonsense
        if (!isset($doesCommandExist['error'])) {
            $isCommandValid = isCommandValid($doesCommandExist);
        } else {
            $commandErrorMsg = $doesCommandExist['error'];
        }

        // check if command is not an error
        if (isset($isCommandValid[1])) {
            // move user north, east, south, and west
            if ($isCommandValid[1] == 'cardinal') {
                $commandErrorMsg = moveAmount($isCommandValid[0]);

                // allow user to move two spaces however I don't think this is needed
                if (in_array("2", $doesCommandExist[2]) or in_array("two", $doesCommandExist[2])) {
                    $isCommandValid = isCommandValid($doesCommandExist);

                    if (isset($isCommandValid[1])) {
                        $commandErrorMsg = moveAmount($isCommandValid[0]);
                    }
                }
            }
            // move user vertically
            else if ($isCommandValid[1] == 'vertical') {
                $commandErrorMsg = moveVertical($isCommandValid[0]);
            }
            // pick up item
            else if ($isCommandValid[1] == 'take') {
                $commandErrorMsg = takeItem($doesCommandExist[2]);
            }
            // drop item
            else if ($isCommandValid[1] == 'drop') {
                $commandErrorMsg = dropItem($doesCommandExist[2]);
            }
            // use item
            else if ($isCommandValid[1] == 'use') {
                $commandErrorMsg = useItem($doesCommandExist[2]);
            }
            // see inventory
            else if ($isCommandValid[1] == 'inventory') {
                $commandErrorMsg = showInventory();
            }
            // ask for help
            else if ($isCommandValid[1] == 'help') {
                $commandErrorMsg = showHelp();
            }
            // enter pin
            else if ($isCommandValid[1] == 'pin') {
                $commandErrorMsg = enterPin($doesCommandExist[2]);
            }
        }
        // if the command is a valid command, just not in this spot, tell user
        else {
            $commandErrorMsg = $isCommandValid['error'];
        }

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

        <!-- import type animation -->
        <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>
    </head>

    <body>
        <div class="content">
            <h1>Welcome <?php echo $_SESSION['user']['name'] ?></h1>

            <a href="">Save</a>
            ·
            <a href="">Reset</a>
            ·
            <a href="logout.php">Logout</a>

            <div class="game">
                <?php
                // echo the location of the current location
                echo "<h2 class='location'>" . getLocation()['location'] . "</h2>";

                // echo the coordinates of the user
                echo "<h3 class='coordinates'>" . getXYZ() . "</h3>";

                // foreach storyline of the current location echo the description
                if (getVertLocation() == 1) {
                    $key = $_SESSION['game_save']['location'];
                    $story = implode("<hr>", getLocation()['story-up']);
                } else if (getVertLocation() == -1) {
                    $key = $_SESSION['game_save']['location'];
                    $story = implode("<hr>", getLocation()['story-down']);
                } else {
                    $key = $_SESSION['game_save']['location'];
                    $story = implode("<hr>", getLocation()['story']);
                }

                // then echo the effect
                echo "<p class='story$key'></p>";

                echo
                "<script>
                        let typed$key = new Typed('.story$key', {
                            strings: ['" . $story . "'],
                            typeSpeed: 10,
                            loop: false,
                            showCursor: false
                        });
                    </script>";


                // echo the users command
                echo "> <i>";
                echo $yourCommand ? $yourCommand : "";
                echo "</i><br>";

                // echo the error message
                echo $commandErrorMsg;
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
