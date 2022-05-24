<?php

class view
{
  public int $maxUsers = 10;

  /**
   * initial page draw
   * ________________________________________________________________
   */
  public function drawPage(DatePeriod $period, array $data)
  {
    $str  = '';

    $str .= $this->renderHeader();

    foreach ($period as $date)
    {
      $dateStamp = $date->getTimestamp();

      $str .= '<div class="dateCard">';
      $str .= $this->renderDayHeadline($dateStamp);
      $str .= $this->renderDay($data, $dateStamp, null);
      $str .= '</div>'; // dateCard
    }

    $str .= $this->renderFooter();

    echo $str;
  }

  /**
   * draw day headline / error / success messages
   * ________________________________________________________________
   */
  public function drawDayHeadline(int $stamp)
  {
    $result = [
      [
        'action' => 'dom',
        'method' => 'replace',
        'target' => '.dateCard__headline__'.$stamp,
        'html' => $this->renderDayHeadline($stamp),
      ]
    ];

    echo json_encode($result);
  }

  /**
   * draw users for a day
   * ________________________________________________________________
   */
  public function drawDay(array $data, int $oldStamp = null, int $dateStamp, int|null $idx = null): void
  {
    $result = [];

    if (($oldStamp !== null) && ($oldStamp != $dateStamp))
    {
      array_push($result, [
        'action' => 'dom',
        'method' => 'replace',
        'target' => '.dateCard__users__'.$oldStamp,
        'html' => $this->renderDay($data, $oldStamp)
      ]);
    }

    array_push($result, [
        'action' => 'dom',
        'method' => 'replace',
        'target' => '.dateCard__users__'.$dateStamp,
        'html' => $this->renderDay($data, $dateStamp, $idx)
    ]);

    array_push($result,
    [
      'action' => 'focus',
      'method' => 'focus',
      'target' => '.dateCard__users__'.$dateStamp.' form input[type="text"]'
    ]);

    echo json_encode($result);
  }

  /**
   * draw everythig that happens when a new user has been added / removes / changed
   * ________________________________________________________________
   */
  public function drawUserChanged(array $data, int $dateStamp, int $code = 0, string $msg = '')
  {
    $result = [];

    array_push($result, [
      'action' => 'dom',
      'method' => 'replace',
      'target' => '.dateCard__headline__'.$dateStamp,
      'html' => $this->renderDayHeadline($dateStamp, $code, $msg),
    ]);

    array_push($result, [
      'action' => 'dom',
      'method' => 'replace',
      'target' => '.dateCard__users__'.$dateStamp,
      'html' => $this->renderDay($data, $dateStamp),
    ]);

    array_push($result, [
      'action' => 'event',
      'type' => 'rcp',
      'timeout' => 2000,
      'detail' => [
        'route' => 'index.php?op=refreshHeadline',
        'rcpStamp' => $dateStamp
      ]
    ]);

    echo json_encode($result);
  }

  /**
   * draw fatal error
   * ________________________________________________________________
   */
  public function drawFatalError($e)
  {
    $result = [];

    array_push($result,
    [
      'action' => 'error',
      'method' => 'console',
      'msg' => $e->getMessage()
    ]);

    echo json_encode($result);
  }

  /**
   * render headline
   * ________________________________________________________________
   */
  protected function renderDayHeadline(int $stamp, int $code = 0, string $msg = '')
  {
    $str = '';

    if (($code === 0) && ($msg == ''))
    {
      $msg = 'Donnerstag, '.date('d.m.Y', $stamp);
    }

    $str = '<h4 class="dateCard__headline__'.$stamp.'">'.$msg.'</h4>';

    return $str;
  }

