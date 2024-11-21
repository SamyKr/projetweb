new Vue({
  el: '#app',
  data() {
    return {
      map: null, // Variable pour la carte
      isInventoryVisible: true, // Variable pour gérer l'affichage ou non de l'inventaire
      isHeatmapVisible: false, // Variable pour gérer l'affichage ou non de la carte de triche
      items: [ // Inventaire visuel de base
        { src: 'data/image/inventaire.png', alt: 'Emplacement vide 1' },
        { src: 'data/image/inventaire.png', alt: 'Emplacement vide 2' },
        { src: 'data/image/inventaire.png', alt: 'Emplacement vide 3' },
        { src: 'data/image/inventaire.png', alt: 'Emplacement vide 4' },
      ],
      inventory: ["", "", "", ""], // Inventaire de base
      elements_visible: [], // Images chargées de base
      loadedObjectIds: [], // ID des objets chargés pour la carte de chaleur  
      wmsLayer: null, // Variable pour initialiser la carte de triche
      selectedItem: {
        index: null,
        id: null,
      },
      elapsedTime: 0
      //interval: null
      //isRunning: false
    };
  },

  methods: {

    toggleInventory() { 
      this.isInventoryVisible = !this.isInventoryVisible;
    },
    
    // CARTE DE CHALEUR !!!
    toggleHeatmap(event) { // Permet d'afficher ou non la carte de triche grâce à une case à cocher
      const map = this.map;
    if (event.target.checked) {
        if (this.loadedObjectIds.length > 0) {
            map.addLayer(this.wmsLayer);
            this.isHeatmapVisible = true;
            this.updateChronoSpeed(true); // Doubler la vitesse du chronomètre
        } else {
            alert("Aucun objet chargé pour afficher la carte de chaleur !");
            event.target.checked = false;
        }
    } else {
        map.removeLayer(this.wmsLayer);
        this.isHeatmapVisible = false;
        this.updateChronoSpeed(false); // Revenir à la vitesse normale
    }
},

    

    loadHeatmapLayer() { // On charge la carte de triche en fonction des objets présents dans loadedObjectIds
      if (this.loadedObjectIds.length > 0) {
        const ids = this.loadedObjectIds.join(','); 
        if (!this.wmsLayer) { // Vérifie si la couche n'est pas encore créée
          this.wmsLayer = L.tileLayer.wms('http://localhost:8080/geoserver/carte_chaleur_projet/wms', {
            layers: 'carte_chaleur_projet:objet',
            format: 'image/png',
            transparent: true,
            opacity: 0.7,
            attribution: 'Données fournies par GeoServer',
            CQL_FILTER: `id IN (${ids})`
          });
        } else {
          this.wmsLayer.setParams({ CQL_FILTER: `id IN (${ids})` }); // Mettre à jour le filtre
        }
      } else {
        if (this.wmsLayer && !this.isHeatmapVisible) {
          this.map.removeLayer(this.wmsLayer); // Retirer la couche de chaleur si aucun objet
          this.wmsLayer = null; // Réinitialiser la ref
        }
      }
    },

// GESTION DE L'INVENTAIRE

  updateUIinventory() { // Modifie l'inventaire 
    this.inventory.forEach((item, index) => {
      if (item && item.nom_objet) { // Vérifie si l'item existe et a un nom
        this.$set(this.items, index, { 
          src: `data/image/${item.nom_objet}.png`, 
          alt: `Image de ${item.nom_objet}`
        });
      } else {
        this.$set(this.items, index, { 
          src: 'data/image/inventaire.png', 
          alt: `Emplacement vide ${index + 1}`
        });
      }
    });
  },  



  addToInventory(item, ajout, inv) { // Gère l'ajout d'un élément à l'inventaire
    //item = parseInt(item, 10); // Convertir en nombre entier
    let emptyIndex = this.inventory.indexOf(""); // Initialiser emptyIndex
    const objectToAdd = this.elements_visible.find(i => i.id === item);


    if (!objectToAdd) {
        alert("Objet non trouvé !");
        return;
    }

    // Vérifie si l'objet est bloqué ou si l'ID de selectedItem correspond à l'ID de blocage de l'objet
    if (objectToAdd.block !== null && objectToAdd.block !== this.selectedItem.id) {
        alert(`L'objet ${objectToAdd.nom_objet} est bloqué et ne peut pas être ajouté à l'inventaire. Débloquez-le d'abord.`);
        return; 
    }

    if (emptyIndex !== -1) {
        const unlockerIndex = this.inventory.findIndex(item => item.id === this.selectedItem.id);
        
        console.log("avant suppression :", this.inventory);
        // Si un objet à remplacer a été trouvé, le remplacer par une chaîne vide
        if (unlockerIndex !== -1) {
            this.$set(this.inventory, unlockerIndex, ""); // Remplacer l'objet par une chaîne vide
            console.log("après suppression :", this.inventory);
        }

        // Mettre à jour emptyIndex après la suppression
        emptyIndex = this.inventory.indexOf("");

        // Ajouter l'objet à l'inventaire à l'index vide si c'est un objet inventaire
        if (inv === true) {
        this.$set(this.inventory, emptyIndex, {
            nom_objet: objectToAdd.nom_objet,
            id: objectToAdd.id
        });}

        this.updateUIinventory(); // Fait la liaison entre inventaire et items (qui gère les images)
        this.deleteObject(item); // Supprime l'objet de la carte


        console.log("ici", typeof this.stringToArray(ajout))
        if (ajout !== 'null') {
          this.Ajout_objet(this.stringToArray(ajout));
        }


        
        if (objectToAdd.block == this.selectedItem.id) {
            this.selectedItem = { index: null, id: null };
        }
    } else {
        alert("L'inventaire est plein !");
    }
},  

// SELECTIONNER UN OBJET DANS L'INVENTAIRE

selectItem(index) {
  console.log(`Sélection d'un élément à l'index : ${index}`); // Log de l'index sélectionné

  // Afficher l'état de l'inventaire avant la sélection
  console.log("État de l'inventaire avant la sélection :", this.items);

  //const item = this.items[index]; // Obtenir l'élément correspondant à l'index sélectionné INUTILE MNT ?
  const inventoryItem = this.inventory[index]; // Obtenir l'élément de l'inventaire correspondant

  // Vérifie si un élément est déjà sélectionné
  if (this.selectedItem.index === null) {
    // Aucun élément n'est sélectionné, on sélectionne celui-ci
    this.$set(this.items, index, {
      ...this.items[index], // Conserver les autres propriétés
      border: '2px solid red', // Ajoute une bordure rouge
      backgroundColor: 'rgba(255, 0, 0, 0.2)' 
    });
    this.selectedItem = { index: index, id: inventoryItem ? inventoryItem.id : null }; // Met à jour l'élément sélectionné

  } else {
    // Un élément est déjà sélectionné, retirer la bordure de l'élément précédent
    this.$set(this.items, this.selectedItem.index, {
      ...this.items[this.selectedItem.index], // Conserve les autres propriétés
      border: 'none', // Enlève la bordure
      backgroundColor: 'transparent' // Réinitialise le fond
    });

    // Mettre à jour l'élément sélectionné
    this.$set(this.items, index, {
      ...this.items[index], // Conserver les autres propriétés
      border: '2px solid red', // Ajouter une bordure rouge
      backgroundColor: 'rgba(255, 0, 0, 0.2)' // Optionnel : ajouter un fond léger
    });

    // Mettre à jour l'élément sélectionné avec l'index et l'ID
    this.selectedItem = { index: index, id: inventoryItem ? inventoryItem.id : null }; 
    console.log("État de selectedItem :", this.selectedItem); // Log pour vérifier
  }
  console.log("État de selectedItem :", this.selectedItem); // Log pour vérifier
},




checkCode(id, code, ajout) {
  //id = parseInt(id, 10);

  currentElt = this.elements_visible.find(i => i.id === id);

  if (currentElt.code === code) {
    
    if (currentElt) {
      const eltADebloquer = this.elements_visible.find(i => i.block === currentElt.id);
      if (eltADebloquer) {
        eltADebloquer.block = null;
      }

    

      alert("Code correct !");
      this.deleteObject(id);
      if (ajout !== 'null') {
        this.Ajout_objet(this.stringToArray(ajout));
      }
    } else {
      console.warn("Élément non trouvé avec l'ID :", id);
    }
  } else {
    alert("Code incorrect, veuillez réessayer.");
  }
}
,






    displayMessage(item) { // Normalement elle ne sert à rien
      alert(`L'élément "${item}" est déjà dans l'inventaire !`);
    },

    startChrono(ajout) {
        this.elapsedTime = 0; 
        this.timerInterval = setInterval(() => {
            this.elapsedTime += 0.1;
        }, 100); 
        this.deleteObject('1'); 
        this.Ajout_objet(this.stringToArray(ajout))

    },


    stopChrono() {
      console.log("Arrêt du chrono");
      clearInterval(this.timerInterval);
      this.timerInterval = null;
   
      // Créer un formulaire caché pour envoyer le temps
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/save-time';  // La route pour traiter la requête POST
   
      // Créer un champ de formulaire pour le temps
      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'time';
      input.value = this.elapsedTime;  // Le temps écoulé
   
      form.appendChild(input);
   
      // Ajouter le formulaire à la page et l'envoyer
      document.body.appendChild(form);
      form.submit();  // Soumettre le formulaire
    },
   
  

    updateChronoSpeed(triche) {
      if (this.timerInterval) {
          clearInterval(this.timerInterval); // Arrêter le chronomètre en cours
          const interval = triche ? 60 : 120; // Doubler la vitesse si isDoubleSpeed est vrai
          this.timerInterval = setInterval(() => {
              this.elapsedTime += 0.1; // La même incrémentation reste
          }, interval);
      }
  },


    formatTime(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = Math.floor(seconds % 60);
        const tenths = Math.floor((seconds % 1) * 10); 
        return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}:${tenths}`;
    },


    popup(obj) {
      let content = `<div class="popup-content">`;
  
      // Ajouter la description uniquement si elle existe et n'est pas vide
      if (obj.description && obj.description.trim() !== "") {
          content += `<b>${obj.description}</b><br>`;
      }
  
      // Ajouter l'indice uniquement si il existe et n'est pas vide
      if (obj.indice && obj.indice.trim() !== "") {
          content += `<b>Indice: ${obj.indice}</b><br>`;
      }
  
      switch (obj.type) {
          case 'CODE':
              content += `
                  <input type="text" placeholder="Entrez le code" id="code_user">
                  <button onclick="checkCode('${obj.id}', document.getElementById('code_user').value, '${obj.ajout}')">Valider</button>
              `;
              break;
  
          case 'DEBLOQUANT':
              content += `
                  <button onclick="addToInventory('${obj.id}', '${obj.ajout}', true)">Ajouter à l'inventaire</button>
              `;
              break;
  
          case 'BLOQUE':
              content += `
                  <button onclick="addToInventory('${obj.id}', '${obj.ajout}', false)">Débloquer</button>
              `;
              break;
  
          default:
              if (obj.id === '1') {
                  content += `
                      <button onclick="startChrono('${obj.ajout}')">Commencer la partie</button>
                  `;
              } else if (obj.fin === 't') {
                  content += `
                      <button onclick="stopChrono()">Arrêter le chrono</button>
                  `;
              }
              break;
      }
  
      content += `</div>`;
      return content;
  },



  // Fonction pour ajouter les objets avec stockage de l'ID du marqueur
   Ajout_objet(ids) {

    const url = Array.isArray(ids) && ids.length > 0 
        ? `/objets?${new URLSearchParams({ ids: ids.join(',') })}`
        : '/objets';
    
    fetch(url)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
      })
    .then(data => {
      console.log("Données JSON analysées:", data);

      data.forEach(obj => {
        const latLng = [obj.latitude, obj.longitude];
        const imageSrc = `data/image/${obj.nom_objet}.png`;

        const customIcon = L.icon({
          iconUrl: imageSrc,
          iconSize: [100, 100], 
          iconAnchor: [25, 50], 
          popupAnchor: [0, -40] 
        });

        const marker = L.marker(latLng, { icon: customIcon }).addTo(this.map);
      

        const popupContent = this.popup(obj);

        marker.bindPopup(popupContent);

        this.loadedObjectIds.push(obj.id); // ajout a la carte de chaleur
        this.loadHeatmapLayer(); //MaJ carte de chaleur


        this.elements_visible.push({
          marker,
          zoom: obj.zoom, 
          block: obj.block, 
          id: obj.id, 
          nom_objet: obj.nom_objet, 
          code: obj.code,
          depart: obj.depart,
          ajout: obj.ajout
        });
      });
    })
    .catch(error => console.error('Erreur lors du chargement des objets:', error));
},

stringToArray(str) {
  // Vérifie si str est une chaîne et qu'elle commence par '{' et finit par '}'
  if (typeof str === 'string' && str.startsWith('{') && str.endsWith('}')) {
      // Supprime les accolades et sépare les valeurs par une virgule
      return str.slice(1, -1).split(',').map(Number); // Convertit les chaînes en nombres
  }
  return []; // Retourne un tableau vide si la chaîne n'est pas au bon format
},

deleteObject(id) {
  element= this.elements_visible.find(i => i.id === id);
  this.map.removeLayer(element.marker);
  //supprimer de elements_visible
  const index = this.elements_visible.findIndex(i => i.id === id);
  this.elements_visible.splice(index, 1);
  // supprimer dans la carte de chaleur
  this.loadedObjectIds.splice(index, 1);
  this.loadHeatmapLayer();

},


    
    

    
 
    initMap() { // on crée le fond de carte et on ajoute les objets de départ
      const map = L.map('map').setView([48.8566, 2.3522], 10); 
      this.map = map; 
  
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);
  
     
      this.Ajout_objet(); // On ajoute les objets de DEPART
  
      fetch('data/f1-circuits.geojson')
        .then(response => response.json())
        .then(data => {
          L.geoJSON(data, {
            style: function () {
              return { color: 'red' };
            }
          }).addTo(map);
        })
        .catch(error => console.error('Erreur lors du chargement du fichier GeoJSON:', error));
  
      map.on('zoomend', () => {
        this.toggleImagesVisibility(map);
      });
    },
      
    
    toggleImagesVisibility(map) { // On affiche un marqueur suivant le niveau de zoom
      const currentZoom = map.getZoom();

      this.elements_visible.forEach(({ marker, zoom }) => {
        if (currentZoom >= zoom) {
          map.addLayer(marker); 
        } else {
          map.removeLayer(marker); 
        }
      });
    }
  },  

  






  mounted() { // On "monte" la carte
    this.initMap();
    window.addToInventory = this.addToInventory.bind(this); // METHODE GLOBALE
    window.checkCode = this.checkCode.bind(this); // METHODE GLOBALE
    window.startChrono = this.startChrono.bind(this); // METHODE GLOBALE
    window.stopChrono = this.stopChrono.bind(this); // METHODE GLOBALE

  }
});
