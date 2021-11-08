<?php
// create associative array of possible commands and their descriptions, errors, and outcomes
$commands = [
    'n' => ['description' => 'You walk north.', 'error' => 'You can\'t go that way.', 'type' => 'cardinal'],
    'e' => ['description' => 'You walk east.', 'error' => 'You can\'t go that way.', 'type' => 'cardinal'],
    's' => ['description' => 'You walk south.', 'error' => 'You can\'t go that way.', 'type' => 'cardinal'],
    'w' => ['description' => 'You walk west.', 'error' => 'You can\'t go that way.', 'type' => 'cardinal'],
    'north' => ['description' => 'You walk north.', 'error' => 'You can\'t go that way.',   'type' => 'cardinal'],
    'east' => ['description' => 'You walk east.', 'error' => 'You can\'t go that way.', 'type' => 'cardinal'],
    'south' => ['description' => 'You walk south.', 'error' => 'You can\'t go that way.', 'type' => 'cardinal'],
    'west' => ['description' => 'You walk west.', 'error' => 'You can\'t go that way.', 'type' => 'cardinal'],
    'go' => ['description' => 'You go somewhere.', 'error' => 'You can\'t go that way.', 'type' => 'go'],
    'g' => ['description' => 'You go somewhere.', 'error' => 'You can\'t go that way.', 'type' => 'go'],
    'up' => ['description' => 'You climb up.', 'error' => 'You can\'t go that way.', 'type' => 'vertical'],
    'u' => ['description' => 'You climb up.', 'error' => 'You can\'t go that way.', 'type' => 'vertical'],
    'climb' => ['description' => 'You climb up.', 'error' => 'You can\'t go that way.', 'type' => 'vertical'],
    'down' => ['description' => 'You go down.', 'error' => 'You can\'t go that way.', 'type' => 'vertical'],
    'd' => ['description' => 'You go down.', 'error' => 'You can\'t go that way.', 'type' => 'vertical'],
    'take' => ['description' => 'You take the item.', 'error' => 'You can\'t take that.', 'type' => 'take'],
    'grab' => ['description' => 'You take the item.', 'error' => 'You can\'t take that.', 'type' => 'take'],
    'drop' => ['description' => 'You drop the item.', 'error' => 'You can\'t drop that.', 'type' => 'drop'],
    'use' => ['description' => 'You use an item in your inventory.', 'error' => 'You can\'t use that here.', 'type' => 'use'],
    'eat' => ['description' => 'You eat food.', 'error' => 'You can\'t use that here.', 'type' => 'use'],
    'dig' => ['description' => 'You dig.', 'error' => 'You can\'t use that here.', 'type' => 'use'],
    'unlock' => ['description' => 'You unlock a door.', 'error' => 'You can\'t use that here.', 'type' => 'use'],
    'energy' => ['description' => 'You notice your energy levels.', 'error' => '', 'type' => 'energy'],
    'inventory' => ['description' => 'You access your inventory.', 'error' => '', 'type' => 'inventory'],
    'i' => ['description' => 'You access your inventory.', 'error' => '', 'type' => 'inventory'],
    'gui' => ['description' => 'Toggle the GUI for easier game play.', 'error' => '', 'type' => 'gui'],
    'help' => ['description' => 'How\'d you get here?', 'error' => '', 'type' => 'help'],
    'h' => ['description' => 'How\'d you get here?', 'error' => '', 'type' => 'help'],
    '?' => ['description' => 'How\'d you get here?', 'error' => '', 'type' => 'help'],
    'hint' => ['description' => 'You receive a helpful hint.', 'error' => '', 'type' => 'hint'],
];

// create energy level descriptions
$energy_levels = [
    'low' => ['You are low on energy.', 'You must eat soon.', 'Your energy is getting too low to continue.'],
    'medium' => ['You have a medium level of energy.', 'Careful, you are losing energy fast.', 'Your energy level is depleting.'],
    'high' => ['You are high on energy.', 'You have enough energy to last a lifetime.', 'With this kind of energy, you are ready for anything!'],
];

