<?php

/**
 * RecipeJS - your final JavaSript Library
 * __________________________________________________________________
 */

class RecipeJS
{
  protected $out = [];

  public function domReplace(string $target, string $html): void
  {
    $this->addOutput(
    [
      'action' => 'dom',
      'method' => 'replace',
      'target' => $target,
      'html' => $html
    ]);
  }

  public function domReplaceInner(string $target, string $html): void
  {
    $this->addOutput(
    [
      'action' => 'dom',
      'method' => 'replaceInner',
      'target' => $target,
      'html' => $html
    ]);
  }

  public function domAttr(string $target, string $attrName, string $attrVal)
  {
    $this->addOutput(
    [
      'action' => 'dom',
      'method' => 'attr',
      'target' => $target,
      'attrName' => $attrName,
      'attrVal' => $attrVal
    ]);
  }

  public function focusFocus(string $target): void
  {
    $this->addOutput(
    [
      'action' => 'focus',
      'method' => 'focus',
      'target' => $target
    ]);
  }

  public function focusBlur(string $target): void
  {
    $this->addOutput(
    [
      'action' => 'focus',
      'method' => 'blur',
      'target' => $target
    ]);
  }

  public function cssAddClass(string $target, array $classes): void
  {
    $this->addOutput(
    [
      'action' => 'css',
      'method' => 'addClass',
      'target' => $target,
      'classes' => $classes
    ]);
  }

  public function cssRemoveClass(string $target, array $classes): void
  {
    $this->addOutput(
    [
      'action' => 'css',
      'method' => 'removeClass',
      'target' => $target,
      'classes' => $classes
    ]);
  }

  public function cssToggleClass(string $target, array $classes): void
  {
    $this->addOutput(
    [
      'action' => 'css',
      'method' => 'toggleClass',
      'target' => $target,
      'classes' => $classes
    ]);
  }

  public function cssReplaceClass(string $target, string $oldName, string $newName): void
  {
    $this->addOutput(
    [
      'action' => 'css',
      'method' => 'addClass',
      'target' => $target,
      'oldName' => $oldName,
      'newName' => $newName
    ]);
  }

  public function cssHide(string $target, string $hideClass, string $showClass, bool $await = false): void
  {
    $this->addOutput(
    [
      'action' => 'css',
      'method' => 'hide',
      'target' => $target,
      'showClass' => $showClass,
      'hideClass' => $hideClass,
      'await' => $await
    ]);
  }

  public function cssShow(string $target, string $hideClass, string $showClass, bool $await = false): void
  {
    $this->addOutput(
    [
      'action' => 'css',
      'method' => 'show',
      'target' => $target,
      'showClass' => $showClass,
      'hideClass' => $hideClass,
      'await' => $await
    ]);
  }

  public function eventRcp(array $detail, int $timeout): void
  {
    $this->addOutput(
    [
      'action' => 'event',
      'type' => 'rcp',
      'detail' => $detail,
      'timeout' => $timeout
    ]);
  }

  public function errorConsole(string $msg): void
  {
    $this->addOutput(
    [
      'action' => 'error',
      'method' => 'console',
      'msg' => $msg
    ]);
  }

  public function nop(): void
  {
    $this->addOutput(
    [
      'action' => 'nop'
    ]);
  }

  public function reload(): void
  {
    $this->addOutput(
    [
      'action' => 'reload'
    ]);
  }

  public function send(): void
  {
    echo json_encode($this->out);
  }

  protected function addOutput($data): void
  {
    array_push($this->out, $data);
  }

}
