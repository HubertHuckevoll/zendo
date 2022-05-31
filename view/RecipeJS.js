'use strict';

/**
 * RecipeJS
 * your final JavaScript library, lol
 *
 */
class RecipeJS
{

  /**
   * Konstruktor
   * ________________________________________________________________
   */
  constructor()
  {
    this.events = ['click', 'change', 'blur', 'focus', 'submit', 'rcp'];
    this.handler = this.handleEvents.bind(this);
    this.logging = true;

    this.requestCounter = 0;
    this.requestNo = 0;
    this.requestQueue = [];
  }

  /**
   * attach our event listeners
   * ________________________________________________________________
   */
  attach()
  {
    for (let evName of this.events)
    {
      window.addEventListener(evName, this.handler, true);
    };
  }

  /**
   * detach all of the event listeners
   * ________________________________________________________________
   */
  detach()
  {
    for (let evName of this.events)
    {
      window.removeEventListener(evName, this.handler, true);
    };
  }

  /**
   * main event handling entry point
   * ________________________________________________________________
   */
  handleEvents(ev)
  {
    let url = '';
    let params = {};

    switch (ev.type)
    {
      case 'submit':
        url = ev.target.getAttribute('action');
        params = this.readForm(ev);

        this.exec(url, params, this.requestNo);

        this.requestNo++;

        ev.preventDefault();
        return false;
      break;

      case 'rcp':
        url = ev.detail.route;
        params.target = ev.detail;

        this.exec(url, params, this.requestNo);

        this.requestNo++;
      break;

      default:
        let rcpEvent = 'data-rcp-' + ev.type;

        if ((ev.target.hasAttribute) && (ev.target.hasAttribute(rcpEvent)))
        {
          if (!this.isBlurOnSubmit(ev))
          {
            url = ev.target.getAttribute(rcpEvent);
            params = this.readData(ev);

            this.exec(url, params, this.requestNo);

            this.requestNo++;

            ev.preventDefault(); // must be called before any await
            return false;
          }
        }
      break;
    }
  }

  /**
   * Executor function
   * Is called whenever a registered event happens.
   *
   * AWAIT waits for the "request" to finish, but as the function
   * is called multiple times when multiple events happen at the
   * same time it doesn't wait for the requests to return "in order".
   *
   * That's why we use the request counter.
   * Whenever we issue a request, the request counter goes up
   * Whenever a request returns, the request counter goes down.
   * When all "concurrent" requests have returned, the request counter
   * should be zero again. All requests are  assigned a reuest number (reqNo)
   * and the result for each request is stored in the requestQueue with
   * the request number as index (see the "request" function).
   *
   * Once all requests have settled down we can start cooking the recipes
   * in the order of how the events happened.
   * ________________________________________________________________
   *
   */
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

  /**
   * do the request
   * ________________________________________________________________
   */
  async request(reqNo, url, params)
  {
    try
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

      this.log('Fetched data for request no', reqNo, ', is:', js);
      this.requestQueue[reqNo] = js;
    }
    catch (e)
    {
      this.log(e);
    }
  }

  /**
   * cook our recipes.
   * ________________________________________________________________
   */
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

            if (rcp.await == true)
            {
              await this[rcp.action](rcp);
            }
            else
            {
              this[rcp.action](rcp);
            }

            if (rcp.action !== 'event')
            {
              this.attach();
            }
          }
          catch (e)
          {
            this.log(e);
          }
        }
      }
    });
  }

  /**
   * DOM action(s)
   * ________________________________________________________________
   */
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

  /**
   * CSS action(s)
   * ________________________________________________________________
   */
  css(rcp)
  {
    let promises = [];
    let result = null;
    let nodes = document.querySelectorAll(rcp.target);
    this.log('Executing', rcp.action, '/', rcp.method, ' on ', rcp.target);

    if (nodes.length > 0)
    {
      switch (rcp.method)
      {
        case 'addClass':
          result = new Promise((resolve, reject) =>
          {
            nodes.forEach((node) =>
            {
              for (let cl of rcp.classes)
              {
                node.classList.add(cl);
              };
            });
            resolve();
          });
        break;

        case 'removeClass':
          result = new Promise((resolve, reject) =>
          {
            nodes.forEach((node) =>
            {
              for (let cl of rcp.classes)
              {
                node.classList.remove(cl);
              };
            });
            resolve();
          });
        break;

        case 'toggleClass':
          result = new Promise((resolve, reject) =>
          {
            nodes.forEach((node) =>
            {
              for (let cl of rcp.classes)
              {
                node.classList.toggle(cl);
              };
            });
            resolve();
          });
        break;

        case 'replaceClass':
          result = new Promise((resolve, reject) =>
          {
            nodes.forEach((node) =>
            {
              node.classList.replace(rcp.oldName, rcp.newName);
            });
            resolve();
          });
        break;

        case 'hide':
          nodes.forEach((node) =>
          {
            let elemP = new Promise(function(resolve, reject)
            {
              node.ontransitionend = () => resolve();
              node.classList.remove(rcp.showClass);
              node.classList.add(rcp.hideClass);
            });

            promises.push(elemP);
          });

          result = Promise.all(promises);
          promises = [];
        break;

        case 'show':
          nodes.forEach((node) =>
          {
            let elemP = new Promise(function(resolve, reject)
            {
              node.ontransitionend = () => resolve();
              node.classList.remove(rcp.hideClass);
              node.classList.add(rcp.showClass);
            });

            promises.push(elemP);
          });

          result = Promise.all(promises);
          promises = [];
        break;
      }
    }
    else
    {
      throw('css: "' + rcp.target + '" yields no elements.');
    }

    return result;
  }

  /**
   * EVENT action
   * ________________________________________________________________
   */
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

  /**
   * FOCUS action(s)
   * ________________________________________________________________
   */
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

  /**
   * ERROR action(s)
   * ________________________________________________________________
   */
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

  /**
   * NOP action
   * do nothing
   * useful when just sending state changes to the server
   * ________________________________________________________________
   */
  nop(rcp)
  {
    return new Promise((resolve, reject) =>
    {
      resolve();
    });
  }

  /**
   * RELOAD action
   * reload the page
   * ________________________________________________________________
   */
  reload(rcp)
  {
    return new Promise((resolve, reject) =>
    {
      window.location.reload();
      resolve();
    });
  }

  /**
   * check if the BLUR event happens while we are
   * hitting the submit button (in this we are gonna
   * cancel it)
   * ________________________________________________________________
   */
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

  /**
   * read data attributes from target node
   * and relatedTarget
   * ________________________________________________________________
   */
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

  /**
   * read form data from target
   * ________________________________________________________________
   */
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

  /**
   *
   * log variables when logging is on
   * ________________________________________________________________
   */
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