  /**
   * render day
   * ________________________________________________________________
   */
  protected function renderDay(array $data, int $dateStamp, int|null $idx = null): string
  {
    $str  = '';
    $str .= '<div class="dateCard__users__'.$dateStamp.'">';

    $str .= '<ul>';

    if (!$this->isHidden($data, $dateStamp))
    {
      for ($i = 0; $i < $this->maxUsers; $i++)
      {
        $user = $this->extractUser($data, $dateStamp, $i);
        $str .= '<li>';
        if ($idx === $i)
        {
          $str .= '<form action="index.php?op=updateUser">';
          $str .= '<input name="rcpIdx"   type="hidden" value="'.$i.'">';
          $str .= '<input name="rcpStamp" type="hidden" value="'.$dateStamp.'">';
          $str .= '<input name="rcpHash"  type="hidden" value="'.$data['hash'].'">';
          $str .= '<input name="rcpUser"  data-rcp-blur="index.php?op=refreshDay" data-rcp-stamp="'.$dateStamp.'" data-rcp-idx="'.$i.'" data-rcp-hash="'.$data['hash'].'" type="text" value="'.html_entity_decode($user, ENT_QUOTES).'">';
          $str .= '&nbsp;';
          $str .= '<input name="rcpSubm"  type="submit" value="OK">';
          $str .= '</form>';
        }
        else
        {
          $str .= '<input class="dateCard__userInput" data-rcp-focus="index.php?op=userForm" name="rcpInput" data-rcp-idx="'.$i.'" data-rcp-stamp="'.$dateStamp.'" data-rcp-hash="'.$data['hash'].'" type="text" readonly value="'.html_entity_decode($user, ENT_QUOTES).'">';
        }

        $str .= '</li>';
      }
    }
    else
    {
      // "0" as int means we received this from the users end, "null" means we just rendered a default value
      if ($idx === 0)
      {
        $str .= '<form action="index.php?op=updateUser">';
        $str .= '<input name="rcpIdx"   type="hidden" value="0">';
        $str .= '<input name="rcpStamp" type="hidden" value="'.$dateStamp.'">';
        $str .= '<input name="rcpHash"  type="hidden" value="'.$data['hash'].'">';
        $str .= '<input name="rcpUser"  type="text" type="text" value="Entfällt.">';
        $str .= '&nbsp;';
        $str .= '<input name="rcpSubm"  type="submit" value="OK">';
        $str .= '</form>';
      }
      else
      { // $idx === null
        $str .= '<li><input class="dateCard__userInput" data-rcp-focus="index.php?op=userForm" name="rcpInput" data-rcp-idx="0" data-rcp-stamp="'.$dateStamp.'" data-rcp-hash="'.$data['hash'].'" type="text" readonly value="Entfällt."></li>';
      }

    }

    $str .= '</ul>';
    $str .= '</div>'; // dateCard__users

    return $str;
  }

  /**
   * render page header
   * ________________________________________________________________
   */
  protected function renderHeader(): string
  {
    $erg = '<!DOCTYPE html>' .
      '<html>' .
      '<head>' .
      '<meta charset="utf-8">' .
      '<title>ZENDOnnerstag</title>' .
      '<link rel="shortcut icon" href="./assets/icons8-guru-material-filled-96.png" type="image/png">' .
      '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">' .
      '<meta http-equiv="cache-control" content="no-cache">' .
      '<meta http-equiv="pragma" content="no-cache">' .
      '<meta http-equiv="expires" content="0">' .
      '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@exampledev/new.css@1/new.min.css">' .
      '<link rel="stylesheet" href="https://fonts.xz.style/serve/inter.css">' .
      '<link rel="stylesheet" type="text/css" href="./view/main.css">' .
      '<script src="./view/rcp.js" type="text/javascript"></script>' .
      '</head>' .
      '<body>' .
      '<header>' .
      '<h1 class="mainHeadline">ZENDO<span class="mainHeadline__second">nnerstag</span></h1>' .
      '</header>';

    $erg .= '<main>';
    $erg .= '<blockquote>';
    $erg .= 'Willkommen beim Kalender der <a href="https://www.lebendiges-zen.de/zendo-erfurt/" target="_blank">"Lebendiges Zen"-Gruppe Erfurt</a>.
             Wir sitzen donnerstags von 18.30 Uhr bis ca. 20:30 Uhr in der <a href="https://goo.gl/maps/zZJsrxE17p4Pp4gU7" target="_blank">Puschkinstraße 1</a> Zazen.
             Bitte tragt Euch bei den einzelnen Donnerstagen ein.<br><br>
             Dieser Kalender folgt dem ursprünglichen Wiki-Prinzip: jeder kann alles bearbeiten,
             es gibt keine Anmeldung oder Ähnliches. Wir vertrauen darauf, dass ihr sorgsam damit umgeht.';
    $erg .= '</blockquote>';

    return $erg;
  }

  /**
   * render page footer
   * ________________________________________________________________
   */
  protected function renderFooter(): string
  {
    $str  = '';
    $str .= '</main>';
    $str .= '<footer>';
    $str .= 'Konstantin Meyer [2022/4+] für <a href="https://www.lebendiges-zen.de/zendo-erfurt/" target="_blank">Lebendiges Zen Erfurt</a>.<br>';
    $str .= 'Dank an <a href="https://newcss.net/" target="_blank">new.css</a>.';
    $str .= '</footer>';
    $str .= '</body></html>';

    return $str;
  }

  /**
   * is a day hidden?
   * ________________________________________________________________
   */
  protected function isHidden($data, $dateStamp): bool
  {
    if (isset($data['content'][$dateStamp]['options']['hidden']))
    {
      if ($data['content'][$dateStamp]['options']['hidden'] == true)
      {
        return true;
      }
    }
    return false;
  }

  /**
   * extract a user from the data array
   * ________________________________________________________________
   */
  protected function extractUser($data, $dateStamp, $i): string
  {
    if (isset($data['content'][$dateStamp]['users'][$i]))
    {
      return $data['content'][$dateStamp]['users'][$i];
    };

    return '';
  }
}
