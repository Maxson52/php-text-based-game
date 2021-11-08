<?php
session_start();
if (isset($_SESSION['user'])) {
    require('scripts/game_save.php');
    require('scripts/commands.php');

    // default vars
    $yourCommand = "";
    $doesCommandExist['error'] = "";
    $isCommandValid[0] = '';
    $isCommandValid[1] = '';
    $commandErrorMsg = '';
    $energyRes = $_SESSION['game_save']['location'] == 9 && $_SESSION['game_save']['energy'] == 10 ? 'Play by typing in the command box. Type <i>help</i> or <i>?</i> for help.' : getEnergy(0);

    // save or reset game
    if (isset($_GET['fn'])) {
        if ($_GET['fn'] == "reset") {
            resetGame();
        } else if ($_GET['fn'] == "save") {
            saveGame();
        }

        $url = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], "?"));
        header("Location: " . $url);
    }

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
                $energyRes = randomEnergyLoss();

                // allow user to move two spaces however I don't think this is needed
                if (in_array("2", $doesCommandExist[2]) or in_array("two", $doesCommandExist[2])) {
                    $isCommandValid = isCommandValid($doesCommandExist);

                    if (isset($isCommandValid[1])) {
                        $commandErrorMsg = moveAmount($isCommandValid[0]);
                        $energyRes = randomEnergyLoss();
                    }
                }
            }
            // move user vertically
            else if ($isCommandValid[1] == 'vertical') {
                $commandErrorMsg = moveVertical($isCommandValid[0]);
                $energyRes = randomEnergyLoss();
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
                $energyRes = randomEnergyLoss();
            }
            // see inventory
            else if ($isCommandValid[1] == 'inventory') {
                $commandErrorMsg = showInventory(false);
            }
            // ask for help
            else if ($isCommandValid[1] == 'help') {
                $commandErrorMsg = showHelp();
            }
            // check energy
            else if ($isCommandValid[1] == 'energy') {
                $commandErrorMsg = getEnergy(false);
            }
            // go
            else if ($isCommandValid[1] == 'go') {
                $commandErrorMsg = go($doesCommandExist[2]);
                $energyRes = randomEnergyLoss();
            }
            // enter pin
            else if ($isCommandValid[1] == 'pin') {
                $commandErrorMsg = enterPin($doesCommandExist[2]);
            }
            // toggle gui
            else if ($isCommandValid[1] == 'gui') {
                $commandErrorMsg = toggleGui();
            }
            // get hint
            else if ($isCommandValid[1] == 'hint') {
                $commandErrorMsg = getHint();
            }
        }
        // if the command is a valid command, just not in this spot, tell user
        else {
            $commandErrorMsg = $isCommandValid['error'];
        }

        // refresh scene (because of ternary operators that are used to show if there is an item on a spot)
        setScene();

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

        <!-- import favicon -->
        <link rel="icon" href="https://cdn-0.emojis.wiki/emoji-pics/microsoft/desert-island-microsoft.png" type="image/x-icon">

        <link rel=stylesheet href="https://s3-us-west-2.amazonaws.com/colors-css/2.2.0/colors.min.css"> <!-- import colors -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> <!-- import font awesome -->

        <!-- import styles -->
        <link rel="stylesheet" href="css/global.css">
        <link rel="stylesheet" href="css/game.css">
        <link rel="stylesheet" href="css/form.css">

        <!-- import type animation -->
        <script src="https://unpkg.com/typewriter-effect@latest/dist/core.js"></script>

    </head>

    <body>
        <div class="content set-width">
            <h1>Welcome <?php echo $_SESSION['user']['name'] ?></h1>

            <a href="?fn=save">Save</a>
            ·
            <a href="?fn=reset" onclick="return confirm('Are you sure you want to reset all progress?')">Reset</a>
            ·
            <a href="logout.php" onclick="return confirm('Are you sure you want sign out (all progress will be saved)?')">Sign out</a>

            <div class="game">
                <?php
                // echo the location of the current location
                echo "<h2 class='location'>" . getLocation()['location'] . "</h2>";

                if ($_SESSION['game_save']['gui']) {
                    echo "<div class='gui'>";
                    echo "<h3>" . getXYZ() . " - <span class='backpack'>🎒</span><span class='inventory'></span> - " . getEnergy(true) . "</h3>";
                    echo "</div>";
                }

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

                // kinda slow
                echo
                "<script>
                        let type$key = new Typewriter('.story$key', {
                            loop: false,
                            cursor: '',
                            delay: 0.1,
                        });

                        type$key.typeString('" . $story . "<hr>" . $energyRes . "').start();
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
                <input type="text" name="command" placeholder="Enter a command" autocomplete="off" required autofocus <?php echo $_SESSION['game_save']['energy'] <= 0 || $_SESSION['game_save']['location'] == 16 ? "disabled" : "" ?>>
                <button type="submit" name="submitBtn" <?php echo $_SESSION['game_save']['energy'] <= 0 || $_SESSION['game_save']['location'] == 16 ? "disabled" : "" ?>><i class="fas fa-arrow-right"></i></button>
            </form>
        </div>

        <!-- audio -->
        <embed src="assets/backgroundMusic.mp3" loop="true" autostart="true" width="2" height="0">

        <!-- code for inventory hover -->
        <?php if ($_SESSION['game_save']['gui']) { ?>
            <script>
                let backpack = document.querySelector('.backpack');
                let inventory;

                backpack.addEventListener('mouseover', () => {

                    inventory = new Typewriter('.inventory', {
                        loop: false,
                        cursor: '',
                        delay: 5,

                    });

                    inventory.typeString('<?php echo showInventory(true) ?>').start();
                });

                backpack.addEventListener('mouseout', function() {
                    inventory.deleteAll(5).start();
                });
            </script>
        <?php } ?>
    </body>

    </html>
<?php
} else {
    header('Location: login.php');
}
