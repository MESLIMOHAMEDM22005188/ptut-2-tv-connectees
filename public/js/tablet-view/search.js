/**
 * Quand le DOM (https://fr.wikipedia.org/wiki/Document_Object_Model#Aspects_techniques) est chargé,
 * on remplit la liste de suggestions de la barre de recherche avec les groupes et années
 */
 docReady(() => {
  fetch(HOST + '/wp-json/amu-ecran-connectee/v1/ade/')
  .then(res => res.json())
  .then(data => data.forEach(code => {
    entries.push({ value: code.title, action: () => alert('Action OK') })
  }))
  .catch(console.error);
});
