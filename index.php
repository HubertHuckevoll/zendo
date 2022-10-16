<?php

require_once('./lib/pRouter.php');
require_once('./lib/pController.php');
require_once('./lib/logger.php');
require_once('./controller/dsgvoC.php');
require_once('./controller/mainC.php');
require_once('./view/mainV.php');
require_once('./view/dsgvoV.php');
require_once('./model/calendarM.php');
require_once('./model/usersM.php');
require_once('../RecipeJS/RecipeJS.php');

$a = new pRouter('mainC', 'index');
$a->run()

?>