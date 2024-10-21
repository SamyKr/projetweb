
new Vue({
    el: '#app',
    data: {
        isInventoryVisible: true, // État pour gérer la visibilité de l'inventaire
        items: ['Objet 1', 'Objet 2', 'Objet 3', 'Objet 4', 'Objet 5'], // Liste des objets dans l'inventaire
    },
    methods: {
        toggleInventory() {
            this.isInventoryVisible = !this.isInventoryVisible; // Alterner la visibilité de l'inventaire
        },
        changeFirstItemName() {
            // Cette méthode modifie le premier objet en "Coucou"
            this.$set(this.items, 0, 'Coucou');
        },
        initMap() {
            // Initialisation de la carte avec OpenStreetMap
            const map = L.map('map').setView([26.031766, 50.510539], 13); // Positionnement de la carte (Sakhir)

            // Ajouter une couche OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Exemple d'ajout d'un marqueur sur la carte
            const marker = L.marker([26.031766, 50.510539]).addTo(map);
            
            // Action au clic sur le marqueur
            marker.on('click', () => {
                // Quand le marqueur est cliqué, l'objet 1 devient "Coucou"
                this.changeFirstItemName();
            });


            // Chargement et ajout des données GeoJSON pour f1-locations.geojson
            fetch('data/f1-locations.geojson')
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    style: function (feature) {
                        return {color: 'red'}; // Style des lignes
                    },
                    onEachFeature: function (feature, layer) {
                        // Action lors du clic sur un élément
                        layer.on('click', function (e) {
                            // Créer un popup avec des infos personnalisées (exemple : feature.properties.name)
                            if (feature.properties && feature.properties.name) {
                                layer.bindPopup("Nom du lieu : " + feature.properties.name).openPopup();
                            } else {
                                layer.bindPopup("Lieu sans nom").openPopup();
                            }
                            console.log("Vous avez cliqué sur : ", feature.properties.name);
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
                        return {color: 'red'}; // Style des lignes
                    },
                    onEachFeature: (feature, layer) => {
                        // Ajouter une action lorsqu'on clique sur une entité GeoJSON
                        layer.on('click', () => {
                            this.changeFirstItemName(); // Appelle la méthode qui change le nom de l'objet 1
                        });
                    }
                }).addTo(map);
            })
            .catch(error => console.error('Erreur lors du chargement du fichier GeoJSON:', error));
        }
    },
    mounted() {
        this.initMap(); // Initialise la carte quand le composant est monté
    }
});
