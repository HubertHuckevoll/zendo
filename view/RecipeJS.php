<?php

class RecipeJS
{
  protected $out = [];

  public function domReplace(string $target, string $html): void
  {
    array_push($this->out, [
      'action' => 'dom',
      'method' => 'replace',
      'target' => $target,
      'html' => $html
    ]);
  }

  public function domReplaceInner(string $target, string $html): void
  {
    array_push($this->out, [
      'action' => 'dom',
      'method' => 'replaceInner',
      'target' => $target,
      'html' => $html
    ]);
  }

  public function focusFocus(string $target): void
  {
    array_push($this->out, [
      'action' => 'focus',
      'method' => 'focus',
      'target' => $target
    ]);
  }

  public function focusBlur(string $target): void
  {
    array_push($this->out, [
      'action' => 'focus',
      'method' => 'blur',
      'target' => $target
    ]);
  }

  public function cssAddClass(string $target, array $classes): void
  {
    array_push($this->out, [
      'action' => 'css',
      'method' => 'addClass',
      'target' => $target,
      'classes' => $classes
    ]);
  }

  public function cssRemoveClass(string $target, array $classes): void
  {
    array_push($this->out, [
      'action' => 'css',
      'method' => 'removeClass',
      'target' => $target,
      'classes' => $classes
    ]);
  }

  public function cssToggleClass(string $target, array $classes): void
  {
    array_push($this->out, [
      'action' => 'css',
      'method' => 'toggleClass',
      'target' => $target,
      'classes' => $classes
    ]);
  }

  public function cssReplaceClass(string $target, string $oldName, string $newName): void
  {
    array_push($this->out, [
      'action' => 'css',
      'method' => 'addClass',
      'target' => $target,
      'oldName' => $oldName,
      'newName' => $newName
    ]);
  }

  public function eventRcp(array $detail, int $timeout): void
  {
    array_push($this->out, [
      'action' => 'event',
      'type' => 'rcp',
      'detail' => $detail,
      'timeout' => $timeout
    ]);
  }

  public function errorConsole(string $msg): void
  {
    array_push($this->out,
    [
      'action' => 'error',
      'method' => 'console',
      'msg' => $msg
    ]);
  }

  public function nop(): void
  {
    array_push($this->out,
    [
      'action' => 'nop'
    ]);
  }

  public function drop(): void
  {
    echo json_encode($this->out);
  }

}
