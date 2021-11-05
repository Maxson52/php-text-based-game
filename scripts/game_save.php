<?php
require('scripts/conn.php');

// save game_save to database
function saveGame()
{
    global $conn;

    $uid = $_SESSION['user']['user_id'];
    $location = $_SESSION['game_save']['location'];
    $vertLocation = $_SESSION['game_save']['vertLocation'];
    $items = serialize($_SESSION['game_save']['items']);
    $hill = $_SESSION['game_save']['isHilly'] ? 1 : 0;
    $door = $_SESSION['game_save']['doorLocked'] ? 1 : 0;
    $energy = $_SESSION['game_save']['energy'];

    // create and perform update query
    $query = "UPDATE `game_save` SET `location` = '$location', `vertLocation` = '$vertLocation', `items` = '$items', `isHilly` = '$hill', `doorLocked` = '$door', `energy` = '$energy' WHERE `game_save`.`user_id` = $uid";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn) . "BADBAD");

    // return result
    return $result;
}

// reset game_save to defaults
function resetGame()
{
    $_SESSION['game_save'] = [];
    $_SESSION['game_save']['location'] = 9;
    $_SESSION['game_save']['vertLocation'] = 0;
    $_SESSION['game_save']['items'] = [
        'volleyball' => ['pos' => '(0,2,0)', 'name' => ['volleyball', 'ball']],
        'shovel' => ['pos' => '(0,0,0)', 'name' => ['shovel']],
        'food' => ['pos' => '(3,0,1)', 'name' => ['food', 'beef', 'jerky']],
        'key' => ['pos' => '(3,1,-1)', 'name' => ['key']],
    ];
    $_SESSION['game_save']['isHilly'] = true;
    $_SESSION['game_save']['doorLocked'] = true;
    $_SESSION['game_save']['energy'] = 10;
    $_SESSION['game_save']['gui'] = false;

    saveGame();
}

// import game_save from database
function loadGame()
{
    global $conn;

    $uid = $_SESSION['user']['user_id'];

    $query = "SELECT * FROM `game_save` WHERE `user_id` = $uid";
    $result = mysqli_query($conn, $query) or die(mysqli_error($conn) . "BADBAD");

    // for each column, set the value of the game_save array
    while ($row = mysqli_fetch_array($result)) {
        $_SESSION['game_save']['location'] = $row['location'];
        $_SESSION['game_save']['vertLocation'] = $row['vertLocation'];
        $_SESSION['game_save']['items'] = unserialize($row['items']);
        $_SESSION['game_save']['isHilly'] = $row['isHilly'] == 1 ? true : false;
        $_SESSION['game_save']['doorLocked'] = $row['doorLocked'] == 1 ? true : false;
        $_SESSION['game_save']['energy'] = $row['energy'];
        $_SESSION['game_save']['gui'] = $row['gui'] == 1 ? true : false;
    }
}
