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

    /**
     * update our data / user
     * ______________________________________________________________
     */
    public function update(int $stamp, int $idx, string $userName): void
    {
      if (!$this->isHidden($stamp))
      {
        if ($this->getUser($stamp, $idx) != $userName)
        {
          if (!$this->isUserAlreadyIn($stamp, $userName))
          {
            $this->setUser($stamp, $idx, $userName);
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

    /**
     * set day to enabled / disabled
     * ______________________________________________________________
     */
    public function updateOption(int $stamp, string $option, bool $val): void
    {
      $this->data['content'][$stamp]['options'][$option] = $val;
    }

    /**
     * save file
     * ______________________________________________________________
     */
    public function save(string $hash): string
    {
      if ($this->data['content'] !== false)
      {
        $fc = @file_get_contents($this->fname);
        if ($fc !== false)
        {
          if (md5($fc) == $hash)
          {
            $this->cleanUp();
            $fc = json_encode($this->data['content'], JSON_PRETTY_PRINT);
            $newHash = md5($fc);
            if (@file_put_contents($this->fname, $fc, LOCK_EX) !== false)
            {
              $this->data['hash'] = $newHash;
              return 'Gespeichert.';
            }
          }
        }
      }

      throw new Exception('Speichern fehlgeschlagen.', self::ERR_NOT_SAVED);
    }

    /**
     * get index to focus
     * ______________________________________________________________
     */
    public function getIdxForFocus(int $stamp): int
    {
      if (isset($this->data['content'][$stamp]['users']))
      {
        $rawNum = count($this->data['content'][$stamp]['users']);
        return $rawNum;
      }

      return 0;
    }

    /**
     * clean up
     * ______________________________________________________________
     */
    protected function cleanUp(): void
    {
      $date = new DateTime('today');
      $treshold = $date->getTimestamp();

      foreach($this->data['content'] as $stamp => $val)
      {
        if ((int) $stamp < (int) $treshold)
        {
          unset($this->data['content'][$stamp]);
        }
        elseif (
                (isset($this->data['content'][$stamp]['users'])) &&
                (count($this->data['content'][$stamp]['users']) === 0)
               )
        {
          unset($this->data['content'][$stamp]);
        }
        else
        {
          if (isset($this->data['content'][$stamp]['users']))
          {
            for ($i=0; $i < count($this->data['content'][$stamp]['users']); $i++)
            {
              if (trim($this->getUser($stamp, $i)) == '')
              {
                unset($this->data['content'][$stamp]['users'][$i]);
              }
            }
            array_filter($this->data['content'][$stamp]['users']); // remove empty elements
            $this->data['content'][$stamp]['users'] = array_values($this->data['content'][$stamp]['users']); // make sure we are just numeric
          }
        }
      }

      ksort($this->data['content']); // sort the whole thing by timestamp
    }

    /**
     * is day hidden?
     * ______________________________________________________________
     */
    protected function isHidden($dateStamp): bool
    {
      if (isset($this->data['content'][$dateStamp]['options']['hidden']))
      {
        if ($this->data['content'][$dateStamp]['options']['hidden'] == true)
        {
          return true;
        }
      }
      return false;
    }

    /**
     * get user
     * ______________________________________________________________
     */
    protected function getUser($dateStamp, $i): string
    {
      if (isset($this->data['content'][$dateStamp]['users'][$i]))
      {
        return $this->data['content'][$dateStamp]['users'][$i];
      };

      return '';
    }

    /**
     * set user
     * ______________________________________________________________
     */
    protected function setUser($dateStamp, $i, $userName): void
    {
      $this->data['content'][$dateStamp]['users'][$i] = $userName;
    }

    /**
     * check if user is already in
     * ______________________________________________________________
     */
    protected function isUserAlreadyIn($stamp, $user): bool
    {
      if ($user != '')
      {
        if (isset($this->data['content'][$stamp]['users']))
        {
          for ($i=0; $i < count((array) $this->data['content'][$stamp]['users']); $i++)
          {
            if ($this->data['content'][$stamp]['users'][$i] == $user)
            {
              return true;
            }
          }
        }
      }

      return false;
    }

  }

?>