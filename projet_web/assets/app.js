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
      elements_visible: [], // Images chargées de base
      wmsLayer: null, // Variable pour initialiser la carte de triche
      loadedObjectIds: [], // Id des objets chargés et présent sur la carte
      selectedItem: {
        index: null,
        id: null,
      },
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
        } else {
          alert("Aucun objet chargé pour afficher la carte de chaleur !");
          event.target.checked = false;
        }
      } else {
        map.removeLayer(this.wmsLayer);
        this.isHeatmapVisible = false;
      }
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

// GESTION DE L'INVENTAIRE

  updateUIinventory() { // Modifie l'inventaire 
    this.inventory.forEach((item, index) => {
      if (item && item.nom_objet) { // Vérifiez si l'item existe et a un nom
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


  addToInventory(item) { // Gère l'ajout d'un élément à l'inventaire
      item = parseInt(item, 10); // Convertir en nombre entier
      const emptyIndex = this.inventory.indexOf("");
      

      const objectToAdd = this.elements_visible.find(i => i.id === item);
        if (!objectToAdd) {
        alert("Objet non trouvé !");
        return;
    }
    

      // Vérifiez si l'objet est bloqué ou si l'ID de selectedItem correspond à l'ID de blocage de l'objet
      if (objectToAdd.block !== null && objectToAdd.block !== this.selectedItem.id) {
        alert(`L'objet ${objectToAdd.nom_objet} est bloqué et ne peut pas être ajouté à l'inventaire. Débloquez-le d'abord.`);
        return; // Ne pas ajouter l'objet
      }

     
  
      if (emptyIndex !== -1) {
          // Ajouter l'objet à l'inventaire
          this.$set(this.inventory, emptyIndex, {
            nom_objet: objectToAdd.nom_objet,
            id: objectToAdd.id
        });
        
          this.updateUIinventory(); // Fait la liaison entre inventaire et items (qui gere les images)
          


          //this.images.splice(this.images.indexOf(objectToAdd), 1); // On retire l'objet des images
          
          // Mettre à jour la carte de chaleur
          this.loadHeatmapLayer(); 
  
          // On remet à Null l'objet sélectionné si on a réussi à l'ajouter
          if (objectToAdd.block == this.selectedItem.id){
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

  // Vérifier si un élément est déjà sélectionné
  if (this.selectedItem.index === null) {
    // Aucun élément n'est sélectionné, on sélectionne celui-ci
    this.$set(this.items, index, {
      ...this.items[index], // Conserver les autres propriétés
      border: '2px solid red', // Ajouter une bordure rouge
      backgroundColor: 'rgba(255, 0, 0, 0.2)' 
    });
    this.selectedItem = { index: index, id: inventoryItem ? inventoryItem.id : null }; // Mettre à jour l'élément sélectionné

  } else {
    // Un élément est déjà sélectionné, retirer la bordure de l'élément précédent
    this.$set(this.items, this.selectedItem.index, {
      ...this.items[this.selectedItem.index], // Conserver les autres propriétés
      border: 'none', // Enlever la bordure
      backgroundColor: 'transparent' // Réinitialiser le fond
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






// AFFICHAGE DES ALERTES SPECIFIQUES
  

    displayMessage(item) { // Normalement elle ne sert à rien
      alert(`L'élément "${item}" est déjà dans l'inventaire !`);
    },


  


    
    // On va chercher un objets selon son ID dans la BDD pour l'ajouter sur la map
  
    Ajout_objet(map, ids) { // FONCTION POUR AJOUTER LES IMAGES SUR LA CARTE AVEC LES IDS DES OBJETS
      const params = new URLSearchParams({ ids: ids.join(',') }); // Convertir la liste d'IDs en chaîne
    
      fetch(`/objets?${params}`)
        .then(response => {
          if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
          }
          return response.json(); // Récupérer les données en JSON
        })
        .then(data => {
          console.log("Données JSON analysées:", data);
    
          data.forEach(obj => {
            const latLng = [obj.latitude, obj.longitude]; // Format correct pour L.marker
            const imageSrc = `data/image/${obj.nom_objet}.png`;
    
            // Créer une icône personnalisée
            const customIcon = L.icon({
              iconUrl: imageSrc,
              iconSize: [100, 100], 
              iconAnchor: [25, 50], 
              popupAnchor: [0, -40] 
            });
    
            // ON MET LES IMAGES SUR LA CARTE
            const marker = L.marker(latLng, { icon: customIcon }).addTo(map);



            
            // Créer le contenu de la popup avec un bouton
            const popupContent = `
          <div class="popup-content">
            <b>${obj.description}</b><br>
            <button onclick="(function() { addToInventory_Remove('${obj.id}', '${encodeURIComponent(marker)}') })()">Ajouter à l'inventaire</button>
          </div>`;

            
          // Lier le contenu de la popup au marqueur
            marker.bindPopup(popupContent);

          
          // ON ENREGISTRE LES IMAGES POUR LE CONTROLE DE VISIBILITE (ET SUREMENT POUR LE BLOQUAGE APRÈS ET L'INVENTAIRE)
          this.elements_visible.push({marker, zoom: obj.zoom, block: obj.block, id: obj.id, nom_objet: obj.nom_objet });

          

        });
      })
      .catch(error => console.error('Erreur lors du chargement des objets:', error));
              },



    addToInventory_Remove(item, marker) { // Gère l'ajout d'un élément à l'inventaire
      this.addToInventory(item);
      //this.map.removeLayer(marker); // On retire le marqueur de la carte
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
    window.addToInventory_Remove = this.addToInventory_Remove.bind(this); // METHODE GLOBALE

  }
});
