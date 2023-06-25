<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/coins/loader.php');
require_once('./loader.php');
/*
require_once('./lb/logger.php');
require_once('../RecipeJS/RecipeJS.php');
*/

$a = new cAppC('mainC', 'index');
$a->run();

?>