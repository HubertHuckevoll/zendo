'use strict';

class ZENDOnnerstag
{
  init()
  {
    let nodes = document.querySelectorAll('.dateCard__userInput');
    nodes.forEach((node) =>
    {
      node.addEventListener('blur', this.update.bind(this));
    });
  }

  async update(ev)
  {
    let stamp = ev.target.getAttribute('data-stamp');
    let idx = ev.target.getAttribute('data-user-idx');
    let user = ev.target.value;

    let result = await fetch('index.php?op=updateUser&stamp='+stamp+'&idx='+idx+'&user='+encodeURIComponent(user));
    let txt = await result.text();
    this.flashStatus(ev.target, txt);
  }

  flashStatus(target, message)
  {
    var toast = document.createTextNode(' ' + message);
    target.parentNode.insertBefore(toast, target.nextSibling);
    setTimeout(()=>
    {
      toast.remove();
    }, 2000);
  }
}

document.addEventListener('DOMContentLoaded', () =>
{
  let app = new ZENDOnnerstag();
  app.init();
});