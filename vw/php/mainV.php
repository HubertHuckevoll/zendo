<?php

class mainV extends cAppV
{
  public int $maxUsers = 10;

  /**
   * initial page draw
   * ________________________________________________________________
   */
  public function drawPage(DatePeriod $period, array $data, string $news): void
  {
    $str  = '';

    $this->setTag('intro', $this->renderIntro());
    $this->setTag('news', $this->renderNews($news));

    foreach ($period as $date)
    {
      $dateStamp = $date->getTimestamp();

      $str .= '<div class="dateCard">';
      $str .= $this->renderDayHeadline($dateStamp);
      $str .= $this->renderDay($data, $dateStamp, null);
      $str .= '</div>'; // dateCard
    }

    $this->setTag('content', $str);
    $this->draw();
  }

  /**
   * render Intro
   * ________________________________________________________________
   */
  public function renderIntro(): string
  {
    return 'Willkommen beim Kalender der <a href="https://www.lebendiges-zen.de/zendo-erfurt/" target="_blank">"Lebendiges Zen"-Gruppe Erfurt</a>. '.
           'Wir sitzen <strong>donnerstags von 19.30 Uhr bis ca. 20.45 Uhr</strong> in der <strong><a href="https://maps.app.goo.gl/Ba9mW17T3sjqYNqo6" target="_blank">Rosengasse 9</a> ("Yoga Loft")</strong> Zazen. '.
           'Bitte tragt Euch bei den einzelnen Donnerstagen ein, seid ca. 10 - 15 Minuten früher da und bringt 5€ mit - der Raum wird stundenweise gemietet. '.
           '(Wem die 5€ Schwierigkeiten bereiten, der spreche uns bitte an.)'.
           '<br><br>'.
           'Dieser Kalender folgt dem ursprünglichen Wiki-Prinzip: jeder kann alles bearbeiten, '.
           'es gibt keine Anmeldung oder Ähnliches. Wir vertrauen darauf, dass Ihr sorgsam damit umgeht.';
  }


  /**
   * render News
   * ________________________________________________________________
   */
  public function renderNews(string $news): string
  {
    if ($news !== '')
    {
      return '<div class="newsBox">'.
             '<h5>Aktuelles</h5>'.
             trim($news).
             '</div>';
    }

    return '';
  }

  /**
   * draw day headline / error / success messages
   * ________________________________________________________________
   */
  public function ajaxDrawDayHeadline(int $stamp): void
  {
    $rcp = new RecipeV();

    $rcp->cssHide('.dateCard__headline__'.$stamp, 'fadeOut', 'fadeIn', true);
    $rcp->domReplaceInner('.dateCard__headline__'.$stamp, $this->renderDayHeadlineInner($stamp));
    $rcp->cssShow('.dateCard__headline__'.$stamp, 'fadeOut', 'fadeIn', true);

    $rcp->send();
  }

  /**
   * draw users for a day
   * ________________________________________________________________
   */
  public function ajaxDrawDay(array $data, int $oldStamp = null, int $dateStamp, int|null $idx = null): void
  {
    $rcp = new RecipeV();

    if (($oldStamp !== null) && ($oldStamp != $dateStamp))
    {
      $rcp->domReplace('.dateCard__users__'.$oldStamp, $this->renderDay($data, $oldStamp));
    }

    $rcp->domReplace('.dateCard__users__'.$dateStamp, $this->renderDay($data, $dateStamp, $idx));

    if ($idx !== null)
    {
      $rcp->focusFocus('.dateCard__users__'.$dateStamp.' form input[type="text"]');
    }

    $rcp->send();
  }

  /**
   * draw everythig that happens when a new user has been added / removed / changed
   * ________________________________________________________________
   */
  public function ajaxDrawUserChanged(array $data, int $stamp, int $code = 0, string $msg = ''): void
  {
    $rcp = new RecipeV();

    $rcp->domReplace('.dateCard__users__'.$stamp, $this->renderDay($data, $stamp));
    $rcp->cssHide('.dateCard__headline__'.$stamp, 'fadeOut', 'fadeIn', true);
    $rcp->domReplaceInner('.dateCard__headline__'.$stamp, $this->renderDayHeadlineInner($stamp, $code, $msg));
    $rcp->cssShow('.dateCard__headline__'.$stamp, 'fadeOut', 'fadeIn', true);

    $rcp->eventEmitRcp([
      'route' => '/zendo/index.php/mainC/refreshHeadline',
      'rcpStamp' => $stamp
    ], 2000);

    $rcp->send();
  }

