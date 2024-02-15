let lastDate = new Date();

/**
 * Met-à-jour la date de l'interface TV
 * grâce à l'API JavaScript pour un gain de performances
 * @author Thomas Cardon
 */
function updateDate() {
  if (document.getElementById('date') === null) return;
  let d1 = lastDate.toLocaleDateString('fr-FR', { weekday: 'long' });
  let d2 = lastDate.toLocaleDateString('fr-FR', { year: 'numeric', month: 'long', day: 'numeric' });
  document.getElementById('date').innerHTML = d1 + '<br />' + d2;
}

/**
 * Met-à-jour l'heure de l'interface TV
 * grâce à l'API JavaScript pour un gain de performances
 * @author Thomas Cardon
 */
function updateTime() {
  lastDate = new Date();
  
  if (document.getElementById('time') !== null)
      document.getElementById('time').innerHTML = lastDate.toLocaleTimeString('fr-FR').slice(0, 5);
}

setTimeout(() => {
    setInterval(updateTime, 1000) // Appel de la fonction updateTime toute les 1000ms
    setInterval(updateDate, 60 * 1000); // Appel de la fonction updateDate toute les 60*1000 ms = 60s
    updateDate();
}, 1000 - new Date().getMilliseconds());

