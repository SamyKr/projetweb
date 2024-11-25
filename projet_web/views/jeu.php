<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Jeu</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/popup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">
</head>

<body>
    <div id="app">
        <!-- En-tête de la page avec titre et case pour triche -->
        <header class="text-white text-center py-3">
            <h1><i class="bi bi-joystick"></i> Formula 1: Drive to Escape </h1>
            <div>
                <label>
                    <input type="checkbox" @change="toggleHeatmap" />
                    Triche ?
                </label>
            </div>

            <!-- Affichage de l'utilisateur connecté et du temps écoulé -->
            <div class="mt-2">
                <i class="bi bi-person-circle"></i> 
                <span><?= htmlspecialchars($_SESSION['pseudo']) ?> - Temps écoulé : {{ formatTime(elapsedTime) }}</span> 
            </div>
        </header>

        <!-- Zone contenant la carte Leaflet -->
        <div class="d-flex">
            <div id="map" class="flex-grow-1"></div>
        </div>

        <!-- Bouton pour afficher ou cacher l'inventaire -->
        <div class="text-center my-2">
            <button @click="toggleInventory" id="actionButton" class="btn btn-sm">
                <i v-if="!isInventoryVisible" class="bi bi-caret-up-fill"></i>
                <i v-if="isInventoryVisible" class="bi bi-caret-down-fill"></i>
            </button>
        </div>

        <!-- Transition pour afficher ou masquer l'inventaire avec un effet de fondu -->
        <transition name="fade">
            <div v-if="isInventoryVisible" class="inventory p-3">
                <ul class="list-group">
                    <li class="list-group-item" 
                        v-for="(item, index) in items" 
                        :key="index" 
                        :style="{ backgroundColor: item.backgroundColor }" 
                        @click="selectItem(index)" 
                    >
                        <img :src="item.src" :alt="item.alt" class="img-fluid" :style="{ border: item.border }" />
                    </li>
                </ul>
            </div>
        </transition>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="assets/app.js"></script>
</body>
</html>
