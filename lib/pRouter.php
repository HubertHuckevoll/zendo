<?php

class pRouter
{
  protected string $mainControllerName = '';
  protected string $mainMethodName = '';

  public function __construct(string $mainControllerName, string $mainMethodName)
  {
    $this->mainControllerName = $mainControllerName;
    $this->mainMethodName = $mainMethodName;
  }

  public function run()
  {
    $dummy = '';
    $obj = null;
    $pathInfo = '';
    $objName = '';
    $methodName = '';

    if (isset($_SERVER['PATH_INFO']))
    {
      $pathInfo = $_SERVER['PATH_INFO'];
      list($dummy, $objName, $methodName) = explode('/', $pathInfo); // $dummy => PATH_INFO has a leading "/" that creates a fake first entry
    }
    else
    {
      $objName = $this->mainControllerName;
      $methodName = $this->mainMethodName;
    }

    try
    {
      $obj = new $objName();

      if ((isset($obj) && method_exists($obj, $methodName)))
      {
        call_user_func(array($obj, $methodName));
      }
      else
      {
        die('Fatal error: Couldn\'t run method "'.$methodName.'" on object "'.$objName.'".');
      }
    }
    catch(Exception $e)
    {
      die('Fatal error: Couldn\'t instantiate object "'.$objName.'".');
    }
  }
}


?>