<?php

class view
{
  public int $maxUsers = 10;

  public function drawPage(DatePeriod $period, array $data)
  {
    $str  = '';

    $str .= $this->renderHeader();

    foreach ($period as $date)
    {
      $dateStamp = $date->getTimestamp();

      $str .= '<div class="dateCard">';
      $str .= '<h4 class="dateCard__header">Donnerstag, '.$date->format('d.m.Y').'</h4>';
      $str .= $this->renderDay($data, $dateStamp, 0, '');
      $str .= '</div>'; // dateCard
    }

    $str .= $this->renderFooter();

    echo $str;
  }

  public function drawUserChanged(array $data, int $dateStamp, int $code, string $msg)
  {
    $result = json_encode(
    [
      'target' => '.dateCard__users__'.$dateStamp,
      'html' => $this->renderDay($data, $dateStamp, $code, $msg),
      'msg' => $msg,
      'code' => $code
    ]);

    echo $result;
  }

  protected function renderDay(array $data, int $dateStamp, int $code = 0, string $msg = ''): string
  {
    $str  = '';
    $str .= '<div class="dateCard__users__'.$dateStamp.'">';

    if ($code !== 0)
    {
      $str .= '<p class="dateCard__message dateCard__message--error">'.$msg.'</p>';
    }
    else
    {
      $str .= '<p class="dateCard__message dateCard__message--success">'.$msg.'</p>';
    }
    $str .= '<ul>';

    if (!$data[$dateStamp]['options']['hidden'])
    {
      for ($i = 0; $i < $this->maxUsers; $i++)
      {
        $user = (isset($data[$dateStamp]['users'][$i])) ? $data[$dateStamp]['users'][$i] : '';
        $str .= '<li>';
        $str .= '<input class="dateCard__userInput" data-user-idx="'.$i.'" data-stamp="'.$dateStamp.'" type="text" value="'.html_entity_decode($user, ENT_QUOTES).'">';
        $str .= '</li>';
      }
    }
    else
    {
      $str .= '<li><input class="dateCard__userInput" data-user-idx="0" data-stamp="'.$dateStamp.'" type="text" value="Entfällt."></li>';
    }

    $str .= '</ul>';
    $str .= '</div>'; // dateCard__users

    return $str;
  }

  protected function renderHeader(): string
  {
    $erg = '<!DOCTYPE html>'.
           '<html>'.
           '<head>'.
             '<meta charset="utf-8">'.
             '<title>ZENDOnnerstag</title>'.
             '<link rel="shortcut icon" href="./assets/icons8-guru-material-filled-96.png" type="image/png">'.
             '<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">'.
             '<meta http-equiv="cache-control" content="no-cache">'.
             '<meta http-equiv="pragma" content="no-cache">'.
             '<meta http-equiv="expires" content="0">'.
             '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@exampledev/new.css@1/new.min.css">'.
             '<link rel="stylesheet" href="https://fonts.xz.style/serve/inter.css">'.
             '<link rel="stylesheet" type="text/css" href="./view/main.css">'.
             '<script src="./view/main.js" type="text/javascript"></script>'.
             '<script src="./view/rcp.js" type="text/javascript"></script>'.
           '</head>'.
           '<body>'.
            '<header>'.
              '<h1 data-rcp-click="index.php?op=updateHeader" data-rcp-value="payload for the rescue!" class="mainHeadline">ZENDO<span class="mainHeadline__second">nnerstag</span></h1>'.
              '<div><form action="/submitForm"><input type="text" name="dummytext" value="Yep" /><button type="submit" name="dummysubmit" value="Go">Go</button></form></div>'.
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
}

?>