  /**
   * draw fatal error
   * ________________________________________________________________
   */
  public function ajaxDrawFatalError(Exception $e): void
  {
    $rcp = new RecipeV();

    $rcp->toolLog($e->getMessage());

    $rcp->send();
  }

  /**
   * render headline
   * ________________________________________________________________
   */
  protected function renderDayHeadline(int $stamp, int $code = 0, string $msg = ''): string
  {
    $str = '';
    $str = '<h4 class="dateCard__headline__'.$stamp.'">'.$this->renderDayHeadlineInner($stamp, $code, $msg).'</h4>';

    return $str;
  }

  /**
   * render inside of headline
   * ________________________________________________________________
   */
  protected function renderDayHeadlineInner(int $stamp, int $code = 0, string $msg = ''): string
  {
    if (($code === 0) && ($msg == ''))
    {
      $msg = 'Donnerstag, '.date('d.m.Y', $stamp);
    }

    return $msg;
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
          $str .= '<form action="/zendo/index.php/mainC/updateUser">';
          $str .= '<input name="rcpIdx"   type="hidden" value="'.$i.'">';
          $str .= '<input name="rcpStamp" type="hidden" value="'.$dateStamp.'">';
          $str .= '<input name="rcpHash"  type="hidden" value="'.$data['hash'].'">';
          $str .= '<input name="rcpUser"
                          data-rcp-blur="/zendo/index.php/mainC/refreshDay"
                          data-rcp-stamp="'.$dateStamp.'"
                          data-rcp-idx="'.$i.'"
                          data-rcp-hash="'.$data['hash'].'"
                          type="text"
                          value="'.html_entity_decode($user, ENT_QUOTES).'">';

          $str .= '&nbsp;';
          if ($user != '')
          {
            $str .= '<input name="rcpSubm"
                            type="submit"
                            value="X">';
          }
          else
          {
            $str .= '<input name="rcpSubm"
                            type="submit"
                            value="OK">';
          }

          $str .= '</form>';
        }
        else
        {
          $str .= '<input class="dateCard__userInput"
                          data-rcp-focus="/zendo/index.php/mainC/userForm"
                          name="rcpInput"
                          data-rcp-idx="'.$i.'"
                          data-rcp-stamp="'.$dateStamp.'"
                          data-rcp-hash="'.$data['hash'].'"
                          type="text"
                          readonly
                          value="'.html_entity_decode($user, ENT_QUOTES).'">';
        }
        $str .= '</li>';
      }
    }
    else
    {
      $str .= '<li>';
      // "0" as int means we received this from the users end, "null" means we just render a default value
      if ($idx === 0)
      {
        $str .= '<form action="/zendo/index.php/mainC/updateUser">';
        $str .= '<input name="rcpIdx"   type="hidden" value="0">';
        $str .= '<input name="rcpStamp" type="hidden" value="'.$dateStamp.'">';
        $str .= '<input name="rcpHash"  type="hidden" value="'.$data['hash'].'">';
        $str .= '<input name="rcpUser"
                        type="text"
                        data-rcp-blur="/zendo/index.php/mainC/refreshDay"
                        data-rcp-stamp="'.$dateStamp.'"
                        data-rcp-hash="'.$data['hash'].'"
                        value="Entfällt.">';
        $str .= '&nbsp;';
        $str .= '<input name="rcpSubm"  type="submit" value="OK">';
        $str .= '</form>';
      }
      else
      { // $idx === null

        $str .= '<input class="dateCard__userInput"
                        data-rcp-focus="/zendo/index.php/mainC/userForm"
                        name="rcpInput"
                        data-rcp-idx="0"
                        data-rcp-stamp="'.$dateStamp.'"
                        data-rcp-hash="'.$data['hash'].'"
                        type="text"
                        readonly
                        value="Entfällt.">';
      }
      $str .= '</li>';
    }

    $str .= '</ul>';
    $str .= '</div>'; // dateCard__users

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
