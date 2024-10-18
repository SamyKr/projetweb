<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte OpenLayers Sombre</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v7.2.2/ol.css" type="text/css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            width: 100%;
            height: calc(100% - 50px); /* Réduit la hauteur pour faire de la place pour l'inventaire */
        }
        #inventory {
            width: 100%;
            height: 50px; /* Hauteur de la barre d'inventaire */
            background-color: rgba(0, 0, 0, 0.8); /* Fond sombre et transparent */
            color: white;
            display: flex;
            align-items: center;
            padding: 0 10px; /* Padding pour l'espacement intérieur */
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.5); /* Ombre pour le style */
        }
    </style>
</head>
<body>

<div id="map"></div>
<div id="inventory">
    <span>Inventaire : Aucune info pour le moment</span> <!-- Texte par défaut de l'inventaire -->
</div>

<script src="https://cdn.jsdelivr.net/npm/ol@v7.2.2/dist/ol.js"></script>
<script>
    // Création de la carte avec style sombre
    var map = new ol.Map({
        target: 'map', 
        layers: [
            new ol.layer.Tile({
                source: new ol.source.XYZ({
                    url: 'https://basemaps.cartocdn.com/dark_all/{z}/{x}/{y}.png' // CartoDB Dark Matter tiles
                })
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([2.3522, 48.8566]), // Centre initial sur Paris
            zoom: 15
        })
    });

    // Création d'une source vectorielle et d'un calque de marqueur
    var vectorSource = new ol.source.Vector();
    var markerLayer = new ol.layer.Vector({
        source: vectorSource
    });
    map.addLayer(markerLayer); // Ajoute le calque de marqueur à la carte

    // Fonction pour ajouter un marqueur
    function addMarker(lon, lat, name) {
        var markerStyle = new ol.style.Style({
            image: new ol.style.Circle({
                radius: 10, // Rayon du marqueur
                fill: new ol.style.Fill({ color: 'red' }), // Couleur du marqueur
                stroke: new ol.style.Stroke({ color: 'white', width: 2 }) // Bordure blanche
            })
        });

        var marker = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat])), // Position du marqueur
            name: name // Ajout d'un attribut "name"
        });
        marker.setStyle(markerStyle);

        vectorSource.addFeature(marker); // Ajoute le marqueur à la source vectorielle
    }

    // Ajout d'un objet à Paris
    addMarker(2.3522, 48.8566, 'Paris'); // Marqueur de Paris

    // Écoute des clics sur la carte
    map.on('singleclick', function(evt) {
        // Vérifiez si un marqueur a été cliqué
        map.forEachFeatureAtPixel(evt.pixel, function(feature) {
            if (feature instanceof ol.Feature) {
                updateInventory(feature.get('name')); // Met à jour l'inventaire avec le nom du marqueur
                vectorSource.removeFeature(feature); // Retire le marqueur de la source
            }
        });
    });

    // Exemple de mise à jour de l'inventaire
    function updateInventory(message) {
        var inventory = document.getElementById('inventory');
        inventory.innerHTML = '<span>Inventaire : ' + message + '</span>';
    }
</script>

</body>
</html>
