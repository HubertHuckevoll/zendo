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

    public function __construct()
    {
      $this->calendar = new calendarM();
      $this->view = new view();
      $this->users = new usersM();
      $this->users->load();
    }

    public function run()
    {
      $op = filter_input(INPUT_GET, 'op', FILTER_SANITIZE_STRING);

      switch ($op)
      {
        case 'updateUser':
          $stamp = filter_input(INPUT_GET, 'stamp', FILTER_SANITIZE_NUMBER_INT);
          $idx = filter_input(INPUT_GET, 'idx', FILTER_SANITIZE_NUMBER_INT);
          $userOrCmd = $this->getUserOrCommand('user');

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

          $ret = $this->users->save();
          $this->view->drawUserChanged($ret);
        break;

        default:
          $data = $this->users->get();
          $period = $this->calendar->getDates();
          $this->view->drawPage($period, $this->maxUsers, $data);
        break;
      }
    }

    protected function getUserOrCommand(string $str): string
    {
      $matches = [];
      $ret = '';

      $raw = filter_input(INPUT_GET, $str);
      if (preg_match('/command::([a-z]*)/', $raw, $matches) == true)
      {
        $ret = $matches[1];
      }
      else
      {
        $ret = trim(filter_var($raw, FILTER_SANITIZE_STRING));
      }

      return $ret;
    }

  }

  $a = new app();
  $a->run();

?>