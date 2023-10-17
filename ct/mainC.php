<?php

  class mainC extends cPageC
  {
    public $calendar = null;
    public $users = null;
    public $news = null;
    public $maxUsers = 10;
    public $numDays = 30; // 4 Weeks

    /**
     * Konstruktor
     * ______________________________________________________________
     */
    public function __construct(array $request, ?array $prefs = null)
    {
      $view = new mainV('mainV');
      parent::__construct($request, $prefs, $view);

      $this->calendar = new calendarM();
      $this->users = new usersM();
      $this->view->maxUsers = $this->maxUsers;
      $this->news = new newsM();

      $this->users->load();
      $this->news->load();
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
        $subm = $this->getSubm();

        switch ($userOrCmd)
        {
          case 'disable':
            $this->users->updateOption($stamp, 'hidden', true);
          break;

          case 'enable':
            $this->users->updateOption($stamp, 'hidden', false);
          break;

          default:
            if ($subm == 'X') { $userOrCmd = '';}
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
        $this->view->drawPage($period, $data, $this->news->get());
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
      $inp = $this->request['target'];

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
      if (isset($this->request['relatedTarget']))
      {
        $inp = $this->request['relatedTarget'];
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
      $inp = $this->request['target'];

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
      $inp = $this->request['target'];

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
      $inp = $this->request['target'];
      if (isset($inp['rcpHash']))
      {
        return $inp['rcpHash'];
      }

      throw new Exception('Kein gültiger Hash.');
    }

    /**
     * Get submit value: just or "OK" or "X".
     * ______________________________________________________________
     */
    public function getSubm(): string
    {
      $inp = $this->request['target'];
      if (isset($inp['rcpSubm']))
      {
        return $inp['rcpSubm'];
      }

      return '';
    }
  }

?>