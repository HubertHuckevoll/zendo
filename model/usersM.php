<?php

  class usersM
  {
    public $data = array();
    public $fname = './data/zendo.json';
    const ERR_NOT_CHANGED = 1;
    const ERR_DUPLICATE = 2;
    const ERR_DAY_INACTIVE = 3;
    const ERR_NOT_SAVED = 4;

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
          if (!$this->isUserAlreadyIn($stamp, $userName))
          {
            $this->data[$stamp]['users'][$idx] = $userName;
          }
          else
          {
            throw new Exception('Nutzer ist bereits eingetragen.', self::ERR_DUPLICATE);
          }
        }
        else
        {
          throw new Exception('Keine Änderung vorgenommen.', self::ERR_NOT_CHANGED);
        }
      }
      else
      {
        throw new Exception('Tag ist nicht aktiviert.', self::ERR_DAY_INACTIVE);
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
          return 'Gespeichert.';
        }
      }

      throw new Exception('Speichern fehlgeschlagen.', self::ERR_NOT_SAVED);
    }

    protected function cleanUp()
    {
      $date = new DateTime('today');
      $treshold = $date->getTimestamp();

      foreach($this->data as $stamp => $val)
      {
        if ((int) $stamp < (int) $treshold)
        {
          unset($this->data[$stamp]);
        }
        else
        {
          for ($i=0; $i < count($this->data[$stamp]['users']); $i++)
          {
            if ((trim($this->data[$stamp]['users'][$i])) == '')
            {
              unset($this->data[$stamp]['users'][$i]);
            }
          }
        }
      }

      ksort($this->data);
    }

    protected function isUserAlreadyIn($stamp, $user)
    {
      if ($user != '')
      {
        for ($i=0; $i < count($this->data[$stamp]['users']); $i++)
        {
          if ($this->data[$stamp]['users'][$i] == $user)
          {
            return true;
          }
        }
      }

      return false;
    }

  }

?>