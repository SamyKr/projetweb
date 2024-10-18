new Vue({
    el: '#app',
    data: {
        inventoryItems: [
            { name: 'Objet 1' },
            { name: 'Objet 2' },
            { name: 'Objet 3' },
            { name: 'Objet 4' },
        ],
        isInventoryVisible: true, // Etat pour gérer la visibilité de l'inventaire
    },
    methods: {
        toggleInventory() {
            this.isInventoryVisible = !this.isInventoryVisible; // Alterner la visibilité de l'inventaire
        },
        initMap() {
            // Initialisation de la carte centrée sur Paris
            const map = L.map('map').setView([48.8566, 2.3522], 12); // Coordonnées de Paris

            // Ajouter une couche de tuiles OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
        }
    },
    mounted() {
        this.initMap(); // Initialise la carte quand le composant est monté
    }
});

