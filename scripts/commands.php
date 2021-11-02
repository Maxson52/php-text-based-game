<?php
// import scenes.php
require('scenes.php');

// functions
function onCommand($command) {
    global $commands;

    $command = explode(" ", trim(strtolower($command)));

    // check if command is valid
    foreach ($command as $key=>$value) {
        if (array_key_exists($value, $commands)) {
            return $commands[$value];
        } else if ($key == count($command) - 1) {
            return "I don't know what you mean!";
        }
    }
}

function move($direction)
{
    if ($direction == 'n' or $direction == 'north') {
        return -5;
    } elseif ($direction == 's' or $direction == 'south') {
        return 5;
    } elseif ($direction == 'e' or $direction == 'east') {
        return 1;
    } elseif ($direction == 'w' or $direction == 'west') {
        return -1;
    } else {
        return 0;
    }
}
?>