<?php

  class mainC extends cPageC
  {
    public $calendar = null;
    public $users = null;
    public $maxUsers = 7;
    public $numDays = 30; // 4 Weeks

    /**
     * Konstruktor
     * ______________________________________________________________
     */
    public function __construct()
    {
      $this->calendar = new calendarM();
      $this->view = new mainV('mainV');
      $this->users = new usersM();

      $this->view->maxUsers = $this->maxUsers;
      $this->users->load();
    }

    /**
     * userForm
     * ______________________________________________________________
     */
    public function userForm()
    {
      try
      {
        $oldStamp = $this->getRelatedTimestamp();
        $stamp = $this->getTimestamp();
        $idx = $this->getIdx();
        $data = $this->users->get();
        $this->view->ajaxDrawDay($data, $oldStamp, $stamp, $idx);
      }
      catch (Exception $e)
      {
        $this->view->ajaxDrawFatalError($e);
      }
    }

    /**
     * refreshDay
     * ______________________________________________________________
     */
    public function refreshDay()
    {
      try
      {
        $oldStamp = $this->getRelatedTimestamp();
        $stamp = $this->getTimestamp();
        $data = $this->users->get();
        $this->view->ajaxDrawDay($data, $oldStamp, $stamp);
      }
      catch(Exception $e)
      {
        $this->view->ajaxDrawFatalError($e);
      }
    }

    /**
     * refreshHeadline
     * ______________________________________________________________
     */
    public function refreshHeadline()
    {
      try
      {
        $stamp = $this->getTimestamp();
        $this->view->ajaxDrawDayHeadline($stamp);
      }
      catch(Exception $e)
      {
        $this->view->ajaxDrawFatalError($e);
      }
    }

    /**
     * updateUser
     * ______________________________________________________________
     */
    public function updateUser()
    {
      try
      {
        $stamp = $this->getTimestamp();
        $idx = $this->getIdx();
        $userOrCmd = $this->getUserOrCommand();
        $hash = $this->getHash();

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
        $this->view->ajaxDrawUserChanged($data, $stamp, 0, $msg);
      }
      catch(Exception $e)
      {
        $data = $this->users->get();
        $msg = $e->getMessage();
        $code = $e->getCode();
        $this->view->ajaxDrawUserChanged($data, $stamp, $code, $msg);
      }
    }

    /**
     * show start page
     * ______________________________________________________________
     */
    public function index()
    {
      try
      {
        $data = $this->users->get();
        $period = $this->calendar->getDates($this->numDays);
        $this->view->drawPage($period, $data);
      }
      catch(Exception $e)
      {
        $this->view->ajaxDrawFatalError($e);
      }
    }

    /**
     * extract a timestamp from POST/JSON data
     * ______________________________________________________________
     */
    protected function getTimestamp(): int
    {
      $inp = $this->getJsonInput();
      $inp = $inp['target'];

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
     * Timestamp for relatedTarget
     * ______________________________________________________________
     */
    protected function getRelatedTimestamp(): int|null
    {
      $inp = $this->getJsonInput();

      if (isset($inp['relatedTarget']))
      {
        $inp = $inp['relatedTarget'];
        if (isset($inp['rcpStamp']))
        {
          $stamp = filter_var($inp['rcpStamp'], FILTER_SANITIZE_NUMBER_INT);
          if (preg_match('/[0-9]{10}/', $stamp) == true)
          {
            return $stamp;
          }
        }
      }

      return null;
    }

    /**
     * extract an Index from POST/JSON data
     * ______________________________________________________________
     */
    protected function getIdx(): int
    {
      $inp = $this->getJsonInput();
      $inp = $inp['target'];

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
      $inp = $inp['target'];

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
    public function getHash(): string
    {
      $inp = $this->getJsonInput();
      $inp = $inp['target'];
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

?>