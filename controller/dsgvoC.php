<?php

  class dsgvoC extends pController
  {
    public function index(): void
    {
      $v = new dsgvoV();
      $v->draw();
    }
  }

?>