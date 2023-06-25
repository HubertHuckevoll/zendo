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

  /**
   * run with a path pattern of index.php/mod/hook?queryparams=xyz
   * ________________________________________________________________
   */
  public function runWithPathInfo()
  {
    $dummy = '';
    $pathInfo = '';
    $modName = '';
    $methodName = '';

    if (isset($_SERVER['PATH_INFO']))
    {
      $pathInfo = $_SERVER['PATH_INFO'];
      list($dummy, $modName, $methodName) = explode('/', $pathInfo); // $dummy => PATH_INFO has a leading "/" that creates a fake first entry
    }
    else
    {
      $modName = $this->mainControllerName;
      $methodName = $this->mainMethodName;
    }

    $this->exec($modName, $methodName);
  }

  /**
   * run with two get vars: index.php?mod=controller&hook=methodName
   * ________________________________________________________________
   */
  public function runWithGetVarsModHook()
  {
    $modName = '';
    $methodName = '';

    $modName = filter_input(INPUT_GET, 'mod');
    $methodName = filter_input(INPUT_GET, 'hook');

    if (!$modName)
    {
      $modName = $this->mainControllerName;
    }

    if (!$methodName)
    {
      $methodName = $this->mainMethodName;
    }

    $this->exec($modName, $methodName);
  }

  /**
   * call the controller
   * ________________________________________________________________
   */
  protected function exec(string $modName, string $methodName): void
  {
    $controllerObj = null;

    try
    {
      $controllerObj = new $modName();

      if ((isset($controllerObj) && method_exists($controllerObj, $methodName)))
      {
        call_user_func(array($controllerObj, $methodName));
      }
      else
      {
        die('Fatal error: Couldn\'t run method "'.$methodName.'" on object "'.$controllerObj.'".');
      }
    }
    catch(Exception $e)
    {
      die('Fatal error: Couldn\'t instantiate object "'.$controllerObj.'".');
    }
  }
}


?>