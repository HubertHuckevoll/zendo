<?php

  class pController
  {
    /**
     * Konstruktor
     * ______________________________________________________________
     */
    public function __construct()
    {
    }

    /**
     * fetch raw JSON input
     * ______________________________________________________________
     */
    protected function getJsonInput()
    {
      $input = json_decode(file_get_contents('php://input'), true);
      return $input;
    }

  }

?>