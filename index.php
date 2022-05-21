<?php

  require_once('./logger.php');
  require_once('./view/view.php');
  require_once('./model/calendarM.php');
  require_once('./model/usersM.php');

  class app
  {
    public $calendar = null;
    public $view = null;
    public $users = null;
    public $maxUsers = 7;


    /**
     * Konstruktor
     * ______________________________________________________________
     */
    public function __construct()
    {
      $this->calendar = new calendarM();
      $this->view = new view();
      $this->users = new usersM();

      $this->view->maxUsers = $this->maxUsers;
      $this->users->load();
    }

    /**
     * Run
     * ______________________________________________________________
     */
    public function run()
    {
      $op = filter_input(INPUT_GET, 'op');

      switch ($op)
      {
        case 'userForm':
          $stamp = $this->getTimestamp();
          $idx = $this->getIdx();
          $data = $this->users->get();
          $this->view->drawDay($data, $stamp, $idx);
        break;

        case 'refreshHeadline':
          $stamp = $this->getTimestamp();
          $this->view->drawDayHeadline($stamp);
        break;

        case 'updateUser':
          try
          {
            $stamp = $this->getTimestamp();
            $idx = $this->getIdx();
            $userOrCmd = $this->getUserOrCommand();
            $hash = $this->getHash();

            logger::vh($stamp, $idx, $userOrCmd, $hash);

            switch ($userOrCmd)
            {
              case 'disable':
                $this->users->updateOption($stamp, 'hidden', true);
              break;

              case 'enable':
                $this->users->updateOption($stamp, 'hidden', false);
              break;

              default:
                $this->users->update($stamp, $idx, $userOrCmd);
              break;
            }

            $msg = $this->users->save($hash);
            $data = $this->users->get();
            $this->view->drawUserChanged($data, $stamp, 0, $msg);
          }
          catch(Exception $e)
          {
            $data = $this->users->get();
            $msg = $e->getMessage();
            $code = $e->getCode();
            $this->view->drawUserChanged($data, $stamp, $code, $msg);
          }
        break;

        default:
          $data = $this->users->get();
          $period = $this->calendar->getDates();
          $this->view->drawPage($period, $data);
        break;
      }
    }

    /**
     * extract a timestamp from POST/JSON data
     * ______________________________________________________________
     */
    protected function getTimestamp(): int
    {
      $inp = $this->getJsonInput();

      if (isset($inp['rcpStamp']))
      {
        $stamp = filter_var($inp['rcpStamp'], FILTER_SANITIZE_NUMBER_INT);

        if (preg_match('/[0-9]{10}/', $stamp) == true)
        {
          return $stamp;
        }
      }

      throw new Exception('Kein gültiger Zeitstempel.');
    }

    /**
     * extract an Index from POST/JSON data
     * ______________________________________________________________
     */
    protected function getIdx(): int
    {
      $inp = $this->getJsonInput();

      if (isset($inp['rcpIdx']))
      {
        $idx = filter_var($inp['rcpIdx'], FILTER_SANITIZE_NUMBER_INT);

        if (($idx >= 0) && ($idx <= ($this->maxUsers - 1)))
        {
          return $idx;
        }
      }

      throw new Exception('Kein gültiger Index.');
    }

    /**
     * extract USER or COMMAND from POST/JSON data
     * ______________________________________________________________
     */
    protected function getUserOrCommand(): string
    {
      $matches = [];
      $ret = '';
      $inp = $this->getJsonInput();

      if (isset($inp['rcpUser']))
      {
        $raw = $inp['rcpUser'];

        if (preg_match('/command::([a-z]*)/', $raw, $matches) == true)
        {
          $ret = $matches[1];
        }
        else
        {
          $ret = trim(htmlentities(strip_tags($raw), ENT_QUOTES));
        }

        return $ret;
      }

      throw new Exception('Kein gültiger Nutzer / Befehl.');
    }

    /**
     * get Hash
     * ______________________________________________________________
     */
    public function getHash()
    {
      $inp = $this->getJsonInput();
      if (isset($inp['rcpHash']))
      {
        return $inp['rcpHash'];
      }

      throw new Exception('Kein gültiger Hash.');
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

  $a = new app();
  $a->run();

?>