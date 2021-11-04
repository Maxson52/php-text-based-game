<?php
// import scenes.php
require('scenes.php');

// ------------- FUNCTIONS -------------
// does the command exist?
function doesCommandExist($command)
{
    global $commands;

    $command = explode(" ", trim(strtolower($command)));

    // check if command is valid where command is an array of all the words, value is each word
    foreach ($command as $key => $value) {
        if (array_key_exists($value, $commands)) {
            return [$value, $commands[$value], $command];
        }
        // if on the final tile, you are entering a pin
        else if ($_SESSION['game_save']['location'] == 15 && $value != "w" && $value != "west") {
            return [$value, ['type' => 'pin'], $command];
        }
        // if command is not valid, return an error
        else if ($key == count($command) - 1) {
            return ['error' => "I don't know what you mean!"];
        }
    }
}
// is the command valid in this location?
function isCommandValid($command)
{
    global $scenes;

    // check for oddball commands
    if ($command[1]['type'] == 'take') {
        return [$command[0], $command[1]['type']];
    } else if ($command[1]['type'] == 'drop') {
        return [$command[0], $command[1]['type']];
    } else if ($command[1]['type'] == 'inventory') {
        return [$command[0], $command[1]['type']];
    } else if ($command[1]['type'] == 'help') {
        return [$command[0], $command[1]['type']];
    } else if ($command[1]['type'] == 'use') {
        return [$command[0], $command[1]['type']];
    } else if ($command[1]['type'] == 'pin') {
        return [$command[0], $command[1]['type']];
    }

    // check if (movement) command is valid
    if (in_array($command[0], $scenes[$_SESSION['game_save']['location']]['commands'])) {
        return [$command[0], $command[1]['type']];
    } else {
        return ['error' => $command[1]['error']];
    }
}

// get location
function getLocation()
{
    global $scenes;

    $location = $_SESSION['game_save']['location'];

    return $scenes[$location];
}
function setLocation($amount)
{
    global $scenes;

    $_SESSION['game_save']['location'] = $_SESSION['game_save']['location'] + $amount;

    return $scenes[$_SESSION['game_save']['location']];
}
function getVertLocation()
{
    return $_SESSION['game_save']['vertLocation'];
}
function setVertLocation($amount)
{
    $_SESSION['game_save']['vertLocation'] = $_SESSION['game_save']['vertLocation'] + $amount;

    return $_SESSION['game_save']['vertLocation'];
}
function getXYZ()
{
    $loc = $_SESSION['game_save']['location'];

    $y = floor($loc / 4);
    $x = $loc % 4;
    $z = getVertLocation();

    return "(" . $x . "," . $y . "," . $z . ")";
}

// motion function
function moveAmount($direction)
{
    // make sure user is on a valid location
    // the first part of the condition is the spots you can move to if you're up or down
    if ($_SESSION['game_save']['location'] != 2 && $_SESSION['game_save']['location'] != 3 && ($_SESSION['game_save']['vertLocation'] == -1 || $_SESSION['game_save']['vertLocation'] == 1)) return "You can't go that way, you aren't on level ground!";

    if ($_SESSION['game_save']['location'] == 13 && $_SESSION['game_save']['isHilly']) return "The landscape is too hilly to continue this way!";
    if ($_SESSION['game_save']['location'] == 14 && $_SESSION['game_save']['doorLocked']) return "The door is locked!";

    if ($direction == 'n' or $direction == 'north') {
        setLocation(-4);
    } elseif ($direction == 's' or $direction == 'south') {
        setLocation(4);
    } elseif ($direction == 'e' or $direction == 'east') {
        setLocation(1);
    } elseif ($direction == 'w' or $direction == 'west') {
        setLocation(-1);
    } else {
        return "There was a problem!";
    }
}

// vertical motion
function moveVertical($direction)
{
    // if they want to go up
    if ($direction == 'u' or $direction == 'up') {
        // check if they are already at the top
        if (getVertLocation() < 1) {
            // then make sure they either can go to z = 1 or they can't
            if (isset(getLocation()['story-up']) || getVertLocation() == -1) {
                setVertLocation(1);
            } else {
                return "You can't go up here!";
            }
        } else {
            return "You can't go up any further!";
        }
        // if they want to go down
    } elseif ($direction == 'd' or $direction == 'down') {
        // check if they are already at the bottom
        if (getVertLocation() > -1) {
            // then make sure they either can go to z = -1 or they can't
            if (isset(getLocation()['story-down']) || getVertLocation() == 1) {
                setVertLocation(-1);
            } else {
                return "You can't go down here!";
            }
        } else {
            return "You can't go down any further!";
        }
    } else {
        return "There was a problem!";
    }
}

