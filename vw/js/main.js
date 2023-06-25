import { RecipeJS } from '/RecipeJS/RecipeJS.js';

document.addEventListener('DOMContentLoaded', () =>
{
  // install our service worker - we are basically an "online only" app,
  // but this way we gain some PWA capabilities
  if ('serviceWorker' in navigator)
  {
    navigator.serviceWorker.register('./view/js/sw.js');
  }

  let app = new RecipeJS();
  app.attach();
});