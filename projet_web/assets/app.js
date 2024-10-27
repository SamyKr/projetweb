new Vue({
  el: '#app',
  data() {
    return {
      isInventoryVisible: true,
      items: [
        { src: 'data/image/inventaire.png', alt: 'Description image 1' },
        { src: 'data/image/inventaire.png', alt: 'Description image 2' },
        { src: 'data/image/inventaire.png', alt: 'Description image 3' },
        { src: 'data/image/inventaire.png', alt: 'Description image 4' },
      ],
      inventory: ["", "", "", ""], // Placeholders for inventory slots
      images: [], // To hold markers or images for visibility control
    };
  },
  methods: {
    toggleInventory() {
      this.isInventoryVisible = !this.isInventoryVisible;
    },
    
    updateUIinventory() {
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
    
    addToInventory(item) {
      const emptyIndex = this.inventory.indexOf(""); 
      if (emptyIndex !== -1) {
        this.$set(this.inventory, emptyIndex, item);
        this.updateUIinventory();
      } else {
        alert("L'inventaire est plein !");
      }
    },
    
    deleteFromInventory(item) {
      const index = this.inventory.indexOf(item);
      if (index !== -1) {
        this.$set(this.inventory, index, ""); 
        this.updateUIinventory();
      }
    },
    
    displayMessage(item) {
      alert(`L'élément "${item}" est déjà dans l'inventaire !`);
    },
    
    initMap() {
      const map = L.map('map').setView([43.737, 7.429], 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      const objetDepart = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
      this.Ajout_objet(map, objetDepart);

      // Load GeoJSON for circuits
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

      // ON ECOUTE LA CARTE POUR LE ZOOM TOUT LE TEMPS
      map.on('zoomend', () => {
        this.toggleImagesVisibility(map);
      });
    },
    
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
                <button onclick="app.addToInventory('${obj.nom_objet}')">Ajouter à l'inventaire</button>
              </div>`;
              
            // Lier le contenu de la popup au marqueur
            marker.bindPopup(popupContent);
            
            // ON ENREGISTRE LES IMAGES POUR LE CONTROLE DE VISIBILITE (ET SUREMENT POUR LE BLOQUAGE APRÈS ET L'INVENTAIRE)
            this.images.push({ marker, zoom: obj.zoom, block: obj.block, id: obj.id, nom_objet: obj.nom_objet });
          });
        })
        .catch(error => console.error('Erreur lors du chargement des objets:', error));
    },
    
    toggleImagesVisibility(map) { // FONCTION POUR CONTROLER LA VISIBILITE DES IMAGES
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

  mounted() {
    this.initMap();
  }
});

