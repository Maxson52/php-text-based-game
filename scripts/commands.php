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

    // check if command is a movement command (because it is NOT valid in all locations) otherwise do do normal thing
    if ($command[1]['type'] == 'cardinal' || $command[1]['type'] == 'vertical') {
        if (in_array($command[0], $scenes[$_SESSION['game_save']['location']]['commands'])) {
            return [$command[0], $command[1]['type']];
        } else {
            return ['error' => $command[1]['error']];
        }
    } else {
        return [$command[0], $command[1]['type']];
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
    if ($_SESSION['game_save']['location'] == 11) energyLoss(rand(1, 3));

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
    // allow user to only take two items at a time
    $inventory = [];

    foreach ($_SESSION['game_save']['items'] as $item => $value) {
        if ($value['pos'] == 'i') {
            $inventory[] = $item;
        }
    }

    if (count($inventory) >= 2) {
        return "You can only carry two items at a time!";
    }

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
            // if the item exists but the user isn't at the location
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
function showInventory($emoji)
{
    $inventory = [];

    foreach ($_SESSION['game_save']['items'] as $item => $value) {
        if ($value['pos'] == 'i') {
            $inventory[] = $item;
        }
    }

    if (empty($inventory)) {
        return "You have nothing in your inventory!";
    } else {
        if ($emoji) {
            $inventory = str_replace('key', '🔑', $inventory);
            $inventory = str_replace('shovel', '⛏️', $inventory);
            $inventory = str_replace('volleyball', '🏐', $inventory);
            $inventory = str_replace('food', '🥩', $inventory);

            return "You have a " . implode(", ", $inventory);
        } else {
            return "You have a " . implode(", a ", $inventory);
        }
    }
}

// get help
function showHelp()
{
    global $commands;

    $inventory = "";

    foreach ($commands as $command => $value) {
        $inventory .= $command . " - <i>" . $value['description'] . "</i><br>";
    }

    return "Think critically to get off the island. Use all the tools you find, notice clues, and read carefully to make it off the island. <br> Enter any of the following commands to play: <br> " . $inventory;
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
                }
                // if the item is food
                else if ($itemName == 'food') {
                    energyLoss(rand(-3, -1));
                    // remove food from inventory
                    $_SESSION['game_save']['items'][$itemName]['pos'] = '';
                    // return message
                    return 'Delicious! ' . getEnergy(false);
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

// handle energy loss
function energyLoss($loss)
{
    global $energy_levels;

    $_SESSION['game_save']['energy'] -= $loss;

    if (1 <= $_SESSION['game_save']['energy'] && $_SESSION['game_save']['energy']  <= 3) {
        return $energy_levels['low'][rand(0, 2)];
    } else if (4 <= $_SESSION['game_save']['energy'] && $_SESSION['game_save']['energy'] <= 6) {
        return $energy_levels['medium'][rand(0, 2)];
    } else if (7 <= $_SESSION['game_save']['energy'] && $_SESSION['game_save']['energy'] <= 10) {
        return $energy_levels['high'][rand(0, 2)];
    } else if ($_SESSION['game_save']['energy'] <= 0) {
        return "You ran out of energy ⚡! Game over!";
    } else if ($_SESSION['game_save']['energy'] >= 11) {
        $_SESSION['game_save']['energy'] = 10;
        return $energy_levels['high'][rand(0, 2)];
    }

    return "There was a problem!";
}

// randomize energy loss every move
function randomEnergyLoss()
{
    $loss = rand(0, 1);

    return energyLoss($loss);
}

// get energy 
function getEnergy($shortForm)
{
    if ($shortForm) {
        return $_SESSION['game_save']['energy'] . "⚡";
    } else {
        return "You have " . $_SESSION['game_save']['energy'] . " energy ⚡ points left.";
    }
}

// go somewhere
function go($userCommands)
{
    global $commands;

    if ($_SESSION['game_save']['location'] == 8) {
        return "You peek inside the tent. Inside is nothing but an old volleyball.";
    } else {
        foreach ($userCommands as $word) {
            if ($word == 'go') continue;

            $valid = isCommandValid([$word, $commands[$word], $userCommands]);

            if (!isset($valid['error'])) {
                if ($valid[1] == 'cardinal') {
                    return moveAmount($word);
                } else if ($valid[1] == 'vertical') {
                    return moveVertical($word);
                }
            }
        }
    }

    return "You can't go there!";
}

// toggle gui
function toggleGui()
{
    $_SESSION['game_save']['gui'] = !$_SESSION['game_save']['gui'];
}
