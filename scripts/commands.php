<?php
// import scenes.php
require('scenes.php');

// ------------- FUNCTIONS -------------
// does the command exist?
function doesCommandExist($command)
{
    global $commands;

    $command = explode(" ", trim(strtolower($command)));

    // check if command is valid
    foreach ($command as $key => $value) {
        if (array_key_exists($value, $commands)) {
            return [$value, $commands[$value], $command];
        } else if ($key == count($command) - 1) {
            return ['error' => "I don't know what you mean!"];
        }
    }
}
// is the command valid in this location?
function isCommandValid($command)
{
    global $scenes;

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
function getXY()
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
    if ($direction == 'u' or $direction == 'up') {
        if (getVertLocation() < 1) {
            setVertLocation(1);
        } else {
            return "You can't go up any further!";
        }
    } elseif ($direction == 'd' or $direction == 'down') {
        if (getVertLocation() > -1) {
            setVertLocation(-1);
        } else {
            return "You can't go down any further!";
        }
    } else {
        return "There was a problem!";
    }
}
