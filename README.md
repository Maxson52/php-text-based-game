# Zork: The Island Of The Lost

This is a remake of the original Zork game, created in PHP.

## Database Setup

| user_id | name | username | password |
| ------- | ---- | -------- | -------- |
| 1       | John | johndoe  | hash     |

## $\_SESSION Format

Has the following keys:

- user (containing the user's id, name, and username)
- game_save
- - location (0-16)
- - inventory (array of items)
- - vertLocation (-1, 0, or 1)

## Functions

### /scripts/commands.php

**`doesCommandExist`** checks if a command exists in the system. It returns the provided command (ex. "w") and the array of the command from `/scripts/scenes.php`. If the command does not exist, it returns an array with an index of `error` and a short error message.

**`isCommandvalid`** checks if the command is valid on the given tile/location in the game. When true, it returns the command and the type of command such as cardinal. When false, it returns an array with an index of `error` and a short error message.

**`getLocation`** returns an array of the current location

**`setLocation`** sets the current location given an increment

**`moveAmount`** moves the player in the given direction using `setLocation`
