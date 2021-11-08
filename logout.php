<?php
session_start();
require('scripts/game_save.php');
saveGame();
session_destroy();
header('Location: login.php');
