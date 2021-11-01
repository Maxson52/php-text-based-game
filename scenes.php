<?php
// functions
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

// create assosiative array of possible moves and their descriptions, errors, and outcomes
$moves = [
    'n' => ['description' => 'You walk north.', 'error' => 'You can\'t go that way.', 'outcome' => move('n')],
    'e' => ['description' => 'You walk east.', 'error' => 'You can\'t go that way.', 'outcome' => move('e')],
    's' => ['description' => 'You walk south.', 'error' => 'You can\'t go that way.', 'outcome' => move('s')],
    'w' => ['description' => 'You walk west.', 'error' => 'You can\'t go that way.', 'outcome' => move('w')],
    'north' => ['description' => 'You walk north.', 'error' => 'You can\'t go that way.', 'outcome' => move('n')],
    'east' => ['description' => 'You walk east.', 'error' => 'You can\'t go that way.', 'outcome' => move('e')],
    'south' => ['description' => 'You walk south.', 'error' => 'You can\'t go that way.', 'outcome' => move('s')],
    'west' => ['description' => 'You walk west.', 'error' => 'You can\'t go that way.', 'outcome' => move('w')],
    'up' => ['description' => 'You climb up.', 'error' => 'You can\'t go that way.'],
    'u' => ['description' => 'You climb up.', 'error' => 'You can\'t go that way.'],
    'down' => ['description' => 'You go down.', 'error' => 'You can\'t go that way.'],
    'd' => ['description' => 'You go down.', 'error' => 'You can\'t go that way.'],
    'take' => ['description' => 'You take the item.', 'error' => 'You can\'t take that.'],
    'grab' => ['description' => 'You take the item.', 'error' => 'You can\'t take that.'],
    'drop' => ['description' => 'You drop the item.', 'error' => 'You can\'t drop that.'],
    'look' => ['description' => 'You look around.', 'error' => ''],
    'see' => ['description' => 'You look around.', 'error' => ''],
    'inventory' => ['description' => 'You access your inventory.', 'error' => ''],
    'i' => ['description' => 'You access your inventory.', 'error' => ''],
    'help' => ['description' => 'How\'d you get here?', 'error' => ''],
    'h' => ['description' => 'How\'d you get here?', 'error' => ''],
];

// create a scenes array with 16 scenes
// each scene has a location, array of moves, and array of storylines
$scenes = array();
$scenes[0] = array(
    'location' => 'The Beach',
    'moves' => array('e', 's', 'east', 'south',),
    'story' => array('Sand between your toes, the beach is a delightful place.', 'Around, you see a forest and a ocean with waves crashing.')
);
$scenes[1] = array(
    'location' => 'The Ocean',
    'moves' => array('w', 's', 'e', 'west', 'south', 'east',),
    'story' => array('Waves crashing into your body, the water is refreshing and clear as day.', 'Out a little further is a boat, the first sign of life. The beach right beside.')
);
$scenes[2] = array(
    'location' => 'The Boat',
    'moves' => array('w', 'west', 's', 'south', 'e', 'east', 'u', 'up', 'd', 'down'), // climb on to boat
    'story' => array('The boat is within arms reach, a ladder is there to climb.'),
    'story-up' => array('You climb up the ladder.', 'The boat is empty, not a soul around.', 'Ocean surrounds you, but you can make out the shoreline from which you came.')
);
$scenes[3] = array(
    'location' => 'The Boat',
    'moves' => array('w', 'west'), // grab the food
    'story' => array('Water on all sides, the boat isn\'t a good place to stay.', 'Towards the bow of the ship, you see some delicious beef jerky.')
);
$scenes[4] = array(
    'location' => 'The Forest',
    'moves' => array('n', 'e', 's', 'north', 'east', 'south', 'up', 'u', 'down', 'd'), // climb up tree
    'story' => array('The forest is lush and green.', 'The sounds of the ocean still echo in your ear.', 'A large tree stands right by you, maybe it could be a vantage point?'),
    'story-up' => array('You climb up the tree.', 'The tree is so tall, you can\'t see the bottom.', 'You see but a boat in the water.')
);
$scenes[5] = array(
    'location' => 'The Beach',
    'moves' => array('n', 'e', 's', 'w', 'north', 'east', 'south', 'west'),
    'story' => array('Remember the days when you would relax on the beach, make sandcastles, have a blast?', 'You notice a forest to the west, and the beach on the other sides.')
);
$scenes[6] = array(
    'location' => 'The Ocean',
    'moves' => array('n', 'e', 's', 'w', 'north', 'east', 'south', 'west'),
    'story' => array('Seagulls squawk and the water ripples.', 'A ways deeper, a boat floats. It seems you\'ve found people.')
);
$scenes[7] = array(
    'location' => 'The Ocean',
    'moves' => array('w', 'west', 's', 'south', 'e', 'east', 'u', 'up', 'd', 'down'), // grab the key (should it require a take/grab or only a down/d?)
    'story' => array('As you drift further from the boat, a glimmer of light catches your eye.', 'You see a small key on the ground, at least 15ft below the surface.'),
    'story-down' => array('You dive down, as much air in your lungs as you can hold.', 'Your vision gets worse and your ears start to ring.', 'You snag the key and jolt up to the surface.')
);
$scenes[8] = array(
    'location' => 'The Tent',
    'moves' => array('n', 'north', 's', 'south'), // go inside tent and find volleyball
    'story' => array('Deep in the forest, the sounds of the ocean become increasingly more muted.', 'Another sign of humans lies in front of you.', 'A tent.')
);
$scenes[9] = array(
    'location' => 'The Beach (Start)',
    'moves' => array('n', 'e', 'north', 'east'),
    'story' => array('After a horrible accident, you find yourself alone on an island.', 'Survival relies on you finding food, and a way out.')
);
$scenes[10] = array(
    'location' => 'The Beach',
    'moves' => array('n', 'e', 'w', 'north', 'east', 'west'),
    'story' => array('Is sand edible? You ask yourself, wondering how you will get out of this mess.', 'The ocean is a dangerous place. And the forest seems impenetrable from here.')
);
$scenes[11] = array(
    'location' => 'QUICKSAND',
    'moves' => array('w', 'west'), // the only way out is to have the shovel and use it
    'story' => array('Suddenly, the ground seemingly start to rise.', 'You\'ve found yourself stuck in a patch of quicksand.', 'A shovel might be the only escape.')
);
$scenes[12] = array(
    'location' => 'The Swamp',
    'moves' => array('n', 'e', 'north', 'east'),
    'story' => array('A muddy mess.', 'You see but a hilly valley to the east, and to the north is more forest.')
); // ended here
$scenes[13] = array(
    'location' => 'The River',
    'moves' => array('Go Left', 'Go Right', 'Go Up', 'Go Down'),
    'story' => array('You are in the river. There is a forest to the left.', 'You are in the river. There is a forest to the right.', 'You are in the river. There is a forest to the up.', 'You are in the river. There is a forest to the down.')
);
$scenes[14] = array(
    'location' => 'The Forest',
    'moves' => array('Go Left', 'Go Right', 'Go Up', 'Go Down'),
    'story' => array('')
);
$scenes[15] = array(
    'location' => 'The River',
    'moves' => array('Go Left', 'Go Right', 'Go Up', 'Go Down'),
    'story' => array('You are in the river. There is a forest to the left.', 'You are in the river. There is a forest to the right.', 'You are in the river. There is a forest to the up.', 'You are in the river. There is a forest to the down.')
);