// take or grab items
function takeItem($command)
{
    // check if there is an item on this space using get XYZ
    // loop through all the words the user entered
    foreach ($command as $commandValue) {
        // loop through all the items to pick up
        foreach ($_SESSION['game_save']['items'] as $item => $value) {
            // if the item exists in the location
            if (in_array($commandValue, $value['name']) && getXYZ() == $value['pos']) {
                $_SESSION['game_save']['items'][$item]['pos'] = 'i';
                return "You took the " . $item . "!";
            }
            // if the item exists and the user has it
            elseif (in_array($commandValue, $value['name']) && $value['pos'] == 'i') {
                return "You already have the " . $item . "!";
            }
            // if the item exsit but the user isn't at the location
            elseif (in_array($commandValue, $value['name']) && getXYZ() != $value['pos']) {
                return "There is no $item here!";
            }
        }
    }

    // otherwise return an error
    return "You can't pick that up!";
}

function dropItem($command)
{
    // check if there is an item on this space using get XYZ
    // loop through all the words the user entered
    foreach ($command as $commandValue) {
        // loop through all the items to pick up
        foreach ($_SESSION['game_save']['items'] as $item => $value) {
            // if the item exists and the user has it
            if (in_array($commandValue, $value['name']) && $value['pos'] == 'i') {
                $_SESSION['game_save']['items'][$item]['pos'] = getXYZ();
                return "You dropped the " . $item . "!";
            }
            // if the item exists and the user has it
            elseif (in_array($commandValue, $value['name']) && $value['pos'] != 'i') {
                return "You don't have the " . $item . "!";
            }
        }
    }

    // otherwise return an error
    return "You can't do that!";
}

// show inventory
function showInventory()
{
    $inventory = "You have a ";
    foreach ($_SESSION['game_save']['items'] as $item => $value) {
        if ($value['pos'] == 'i') {
            $inventory .= $item . ", ";
        }
    }

    return substr($inventory, 0, -2);
}

// get help
function showHelp()
{
    global $commands;

    $inventory = "";

    foreach ($commands as $command => $value) {
        $inventory .= $command . " - <i>" . $value['description'] . "</i><br>";
    }

    return $inventory;
}

// use item
function useItem($command)
{
    foreach ($command as $item) {
        // loop through all the items to pick up
        foreach ($_SESSION['game_save']['items'] as $itemName => $value) {
            // if the item exists and the user has it
            if (in_array($item, $value['name']) && $value['pos'] == 'i') {
                // if the item is a key
                if ($itemName == 'key' && $_SESSION['game_save']['location'] == 14) {
                    // if the door is locked
                    if ($_SESSION['game_save']['doorLocked']) {
                        $_SESSION['game_save']['doorLocked'] = false;
                        return 'You unlocked the door!';
                    } else {
                        return "The door is already unlocked!";
                    }
                }
                // if the item is a shovel
                else if ($itemName == 'shovel' && $_SESSION['game_save']['location'] == 13) {
                    // if the location is hilly
                    if ($_SESSION['game_save']['isHilly']) {
                        $_SESSION['game_save']['isHilly'] = false;
                        return 'You dug out the hills!';
                    } else {
                        return "You already dug here!";
                    }
                }
                // if the item is a volleyball
                else if ($itemName == 'volleyball') {
                    return 'WILSON - OFFICIAL GAME BALL - PRODUCT NO: 0712';
                } else {
                    return "You can't use the " . $itemName . "!";
                }
            }
        }
    }

    return "You don't have the " . $item . "!";
}

// enter pin
function enterPin($command)
{
    foreach ($command as $pin) {
        // loop through all the items to pick up
        if ($pin == '0712') {
            setLocation(1);
            return 'The pin code you entered is correct';
        } else {
            return 'The pin code you entered is incorrect.';
        }
    }

    return 'There was a problem!';
}
