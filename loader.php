<?php

  /**
   * Auto loader
   * ________________________________________________________________
   */
  spl_autoload_register(function($className)
  {
    $fname = null;
    $ct = substr($className, -1);

    switch($ct)
    {
      case 'V':
        $fname = '.'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$className.'.php';
      break;

      case 'M':
        $fname = '.'.DIRECTORY_SEPARATOR.'model'.DIRECTORY_SEPARATOR.$className.'.php';
      break;

      case 'C':
        $fname = '.'.DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR.$className.'.php';
      break;

      default:
        $fname = '.'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.$className.'.php';
      break;
    }

    if ($fname !== null)
    {
      if (file_exists($fname))
      {
        require_once($fname);
      }
      else
      {
        die('Couln\'t load class "'.$className.'" from "'.$fname.'"');
      }
    }
  });

?>