new Vue({
  el: '#app',
  data() {
    return {
      isInventoryVisible: true, // Variable pour gérer l'affichage ou non de l'inventaire
      isHeatmapVisible: false, // Variable pour gérer l'affichage ou non de la carte de triche
      items: [ // Inventaire visuel de base
        { src: 'data/image/inventaire.png', alt: 'Emplacement vide 1' },
        { src: 'data/image/inventaire.png', alt: 'Emplacement vide 2' },
        { src: 'data/image/inventaire.png', alt: 'Emplacement vide 3' },
        { src: 'data/image/inventaire.png', alt: 'Emplacement vide 4' },
      ],
      inventory: ["", "", "", ""], // Inventaire de base
      images: [], // Images chargées de base
      wmsLayer: null, // Variable pour initialiser la carte de triche
      loadedObjectIds: [], // Id des objets chargés et présent sur la carte
      ownedObjectIds: [], // Id des objets débloqués et donc "possédés"
    };
  },

  methods: {

    toggleInventory() { 
      this.isInventoryVisible = !this.isInventoryVisible;
    },
    

    toggleHeatmap(event) { // Permet d'afficher ou non la carte de triche grâce à une case à cocher
      const map = this.map;
      if (event.target.checked) {
        if (this.loadedObjectIds.length > 0) {
          map.addLayer(this.wmsLayer);
          this.isHeatmapVisible = true;
        } else {
          alert("Aucun objet chargé pour afficher la carte de chaleur !");
          event.target.checked = false;
        }
      } else {
        map.removeLayer(this.wmsLayer);
        this.isHeatmapVisible = false;
      }
    },

    updateUIinventory() { // Modifie l'inventaire 
      this.inventory.forEach((item, index) => {
        if (item) {
          this.$set(this.items, index, { 
            src: `data/image/${item}.png`, 
            alt: `Image de ${item}`
          });
        } else {
          this.$set(this.items, index, { 
            src: 'data/image/inventaire.png', 
            alt: `Emplacement vide ${index + 1}`
          });
        }
      });
    },


    addToInventory(item, marker) { // Gère l'ajout d'un élément à l'inventaire
      const emptyIndex = this.inventory.indexOf("");
      
      // Vérifiez si l'objet peut être ajouté (débloqué)
      const objectToAdd = this.images.find(i => i.nom_objet.toLowerCase() === item); 
      if (!objectToAdd) {
          alert("Objet non trouvé !");
          return;
      }
  
      // Vérifiez si l'objet est bloqué
      if (objectToAdd.block !== null && !this.ownedObjectIds.includes(objectToAdd.block)) {
          alert(`L'objet ${objectToAdd.nom_objet} est bloqué et ne peut pas être ajouté à l'inventaire. Débloquez-le d'abord.`);
          return; // Ne pas ajouter l'objet
      }
  
      if (emptyIndex !== -1) {
          // Ajouter l'objet à l'inventaire
          this.$set(this.inventory, emptyIndex, item);
          this.updateUIinventory(); // Appel de la fonction qui met à jour l'inventaire
  
          this.map.removeLayer(marker); // On retire le marqueur de la carte
  
          // Retirer l'objet des images et des loadedObjectIds
          const idIndex = this.loadedObjectIds.indexOf(objectToAdd.id);
          if (idIndex !== -1) {
              this.loadedObjectIds.splice(idIndex, 1); // On retire l'ID de loadedObjectIds
          }
  
          this.images.splice(this.images.indexOf(objectToAdd), 1); // On retire l'objet des images
          
          // Mettre à jour la carte de chaleur
          this.loadHeatmapLayer(); 
  
          // Vérifier si un objet a été débloqué
          this.checkUnlockedObjects(objectToAdd.id); // Ajoutez cette ligne pour vérifier les objets débloqués
  
      } else {
          alert("L'inventaire est plein !");
      }

      console.log("Objets possédés :", this.ownedObjectIds);
  },
  

    displayMessage(item) { // Normalement elle ne sert à rien
      alert(`L'élément "${item}" est déjà dans l'inventaire !`);
    },


    initMap() { // on crée le fond de carte et on ajoute les objets de départ
      const map = L.map('map').setView([43.737, 7.429], 10); 
      this.map = map; 
  
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);
  
      const objetDepart = [1, 2, 3];
      this.Ajout_objet(map, objetDepart);
  
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


    // Grosse fonction, grosse étape:
    // On va chercher un objets selon son ID pour l'ajouter sur la map
    Ajout_objet(map, ids) {
      const params = new URLSearchParams({ ids: ids.join(',') }); 
    
      fetch(`/objets?${params}`)
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
            const imageSrc = `data/image/${obj.nom_objet.toLowerCase()}.png`;
    
            const customIcon = L.icon({
              iconUrl: imageSrc,
              iconSize: [100, 100],
              iconAnchor: [25, 50],
              popupAnchor: [0, -40]
            });
    
            const marker = L.marker(latLng, { icon: customIcon });
    
            // On ajoute un popup qui va permettre de jouer
            const popupContent = `
                <div class="popup-content">
                  <b>${obj.description}</b><br>
                  ${obj.description === 'CODE À 4 CHIFFRES' ? 
                    `<input type="text" id="codeInput" placeholder="Entrez le code" maxlength="4" />` : 
                    `<button class="add-to-inventory" data-item="${obj.nom_objet.toLowerCase()}">Ajouter à l'inventaire</button>`}
                </div>`;

              marker.bindPopup(popupContent);


              marker.on("popupopen", () => {
                if (obj.description === 'CODE À 4 CHIFFRES') {
                  const codeInput = document.getElementById('codeInput');
                  codeInput.addEventListener("keydown", (event) => {
                    if (event.key === "Enter") {
                      const userInputCode = codeInput.value.trim();
                      console.log("Code entré par l'utilisateur :", userInputCode);
                      console.log("Code attendu :", obj.code);
                      if (userInputCode === obj.code) {
                        alert("Code correct ! Vous avez débloqué l'objet.");

                        // Ajoutez l'ID de l'objet à la liste des objets possédés
                        this.ownedObjectIds.push(obj.id);
                        console.log("Objets possédés :", this.ownedObjectIds);

                        // Vérifiez si des objets sont débloqués par cet ID
                        this.checkUnlockedObjects(obj.id);
                      } else {
                        alert("Code incorrect. Réessayez.");
                      }
                    }
                  });
                } else {
                  document.querySelector(".add-to-inventory").addEventListener("click", () => {
                    this.addToInventory(obj.nom_objet.toLowerCase(), marker);
                  });
                }
              });
              
    
            // On ajoute l'objet à l'inventaire une fois cliqué
            this.images.push({ marker, zoom: obj.zoom, block: obj.block, id: obj.id, nom_objet: obj.nom_objet });
            this.loadedObjectIds.push(obj.id);
          });
    
          if (this.loadedObjectIds.length > 0) {
            this.loadHeatmapLayer();
          }
        })
        .catch(error => console.error('Erreur lors du chargement des objets:', error));
    },


    // Méthode pour vérifier et ajouter les objets débloqués
    checkUnlockedObjects(blockingId) {
      // Requête pour récupérer les objets dont le block est égal à blockingId
      const unlockedObjects = this.objects.filter(object => object.block === blockingId);
      
      unlockedObjects.forEach(unlockedObject => {
        if (!this.ownedObjectIds.includes(unlockedObject.id)) {
          alert(`Vous avez débloqué l'objet : ${unlockedObject.nom_objet}!`);
          this.addToInventory(unlockedObject.nom_objet.toLowerCase()); // Ajout à l'inventaire
          this.ownedObjectIds.push(unlockedObject.id); // Ajoutez l'ID à ownedObjectIds
          this.ownedObjectIds.push(obj.id);
        }
      });
    },

    
    loadHeatmapLayer() { // On charge la carte de triche en fonction des objets présents dans loadedObjectIds
      if (this.loadedObjectIds.length > 0) {
        const ids = this.loadedObjectIds.join(','); 
        if (!this.wmsLayer) { // Vérifiez si la couche n'est pas encore créée
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
        if (this.wmsLayer) {
          this.map.removeLayer(this.wmsLayer); // Retirer la couche de chaleur si aucun objet
          this.wmsLayer = null; // Réinitialiser la référence
        }
      }
    },

      
    
    toggleImagesVisibility(map) { // On affiche un marqueur suivant le niveau de zoom
      const currentZoom = map.getZoom();

      this.images.forEach(({ marker, zoom }) => {
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
  }
});
