# Zork: The Island Of The Lost

This is a remake of the original Zork game, created in PHP.

## Database Setup

### Table: users

| user_id | name | username | password |
| ------- | ---- | -------- | -------- |
| 1       | John | johndoe  | hash     |

### Table: game_save

| id  | user_id | location | vertLocation | items | isHilly | doorLocked | energy | gui |
| --- | ------- | -------- | ------------ | ----- | ------- | ---------- | ------ | --- |
| 1   | 4       | 9        | 0            | -     | 1       | 1          | 10     | 0   |

### newUserRegister trigger

```sql
DELIMITER //
CREATE DEFINER = `root`@`localhost` TRIGGER newUserRegister AFTER INSERT ON users
FOR EACH ROW BEGIN
INSERT INTO game_save (`id`, `user_id`, `location`, `vertLocation`, `items`, `isHilly`, `doorLocked`, `energy`)
VALUES (NULL, new.user_id, 9, 0, 'a:4:{s:10:"volleyball";a:2:{s:3:"pos";s:7:"(0,2,0)";s:4:"name";a:2:{i:0;s:10:"volleyball";i:1;s:4:"ball";}}s:6:"shovel";a:2:{s:3:"pos";s:7:"(0,0,0)";s:4:"name";a:1:{i:0;s:6:"shovel";}}s:4:"food";a:2:{s:3:"pos";s:7:"(3,0,1)";s:4:"name";a:3:{i:0;s:4:"food";i:1;s:4:"beef";i:2;s:5:"jerky";}}s:3:"key";a:2:{s:3:"pos";s:8:"(3,1,-1)";s:4:"name";a:1:{i:0;s:3:"key";}}}', 1, 1, 10, 0);
END;;
DELIMITER ;
```

## $\_SESSION Format

Has the following keys:

- user (containing the user's id, name, and username)
- game_save
- - location (0-16)
- - inventory (array of items)
- - vertLocation (-1, 0, or 1)
- - items
- - - an associative array of the items and their pos (such as 1,1,0 or in an inventory) and names (volleyball, ball, etc.)
- - isHilly (whether Craggy Cliff has been dug out)
- - doorLocked (whether the door has been unlocked)

## Functions

### /scripts/commands.php

**`doesCommandExist`** checks if a command exists in the system. It returns the provided command (ex. "w") and the array of the command from `/scripts/scenes.php`. If the command does not exist, it returns an array with an index of `error` and a short error message.

**`isCommandvalid`** checks if the command is valid on the given tile/location in the game. When true, it returns the command and the type of command such as cardinal. When false, it returns an array with an index of `error` and a short error message.

**`getLocation`** returns an array of the current location

**`setLocation`** sets the current location given an increment

**`getVertLocation`** returns vertical location

**`setVertLocation`** sets vertical location

**`getXYZ`** returns the XYZ coordinates of the player

**`moveAmount`** moves the player in the given direction using `setLocation`

**`moveVertical`** moves the player vertically

**`takeItem`** picks up an item from the tile

**`dropItem`** drops an item in the current location

**`showInventory`** displays player inventory

**`showHelp`** lists all main game commands

**`useItem`** uses a given item in the player's inventory

**`enterPin`** enters the pin to win the game in the "safe house"
