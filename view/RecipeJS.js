'use strict';

class RecipeJS
{

  constructor()
  {
    this.events = ['click', 'change', 'blur', 'focus', 'submit', 'rcp'];
    this.handler = this.handleEvents.bind(this);
    this.logging = false;
    this.requestCounter = 0;
    this.requestNo = 0;
    this.requestQueue = [];
  }

  attach()
  {
    for (let evName of this.events)
    {
      window.addEventListener(evName, this.handler, true);
    };
  }

  detach()
  {
    for (let evName of this.events)
    {
      window.removeEventListener(evName, this.handler, true);
    };
  }

  handleEvents(ev)
  {
    let url = '';
    let params = {};

    switch (ev.type)
    {
      case 'submit':
        url = ev.target.getAttribute('action');
        params = this.readForm(ev);
        this.requestNo++;

        this.exec(url, params, this.requestNo);

        ev.preventDefault();
        return false;
      break;

      case 'rcp':
        url = ev.detail.route;
        params.target = ev.detail;
        this.requestNo++;

        this.exec(url, params, this.requestNo);
      break;

      default:
        let rcpEvent = 'data-rcp-' + ev.type;

        if ((ev.target.hasAttribute) && (ev.target.hasAttribute(rcpEvent)))
        {
          if (!this.isBlurOnSubmit(ev))
          {
            url = ev.target.getAttribute(rcpEvent);
            params = this.readData(ev);

            this.requestNo++;
            this.exec(url, params, this.requestNo);

            ev.preventDefault(); // must be called before any await
            return false;
          }
        }
      break;
    }
  }

  async exec(url, params, reqNo)
  {
    try
    {
      this.requestCounter++;
      this.log('Requesting: ', reqNo, url, params);
      await this.request(reqNo, url, params); // don't fuck with the await, I dare you!!!
      this.requestCounter--;

      if (this.requestCounter == 0)
      {
        this.cook();
        this.requestQueue = [];
        this.requestNo = 0;
      }
    }
    catch (e)
    {
      this.log(e);
    }
  }

  async request(reqNo, url, params)
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

    let resp = await fetch(url, reqData);
    let js = await resp.json();

    this.log('Fetched data for request no ', reqNo, ', is ', js);
    this.requestQueue[reqNo] = js;

    return true;
  }

  cook()
  {
    this.requestQueue.forEach(async (js) =>
    {
      for (let rcp of js)
      {
        if (typeof this[rcp.action] === "function")
        {
          try
          {
            if (rcp.action !== 'event')
            {
              this.detach();
            }

            await this[rcp.action](rcp);

            if (rcp.action !== 'event')
            {
              this.attach();
            }
          }
          catch (e)
          {
            console.error(e);
          }
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
        this.log('Executing', rcp.action, '/', rcp.method, ' on ', rcp.target);

        switch (rcp.method)
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
        reject('dom: "' + rcp.target + '" yields no element.');
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
        this.log('Executing', rcp.action, '/', rcp.method, ' on ', rcp.target);

        switch (rcp.method)
        {
          case 'addClass':
            for (let cl of rcp.classes)
            {
              target.classList.add(cl);
            };
            break;

          case 'removeClass':
            for (let cl of rcp.classes)
            {
              target.classList.remove(cl);
            };
            break;

          case 'toggleClass':
            for (let cl of rcp.classes)
            {
              target.classList.toggle(cl);
            };
            break;

          case 'replaceClass':
            target.classList.replace(rcp.oldName, rcp.newName);
            break;
        }

        resolve();
      }
      else
      {
        reject('css: "' + rcp.target + '" yields no elements.');
      }
    });
  }

  event(rcp)
  {
    return new Promise((resolve, reject) =>
    {
      this.log('Executing', rcp.action, '/', rcp.type);

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
        this.log('Executing', rcp.action, '/', rcp.method, ' on ', rcp.target);

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
        reject('focus: "' + rcp.target + '" yields no element.');
      }
    });
  }

  error(rcp)
  {
    return new Promise((resolve, reject) =>
    {
      switch (rcp.method)
      {
        case 'console':
          this.log(rcp.msg);
        break;
      }
      resolve();
    });
  }

  nop(rcp)
  {
    return new Promise((resolve, reject) =>
    {
      resolve();
    });
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

  readData(ev)
  {
    let result = {};
    let el = ev.target;
    let relEl = null;
    if (ev.relatedTarget)
    {
      relEl = ev.relatedTarget;
    }

    result.target = Object.assign({}, el.dataset);
    if (el.value && el.name)
    {
      result.target[el.name] = el.value;
    }

    if (relEl)
    {
      result.relatedTarget = Object.assign({}, relEl.dataset);
      if (relEl.value && relEl.name)
      {
        result.relatedTarget[relEl.name] = relEl.value;
      }
    }

    return result;
  }

  readForm(ev)
  {
    let result = {};
    let form = ev.target;

    result.target = {}; // this is necessary!!!

    for (let formElem of form.elements)
    {
      result.target[formElem.name] = formElem.value;
    }

    return result;
  }

  log(...vars)
  {
    if (this.logging == true)
    {
      console.log(...vars);
    }
  }
}

document.addEventListener('DOMContentLoaded', () =>
{
  let app = new RecipeJS();
  app.attach();
});