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
        inventory: ["","","",""] 
      };
    },
    methods: {
      toggleInventory() {
        this.isInventoryVisible = !this.isInventoryVisible; // Alterner la visibilité de l'inventaire
      },
      

      updateUIinventory() {
      
        this.inventory.forEach((item, index) => {
          if (item) {
            this.$set(this.items, index, { 
              src: `data/image/${item}.png`, 
            });
          }
        });
      },      

      addToInventory(item) {
        // Trouver la première position vide ("")
        const emptyIndex = this.inventory.indexOf(""); 
      
        if (emptyIndex !== -1) {
          // Si une position vide est trouvée, ajouter l'élément à cet emplacement
          this.$set(this.inventory, emptyIndex, item);
          this.updateUIinventory();
          console.log(this.inventory);  
        } else {
          // Si l'inventaire est plein (pas de position vide), afficher un message
          alert("L'inventaire est plein !");
        }
      },

      deleteFromInventory(item) {
        // Trouver l'index de l'élément à supprimer
        const index = this.inventory.indexOf(item);
        
        // Si l'élément est trouvé, le remplacer par une chaîne vide ("")
        if (index !== -1) {
          this.$set(this.inventory, index, ""); // Remplacer par une chaîne vide
        }
            this.updateUIinventory();
      },
      


      displayMessage(item) {
        // Afficher un message si l'élément est déjà dans l'inventaire
        alert(`L'élément "${item}" est déjà dans l'inventaire !`);
      },
      initMap() {
        // Initialisation de la carte avec OpenStreetMap
        const map = L.map('map').setView([26.031766, 50.510593], 13); // Positionnement de la carte (Sakhir)
  
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
  
        // Chargement et ajout des données GeoJSON pour f1-locations.geojson
        fetch('data/f1-locations.geojson')
          .then(response => response.json())
          .then(data => {
            L.geoJSON(data, {
              style: function (feature) {
                return { color: 'red' };
              },
              onEachFeature: (feature, layer) => {
                // Action lors du clic sur un élément
                layer.on('click', () => {
                  
                  
                  
                  
                  if (feature.properties && feature.properties.name) {
                    if (this.inventory.includes(feature.properties.name)) {
                      this.displayMessage(feature.properties.name);
                    } else {
                      this.addToInventory("F1");
                    }
                    layer.bindPopup("Nom du lieu : " + feature.properties.name).openPopup();
                  } else {
                    layer.bindPopup("Lieu sans nom").openPopup();
                  }
                });
              }
            }).addTo(map);
          })
          .catch(error => console.error('Erreur lors du chargement du fichier GeoJSON:', error));
  
    
        fetch('data/f1-circuits.geojson')
          .then(response => response.json())
          .then(data => {
            L.geoJSON(data, {
              style: function (feature) {
                return { color: 'red' };
              }
            }).addTo(map);
          })
          .catch(error => console.error('Erreur lors du chargement du fichier GeoJSON:', error));
      }
    },
    mounted() {
      this.initMap();
    }
  });
  