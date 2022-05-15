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

      $this->view->maxUsers = $this->maxUsers;
      $this->users->load();
    }

    public function run()
    {
      $op = filter_input(INPUT_GET, 'op');

      switch ($op)
      {
        case 'refreshDay':
          $stamp = $this->getTimestamp();
          $data = $this->users->get();
          $this->view->drawDay($data, $stamp);
        break;

        case 'updateUser':
          try
          {
            $stamp = $this->getTimestamp();
            $idx = $this->getIdx();
            $userOrCmd = $this->getUserOrCommand();

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

            $msg = $this->users->save();
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

        case 'updateHeader':
          $data = [
            [
              'action' => 'dom',
              'method' => 'replace',
              'target' => 'h1',
              'html' => '<h1 data-rcp-click="index.php?op=updateHeader">I shit you not!'.time().'</h1>'
            ],
            [
              'action' => 'event',
              'type' => 'rcp',
              'timeout' => 2000,
              'detail' => [
                'route' => 'index.php?op=afterEvent',
                'dummy' => 'yummy'
              ]
            ]
          ];

          echo json_encode($data);
        break;

        case 'afterEvent':
          $input = $this->getJsonInput();

          $data = [
            [
              'action' => 'css',
              'method' => 'add',
              'target' => 'h1',
              'names' => ['freaking_cool_css_class', 'freaking_awesome_class'],
              'payload' => $input['route']
            ]
          ];

          echo json_encode($data);
        break;

        default:
          $data = $this->users->get();
          $period = $this->calendar->getDates();
          $this->view->drawPage($period, $data);
        break;
      }
    }

    protected function getTimestamp(): int
    {
      $inp = $this->getJsonInput();
      $stamp = filter_var($inp['rcpStamp'], FILTER_SANITIZE_NUMBER_INT);

      if (preg_match('/[0-9]{10}/', $stamp) == true)
      {
        return $stamp;
      }
      else
      {
        throw new Exception('Kein gültiger Zeitstempel.');
      }
    }

    protected function getIdx(): int
    {
      $inp = $this->getJsonInput();
      $idx = filter_var($inp['rcpIdx'], FILTER_SANITIZE_NUMBER_INT);

      if (($idx >= 0) && ($idx <= ($this->maxUsers - 1)))
      {
        return $idx;
      }
      else
      {
        throw new Exception('Kein gültiger Index.');
      }
    }

    protected function getUserOrCommand(): string
    {
      $matches = [];
      $ret = '';

      $inp = $this->getJsonInput();
      $raw = filter_var($inp['value']);
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

    protected function getJsonInput()
    {
      $input = json_decode(file_get_contents('php://input'), true);
      return $input;
    }

  }

  $a = new app();
  $a->run();

?>