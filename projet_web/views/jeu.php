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
</head>

<body>
    <div id="app">
            <header class="bg-danger text-white text-center py-3">
            <h1>Drive to Escape</h1>
            <div>
                <label>
                    <input type="checkbox" @change="toggleHeatmap" />
                    Triche ?
                </label>
            </div>

            <div class="mt-2">
                <span><?= htmlspecialchars($_SESSION['pseudo']) ?> - Temps écoulé : {{ formatTime(elapsedTime) }}</span> 
            </div>

        </header>
        <div class="d-flex">
            <div id="map" class="flex-grow-1"></div>
        </div>
        <div class="text-center my-2">
            <button @click="toggleInventory" id="actionButton" class="btn btn-info">
                {{ isInventoryVisible ? '⬇️⬇️⬇️⬇️⬇️⬇️⬇️⬇️⬇️⬇️⬇️⬇️' : '⬆️⬆️⬆️⬆️⬆️⬆️⬆️⬆️⬆️⬆️⬆️⬆️' }}
            </button>
        </div>

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


                </ul>
            </div>
        </transition>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="assets/app.js"></script>
</body>
</html>
