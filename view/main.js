'use strict';

class ZENDOnnerstag
{

  constructor()
  {
    this.isReloading = false;
    window.addEventListener('blur', this.doHandle.bind(this), true);
  }

  doHandle(ev)
  {
    if (!this.isReloading)
    {
      if (ev.target.tagName == 'INPUT')
      {
        if (ev.target.classList.contains('dateCard__userInput'))
        {
          this.update(ev);
        }
      }
    }
  }

  async update(ev)
  {
    let stamp = ev.target.getAttribute('data-stamp');
    let idx = ev.target.getAttribute('data-user-idx');
    let user = ev.target.value;

    let nextStamp = (ev.relatedTarget != null) ? ev.relatedTarget.getAttribute('data-stamp') : null;
    let nextIdx = (ev.relatedTarget != null) ? ev.relatedTarget.getAttribute('data-user-idx') : null;

    let result = await fetch('index.php?op=updateUser&stamp='+stamp+'&idx='+idx+'&user='+encodeURIComponent(user));
    let ret = await result.json();

    this.isReloading = true; // prevent consumation of blur events that thappen due to replacing nodes
    document.querySelector(ret.target).outerHTML = ret.html;
    this.isReloading = false;

    let nextElem = document.querySelector(['input[data-stamp="'+nextStamp+'"][data-user-idx="'+nextIdx+'"']);
    if (nextElem)
    {
      nextElem.focus();
    }
  }
}

document.addEventListener('DOMContentLoaded', () =>
{
  let app = new ZENDOnnerstag();
});