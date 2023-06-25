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
        $fname = './vw/php/'.$className.'.php';
      break;

      case 'M':
        $fname = './md/'.$className.'.php';
      break;

      case 'C':
        $fname = './ct/'.$className.'.php';
      break;

      default:
        $fname = './lb/'.$className.'.php';
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