<?php

require_once('./loader.php');
require_once('./lib/logger.php');
require_once('../RecipeJS/RecipeJS.php');

$a = new pRouter('mainC', 'index');
$a->runWithPathInfo();

?>