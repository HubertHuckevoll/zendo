<?php

  class usersM
  {
    public $data = array();
    public $fname = './data/zendo.json';

    public function load()
    {
      $data = @file_get_contents($this->fname);
      if ($data !== false)
      {
        $this->data = json_decode($data, true);
      }
    }

    public function get(): array
    {
      return $this->data;
    }

    public function update(int $stamp, int $idx, string $userName)
    {
      if (!$this->data[$stamp]['options']['hidden'])
      {
        if ($this->data[$stamp]['users'][$idx] != $userName)
        {
          $this->data[$stamp]['users'][$idx] = $userName;
        }
      }
    }

    public function updateOption(int $stamp, string $option, bool $val)
    {
      $this->data[$stamp]['options'][$option] = $val;
    }

    public function save(): string
    {
      if ($this->data !== false)
      {
        $this->cleanUp();
        $data = json_encode($this->data, JSON_PRETTY_PRINT);

        if (@file_put_contents($this->fname, $data, LOCK_EX) !== false)
        {
          return 'Aktualisiert.';
        }
      }
      return 'Aktualisierung fehlgeschlagen.';
    }

    protected function cleanUp()
    {
      $date = new DateTime('first day of this month');
      $treshold = $date->getTimestamp();

      foreach($this->data as $stamp => $val)
      {
        if ((int) $stamp < (int) $treshold)
        {
          unset($this->data[$stamp]);
        }
      }
    }
  }

?>