// make hints for the player
$hints = [
    'The south-east section of the ocean a key glimmers at the floor of the water.',
    'A shovel may come in handy if the terrain is too difficult to continue.',
    'Any sign of life could be crucial. Check for any forgotten items that might prove to be useful.',
    'Watch out, using too much energy is the difference between finding a way out and not.',
    'The north-west-ward section of the island seems to have the best sand for sandcastles.',
    'Quicksand is dangerous stuff, be sure to steer clear of it.',
    'This island was once populated by many, surely there is something they have left for you.'
];

// create a scenes array with 16 scenes
// each scene has a location, array of commands, and array of storylines
$scenes = array();

function setScene()
{
    global $scenes;

    $scenes[0] = array(
        'location' => 'The Beach',
        'commands' => array('e', 's', 'east', 'south',),
        'story' => ($_SESSION['game_save']['items']['shovel']['pos'] == "(0,0,0)") ? array('Sand between your toes, the beach is a delightful place.', 'Around, you see a forest as well as an ocean with waves crashing.', 'It seems like a sandcastle was once made here.', 'Beside it, there lies a shovel, half buried.') : array('Sand between your toes, the beach is a delightful place.', 'Around, you see a forest as well as an ocean with waves crashing.', 'It seems like a sandcastle was once made here.', 'There was recently a shovel stuck in the ground.')
    );
    $scenes[1] = array(
        'location' => 'The Ocean',
        'commands' => array('w', 's', 'e', 'west', 'south', 'east',),
        'story' => array('Waves crashing into your body, the water is refreshing and clear as day.', 'Out a little further is a boat, the first sign of life. The beach right beside.', 'Turning back now means you might not have a chance to return.')
    );
    $scenes[2] = array(
        'location' => 'The Boat',
        'commands' => array('w', 'west', 's', 'south', 'e', 'east', 'u', 'up', 'd', 'down'), // climb on to boat
        'story' => array("The waves are more aggressive out here. You can not stay for long.", "The boat is within arms reach. It is bigger than it seemed, you can only hope it is your ticket out.", "Within reach is a ladder to climb up to the deck of the boat."),
        'story-up' => array('You climb up the ladder.', 'The boat is empty, not a soul around.', 'It seems nobody has been here in ages, the boat has drifted towards land.', 'Ocean surrounds you, but you can make out the shoreline from which you came.')
    );
    $scenes[3] = array(
        'location' => 'The Boat',
        'commands' => array('w', 'west', 's', 'south'), // grab the food
        'story' => array('The boat is right beside you.', 'But the ladder to get up is too far to climb from here.', 'You must stay near the boat to stay afloat, time is of the essence.'),
        'story-up' => ($_SESSION['game_save']['items']['food']['pos'] == "(3,0,1)") ? array("Water on all sides, the boat is not a good place to stay.", 'Towards the bow of the ship, you see some delicious beef jerky.', 'It will provide enough energy to get through the day.') : array("Water on all sides, the boat is not a good place to stay.", "You have already taken the food left on the boat.")
    );
    $scenes[4] = array(
        'location' => 'The Forest',
        'commands' => array('n', 'e', 's', 'north', 'east', 'south', 'up', 'u', 'down', 'd'), // climb up tree
        'story' => array('The forest is lush and green.', 'The sounds of the ocean still echo in your ear.', 'A large tree stands right by you, maybe it could be a vantage point?'),
        'story-up' => array("You climb up the tree.", "The tree is so tall, you can not see the bottom.", 'You see but a boat in the water.', 'Out of the corner of your eye, you notice a small tent below you, maybe it could be useful.')
    );
    $scenes[5] = array(
        'location' => 'The Beach',
        'commands' => array('n', 'e', 's', 'w', 'north', 'east', 'south', 'west'),
        'story' => array('Remember the days when you would relax on the beach, make sandcastles, have a blast?', 'You notice a forest to the west, and the beach on the other sides.', 'The sounds of the ocean resonate in your ear, it can only get more difficult from here.')
    );
    $scenes[6] = array(
        'location' => 'The Ocean',
        'commands' => array('n', 'e', 's', 'w', 'north', 'east', 'south', 'west'),
        'story' => array("Seagulls squawk and the water ripples.", "A ways deeper, a boat floats. It seems you have found people.", 'The sounds of the water rippling is soothing, but there is no time for rest.')
    );
    $scenes[7] = array(
        'location' => 'The Ocean',
        'commands' => array('w', 'west', 's', 'south', 'u', 'up', 'd', 'down'), // grab the key (should it require a take/grab or only a down/d?)
        'story' => ($_SESSION['game_save']['items']['key']['pos'] == "(3,1,-1)") ? array('As you drift further from the boat, a glimmer of light catches your eye.', 'You see a small key on the ground, at least 15 feet below the surface.') : array('Out in the blue of the ocean, a key was once lying on the floor of the sea in this exact spot.'),
        'story-down' => ($_SESSION['game_save']['items']['key']['pos'] == "(3,1,-1)") ? array('You dive down, as much air in your lungs as you can hold.', 'Your vision gets worse and your ears start to ring.', 'You see the glimmer of the key and jolt towards it.') : array('You dive down, as much air in your lungs as you can hold.', 'Your vision gets worse and your ears start to ring.', 'There is nothing down here anymore.')
    );
    $scenes[8] = array(
        'location' => 'The Tent',
        'commands' => array('n', 'north', 's', 'south'), // go inside tent and find volleyball
        'story' => array('Deep in the forest, the sounds of the ocean become increasingly more muted.', 'Another sign of humans lies in front of you.', 'A tent.')
    );
    $scenes[9] = array(
        'location' => 'The Beach (Start)',
        'commands' => array('n', 'e', 's', 'north', 'east', 'south'),
        'story' => array('After a horrible accident, you find yourself alone on an island.', 'Survival relies on you finding food, and a way out.', 'After a brief inspection of the area, you notice a forest to the west and an ocean to the north-east.')
    );
    $scenes[10] = array(
        'location' => 'The Beach',
        'commands' => array('n', 'e', 'w', 'north', 'east', 'west'),
        'story' => array('Is sand edible? You ask yourself, wondering how you will get out of this mess.', 'The ocean is a dangerous place. And the forest seems impenetrable from here.', 'The sand seems to change colour to the east, could it be a sign?', 'The water glimmers from the north, beautiful and blinding.')
    );
    $scenes[11] = array(
        'location' => 'QUICKSAND',
        'commands' => array('w', 'west'), // the only way out is to have the shovel and use it
        'story' => array("Suddenly, the ground seemingly start to rise.", "You have found yourself stuck in a patch of quicksand.", "Why did you come this way? You ask yourself.", "You twist and turn and finally manage to escape, but it was far from easy.")
    );
    $scenes[12] = array(
        'location' => 'The Swamp',
        'commands' => array('n', 'e', 'north', 'east'),
        'story' => array('A muddy mess.', 'Your legs are covered in mud, the water could clean you.', 'You see but a hilly valley to the east. To the north is more forest.')
    ); // ended here
    $scenes[13] = array(
        'location' => $_SESSION['game_save']['isHilly'] ? 'Craggy Cliff' : '<strike>Craggy Cliff</strike> Vacant Valley',
        'commands' => array('n', 'e', 'w', 'north', 'east', 'west'), // use shovel to dig rocks
        'story' => ($_SESSION['game_save']['isHilly']) ? array('A craggy cliff lies upon you.', 'It seems rocks and mounds of dirt block the path.', 'Stomping on the dirt reveals it is loosely compacted and could be dug out. The rocks can be hauled away.') : array('The path has opened up, but at what cost?', 'You have expended energy digging and hauling away the dirt and rocks.', 'Was it worth it?'),
    );
    $scenes[14] = array(
        'location' => 'The Tunnel',
        'commands' => array('e', 'w', 'east', 'west'),
        'story' => ($_SESSION['game_save']['doorLocked']) ? array('A door with a small window presents itself right in front of you.', 'Through the window, you see a long, dark tunnel ahead.', 'Nothing around you has seemed useful. This may be your last chance.') : array('A door with a small window presents itself right in front of you.', 'You slowly open the door.', 'Ahead is a long, dark tunnel.'),
    );
    $scenes[15] = array(
        'location' => 'The Safe House',
        'commands' => array('w', 'west'), // enter pin code to win
        'story' => array("It is almost as if you have entered an abandoned safe house.", "On the wall you notice a keypad.", '<span id="pin">Enter pin: </span>')
    );
    $scenes[16] = array(
        'location' => 'The End',
        'commands' => array(), // win
        'story' => array('The walls start screeching. They lower into the floor to reveal a fridge full of fresh food, a pantry with food to last a lifetime, and a phone to call for help.', 'You have found a way out...')
    );
}

setScene();
