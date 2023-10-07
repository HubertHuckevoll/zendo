<?php

  class newsM
  {
    public string $data = '';
    public string $fname = '';

    /**
     * Konstruktor
     * ______________________________________________________________
     */
    public function __construct()
    {
      $this->fname = $_SERVER['DOCUMENT_ROOT'].'/zendo.datastore/news.txt';
    }

    /**
     * load the file
     * ______________________________________________________________
     */
    public function load(): void
    {
      if (($this->data = @file_get_contents($this->fname)) === false)
      {
        $this->data = '';
      }
    }

    /**
     * return current data
     * ______________________________________________________________
     */
    public function get(): string
    {
      return $this->data;
    }
  }

?>