import { RecipeJS } from '/coins/heads/v1/js/recipe/RecipeJS.js';

document.addEventListener('DOMContentLoaded', () =>
{
  // Install our dummy service worker - we are basically an "online only" app.
  // But using a service worker we gain some PWA capabilities
  if ('serviceWorker' in navigator)
  {
    navigator.serviceWorker.register('./swDummy.js');
  }

  let app = new RecipeJS();
  app.attach();
});