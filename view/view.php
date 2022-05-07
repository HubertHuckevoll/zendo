<?php

class view
{
  public function drawPage(DatePeriod $period, int $maxUsers, array $data)
  {
    $str  = '';

    $str .= $this->openPage();

    foreach ($period as $date)
    {
      $dateStamp = $date->getTimestamp();

      $str .= '<div class="dateCard">';
      $str .= '<h4 class="dateCard__header">Donnerstag, '.$date->format('d.m.Y').'</h4>';

      $str .= '<ul class="dateCard__users">';

      if (!$data[$dateStamp]['options']['hidden'])
      {
        for ($i = 0; $i < $maxUsers; $i++)
        {
          $user = (isset($data[$dateStamp]['users'][$i])) ? $data[$dateStamp]['users'][$i] : '';
          $str .= '<li><input class="dateCard__userInput" data-user-idx="'.$i.'" data-stamp="'.$dateStamp.'" type="text" value="'.html_entity_decode($user, ENT_QUOTES).'"></li>';
        }
      }
      else
      {
        $str .= '<li><input class="dateCard__userInput" data-user-idx="0" data-stamp="'.$dateStamp.'" type="text" value="Entfällt."></li>';
      }

      $str .= '</ul>'; // dateCard__users

      $str .= '</div>'; // dateCard
    }

    $str .= $this->closePage();

    echo $str;
  }

  public function drawUserChanged(int $code, string $msg)
  {
    $result = json_encode([
      'msg' => $msg,
      'code' => $code
    ]);

    echo $result;
  }

  protected function openPage(): string
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
           '</head>'.
           '<body>'.
            '<header>'.
              '<h1 class="mainHeadline">ZENDO<span class="mainHeadline__second">nnerstag</span></h1>'.
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

  protected function closePage(): string
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