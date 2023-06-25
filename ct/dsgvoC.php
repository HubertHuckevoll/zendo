<?php

class dsgvoC extends cPageC
{
  public function __construct()
  {
    $this->view = new dsgvoV('dsgvoV');
  }

  public function show(): void
  {
    $this->view->drawPage();
  }
}

?>