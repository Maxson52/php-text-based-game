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

            // if command is not valid, return an error
        } else if ($key == count($command) - 1) {
            return ['error' => "I don't know what you mean!"];
        }
    }
}
// is the command valid in this location?
function isCommandValid($command)
{
    global $scenes;

    if ($command[1]['type'] == 'take') {
        return [$command[0], $command[1]['type']];
    }

    // check if command is valid
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
function takeItem()
{
    // check if there is an item on this space using get XYZ
    if (array_search(getXYZ(), array_column($_SESSION['game_save']['items'], 'pos'))) {
        return "You already have an item!";
    } else {
        return "There's no item here!";
    }
}
