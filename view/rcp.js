'use strict';

class RecipeJS
{

  constructor()
  {
    this.events = ['click', 'change', 'blur', 'focus', 'submit', 'rcp'];
  }

  init()
  {
    this.events.forEach((evName) =>
    {
      window.addEventListener(evName, this.handle.bind(this), true);
    });
  }

  handle(ev)
  {
    let url = '';
    let params = {};

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
          if (!this.isBlurOnSubmit(ev))
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

  isBlurOnSubmit(ev)
  {
    if (
      (ev.type == 'blur') &&
      (ev.relatedTarget) && (ev.relatedTarget.tagName == 'INPUT') && (ev.relatedTarget.type == 'submit') &&
      (ev.target.form == ev.relatedTarget.form)
    )
    {
      return true;
    }
    return false;
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
    js.forEach(async (rcp) =>
    {
      if (typeof this[rcp.action] === "function")
      {
        try
        {
          if (rcp.await && rcp.await == true)
          {
            await this[rcp.action](rcp);
          }
          else
          {
            this[rcp.action](rcp);
          }
        }
        catch(e)
        {
          console.log(e);
        }
      }
    });
  }

  dom(rcp)
  {
    return new Promise((resolve, reject) =>
    {
      let elem = document.querySelector(rcp.target);

      if (elem)
      {
        switch(rcp.method)
        {
          case 'replace':
            elem.outerHTML = rcp.html;
          break;

          case 'replaceInner':
            elem.innerHTML = rcp.html;
          break;
        }

        resolve();
      }
      else
      {
        reject('dom: "('+ rcp.target +')" yields no element.');
      }
    });
  }

  css(rcp)
  {
    return new Promise((resolve, reject) =>
    {
      let target = document.querySelector(rcp.target);

      if (target)
      {
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

        resolve();
      }
      else
      {
        reject('css: "('+ rcp.target +')" yields no elements.');
      }
    });
  }

  event(rcp)
  {
    return new Promise((resolve, reject) =>
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
        resolve();
      }, rcp.timeout);
    });
  }

  focus(rcp)
  {
    return new Promise((resolve, reject) =>
    {
      let el = document.querySelector(rcp.target);

      if (el)
      {
        switch (rcp.method)
        {
          case 'focus':
            el.focus();
          break;

          case 'blur':
            el.blur();
          break;
        }
        resolve();
      }
      else
      {
        reject('focus: "('+ rcp.target +')" yields no element.');
      }
    });
  }
}

document.addEventListener('DOMContentLoaded', () =>
{
  let app = new RecipeJS();
  app.init();
});