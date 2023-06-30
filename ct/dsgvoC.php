<?php

class dsgvoC extends cPageC
{
  public function __construct(array $request, ?array $prefs = null)
  {
    $view = new dsgvoV('dsgvoV');
    parent::__construct($request, $prefs, $view);
  }

  public function show(): void
  {
    $this->view->drawPage();
  }
}

?>