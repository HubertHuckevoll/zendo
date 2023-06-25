<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/coins/loader.php');
require_once('./loader.php');

$pathInfoAssignCallback = function($pathInfoParts)
{
  $numEntrys = count($pathInfoParts);
  switch ($numEntrys)
  {
    case 0:
    break;
    case 1:
    break;

    case 2:
      $keyVal['mod'] = $pathInfoParts[0];
      $keyVal['hook'] = $pathInfoParts[1];
    break;
  }
  return $keyVal;
};


$a = new cAppC('mainC', 'index', $pathInfoAssignCallback);
$a->run();

?>