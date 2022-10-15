<?php

class dsgvo
{

  /**
   * render page header
   * ________________________________________________________________
   */
  public function draw(): void
  {
    $str = '';

    $str = '<!DOCTYPE html>'.
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
           '</head>'.
           '<body>'.

    $str .= '<header>';
    $str .= '<h1 class="mainHeadline">ZENDO<span class="mainHeadline__second">nnerstag</span></h1>';
    $str .= '<a href="mailto:'.$this->obfuscateStr('konstantin.meyer@gmail.com').'">Konstantin Meyer</a> [2022/4+] für <a href="https://www.lebendiges-zen.de/zendo-erfurt/" target="_blank">Lebendiges Zen Erfurt</a>.<br>';
    $str .= '</header>';

    $str .= '<main>';
    $str .= <<<DSGVOC
    <h1>Datenschutzerklärung</h1>
    <p>Verantwortlicher im Sinne der Datenschutzgesetze, insbesondere der EU-Datenschutzgrundverordnung (DSGVO), ist:</p>
    <p>Konstantin Meyer</p>
    <h2>Ihre Betroffenenrechte</h2>
    <p>Unter den angegebenen Kontaktdaten unseres Datenschutzbeauftragten können Sie jederzeit folgende Rechte ausüben:</p>
    <ul>
    <li>Auskunft über Ihre bei uns gespeicherten Daten und deren Verarbeitung (Art. 15 DSGVO),</li>
    <li>Berichtigung unrichtiger personenbezogener Daten (Art. 16 DSGVO),</li>
    <li>Löschung Ihrer bei uns gespeicherten Daten (Art. 17 DSGVO),</li>
    <li>Einschränkung der Datenverarbeitung, sofern wir Ihre Daten aufgrund gesetzlicher Pflichten noch nicht löschen dürfen (Art. 18 DSGVO),</li>
    <li>Widerspruch gegen die Verarbeitung Ihrer Daten bei uns (Art. 21 DSGVO) und</li>
    <li>Datenübertragbarkeit, sofern Sie in die Datenverarbeitung eingewilligt haben oder einen Vertrag mit uns abgeschlossen haben (Art. 20 DSGVO).</li>
    </ul>
    <p>Sofern Sie uns eine Einwilligung erteilt haben, können Sie diese jederzeit mit Wirkung für die Zukunft widerrufen.</p>
    <p>Sie können sich jederzeit mit einer Beschwerde an eine Aufsichtsbehörde wenden, z. B. an die zuständige Aufsichtsbehörde des Bundeslands Ihres Wohnsitzes oder an die für uns als verantwortliche Stelle zuständige Behörde.</p>
    <p>Eine Liste der Aufsichtsbehörden (für den nichtöffentlichen Bereich) mit Anschrift finden Sie unter: <a href="https://www.bfdi.bund.de/DE/Service/Anschriften/Laender/Laender-node.html" target="_blank" rel="noopener nofollow">https://www.bfdi.bund.de/DE/Service/Anschriften/Laender/Laender-node.html</a>.</p>
    <p></p><h2>Verwendung von Bibliotheken / Webfonts</h2>
    <p>Um unsere Inhalte browserübergreifend korrekt und grafisch ansprechend darzustellen, verwenden wir auf dieser Website "new.css" und den Webfont "Inter" zur Darstellung von Schriften.</p>
    <p>"news.css" finden Sie unter <a href="https://cdn.jsdelivr.net/npm/@exampledev/new.css@1/new.min.css">https://cdn.jsdelivr.net/npm/@exampledev/new.css@1/new.min.css</a> und "Inter" unter <a href="https://fonts.xz.style/serve/inter.css">https://fonts.xz.style/serve/inter.css</a>.</p>
    <p></p><h2>SSL-Verschlüsselung</h2>
    <p>Um die Sicherheit Ihrer Daten bei der Übertragung zu schützen, verwenden wir dem aktuellen Stand der Technik entsprechende Verschlüsselungsverfahren (z. B. SSL) über HTTPS.</p>
    <p></p><hr>
    <h2>Information über Ihr Widerspruchsrecht nach Art. 21 DSGVO</h2>
    <h3>Einzelfallbezogenes Widerspruchsrecht</h3>
    <p>Sie haben das Recht, aus Gründen, die sich aus Ihrer besonderen Situation ergeben, jederzeit gegen die Verarbeitung Sie betreffender personenbezogener Daten, die aufgrund Art. 6 Abs. 1 lit. f DSGVO (Datenverarbeitung auf der Grundlage einer Interessenabwägung) erfolgt, Widerspruch einzulegen; dies gilt auch für ein auf diese Bestimmung gestütztes Profiling im Sinne von Art. 4 Nr. 4 DSGVO.</p>
    <p>Legen Sie Widerspruch ein, werden wir Ihre personenbezogenen Daten nicht mehr verarbeiten, es sei denn, wir können zwingende schutzwürdige Gründe für die Verarbeitung nachweisen, die Ihre Interessen, Rechte und Freiheiten überwiegen, oder die Verarbeitung dient der Geltendmachung, Ausübung oder Verteidigung von Rechtsansprüchen.</p>
    <h3>Empfänger eines Widerspruchs</h3>
    <p>Konstantin Meyer</p>
    <hr>
    <h2>Änderung unserer Datenschutzbestimmungen</h2>
    <p>Wir behalten uns vor, diese Datenschutzerklärung anzupassen, damit sie stets den aktuellen rechtlichen Anforderungen entspricht oder um Änderungen unserer Leistungen in der Datenschutzerklärung umzusetzen, z.B. bei der Einführung neuer Services. Für Ihren erneuten Besuch gilt dann die neue Datenschutzerklärung.</p>
    <h2>Fragen an den Datenschutzbeauftragten</h2>
    <p>Wenn Sie Fragen zum Datenschutz haben, schreiben Sie uns bitte eine E-Mail oder wenden Sie sich direkt an die für den Datenschutz verantwortliche Person in unserer Organisation:</p>
    <p>Konstantin Meyer</p>
    <p><em>Die Datenschutzerklärung wurde mithilfe der activeMind AG erstellt, den Experten für <a href="https://www.activemind.de/datenschutz/datenschutzbeauftragter/" target="_blank" rel="noopener">externe Datenschutzbeauftragte</a> (Version #2020-09-30).</em></p>
    DSGVOC;
    $str .= '</main>';

    $str .= '<footer>';
    $str .= 'Konstantin Meyer [2022/4+] für <a href="https://www.lebendiges-zen.de/zendo-erfurt/" target="_blank">Lebendiges Zen Erfurt</a>.<br>';
    $str .= '</footer>';
    $str .= '</body></html>';

    echo $str;
  }

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