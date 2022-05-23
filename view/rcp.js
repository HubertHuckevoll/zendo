'use strict';

class RecipeJS
{

  constructor()
  {
    this.isReloading = false;

    this.events = ['click', 'change', 'blur', 'focus', 'submit', 'rcp'];
    this.events.forEach((evName) =>
    {
      window.addEventListener(evName, this.handle.bind(this), true);
    });
  }

  handle(ev)
  {
    let url = '';
    let params = {};

    if (!this.isReloading)
    {
      switch (ev.type)
      {
        case 'submit':
          url = ev.target.getAttribute('action');
          params.target = this.readFormdata(ev.target);
          if (ev.relatedTarget)
          {
            params.relatedTarget = this.readFormdata(ev.relatedTarget);
          }
          this.request(url, params);

          ev.preventDefault();
          return false;
        break;

        case 'rcp':
          url = ev.detail.route;
          params.target = ev.detail;
          this.request(url, params);
        break;

        default:
          let urlAttr = 'data-rcp-' + ev.type;
          if ((ev.target.hasAttribute) && (ev.target.hasAttribute(urlAttr)))
          {
            if (!
                 (
                   (ev.type == 'blur') &&
                   (ev.relatedTarget) && (ev.relatedTarget.tagName == 'INPUT') && (ev.relatedTarget.type == 'submit')
                 )
               )
            {
              url = ev.target.getAttribute(urlAttr);
              params.target = this.readDataset(ev.target);
              if (ev.relatedTarget)
              {
                params.relatedTarget = this.readDataset(ev.relatedTarget);
              }

              this.request(url, params);
            }

            ev.preventDefault();
            return false;
          }
        break;
      }
    }
  }

  readDataset(elem)
  {
    let result = [];
    result = Object.assign({}, elem.dataset);
    if (elem.value && elem.name)
    {
      result[elem.name] = elem.value;
    }
    return result;
  }

  readFormdata(form)
  {
    let result = {};

    Array.from(form.elements).forEach(elem =>
    {
      result[elem.name] = elem.value;
    });

    return result;
  }

  async request(url, params)
  {
    let reqData =
    {
      method: 'POST',
      mode: 'no-cors',
      cache: 'no-cache',
      headers:
      {
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      redirect: 'follow',
      referrerPolicy: 'no-referrer',
      body: JSON.stringify(params) // body data type must match "Content-Type" header
    };

    const resp = await fetch(url, reqData);
    const js = await resp.json();

    this.cook(js);
  }

  cook(js)
  {
    js.forEach((rcp) =>
    {
      if (typeof this[rcp.action] === "function")
      {
        this[rcp.action](rcp);
      }
    });
  }

  dom(rcp)
  {
    let elem = null;
    this.isReloading = true; // prevent consumation of events that thappen while we're busy replacing nodes (... but does it?)

    elem = document.querySelector(rcp.target);

    switch(rcp.method)
    {
      case 'replace':
        elem.outerHTML = rcp.html;
      break;

      case 'replaceInner':
        elem.innerHTML = rcp.html;
      break;
    }

    this.isReloading = false;
  }

  css(rcp)
  {
    let target = document.querySelector(rcp.target);

    switch(rcp.method)
    {
      case 'add':
        rcp.names.forEach((name) =>
        {
          target.classList.add(name);
        });
      break;

      case 'remove':
        rcp.names.forEach((name) =>
        {
          target.classList.remove(name);
        });
      break;

      case 'toggle':
        rcp.names.forEach((name) =>
        {
          target.classList.toggle(name);
        });
      break;

      case 'replace':
        target.classList.replace(rcp.oldName, rcp.newName);
      break;
    }
  }

  event(rcp)
  {
    // set up event
    let evDetails =
    {
      detail: rcp.detail,
      bubbles: true,
      cancelable: true
    }
    let ev = new CustomEvent(rcp.type, evDetails);

    // dispatch event
    window.setTimeout(() =>
    {
      window.dispatchEvent(ev);
    }, rcp.timeout);
  }

  focus(rcp)
  {
    let el = document.querySelector(rcp.target);
    if (el)
    {
      el.focus();
    }
  }

}

document.addEventListener('DOMContentLoaded', () =>
{
  let app = new RecipeJS();
});