<?php

class dsgvoV extends cAppV
{

  /**
   * render page header
   * ________________________________________________________________
   */
  public function drawPage(): void
  {
    $this->setTag('mail', $this->obfuscateStr('konstantin.meyer@gmail.com'));
    $this->draw();
  }

  /**
   * obfuscate an email Address
   * ________________________________________________________________
   */
  protected function obfuscateStr($str)
  {
    $result = '';
    $result = preg_replace_callback('/./', function($m)
    {
      return '&#'.ord($m[0]).';';
    },
    $str);

    return $result;
  }

}

?>