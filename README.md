### 🏎️ **Drive to Escape** 🏁

Bienvenue sur notre jeu **Drive to Escape** ! 🚗💨

---

## 🛠️ **Prérequis** :

Pour jouer, assurez-vous d'avoir installé les éléments suivants :

- **Postgres** avec l'extension **postgis** (port 5432 par défaut)
- **PgAdmin** 
- **MAMP** (PHP version 7.4.33)
- **GeoServer** 
- **Mozilla ou Chrome**

---

## 📋 **Configuration** :

#### 1. **PgAdmin** 🗄️
- **Étape 1** : Créez une table map
- **Étape 2** : Ouvrez QueryTool et copiez collez le fichier table.sql


- Vérifiez que **user** est **postgres** et que votre **password** est également **postgres**. Dans le cas contraire il faudra modifier les lignes **9** et **10** du fichier **`projet_web/index.php`** et remplacer votre **user** ainsi que votre **password**. Pensez également à vérifier le **port** utilisé par Postgres.

✨ **PgAdmin, c'est fait !** ✨

---

#### 2. **MAMP** 🖥️
- Configurez correctement **MAMP** pour pointer vers le dossier **`projet_web`**.

---

#### 3. **GeoServer** 🌍
- Récupérez dans le dossier **`config/Geoserver`** le workspace **`carte_chaleur_projet`** et mettez le dans le dossier **`workspaces`** de votre application **Geoserver**
- Lancez le fichier **`startup.sh`** pour démarrer GeoServer.

---

## 🚀 **Lancement du Jeu** :

Une fois toutes les configurations terminées, vous êtes prêts à vous lancer dans **Drive to Escape** ! 🌐

---

### 🧩 **Solution** :

Pour vous aider dans votre quête, nous avons importé une couche avec **tous les circuits de F1**, utilisés ou non. Nous vous donnerons le pays pour chaque solution. Les circuits actifs seront affichés en **rouge** sur la carte, ce qui vous aidera à trouver l'endroit exact des objets. Une **carte de chaleur** est également disponible pour vous aider, elle est activable par une case **triche** et vous indique là où des objets sont présents dans le jeu. Vous êtes chronométré durant votre partie, le temps défile plus vite tant que la **carte de chaleur** est activée. 🗺️

#### **Guide des solutions** :

Si jamais vous êtes perdu, voici les solutions étape par étape du jeu.

- **France** 🇫🇷 (**2.3522 48.8566**) : Le jeu commence à Paris. 
- **Monaco** 🇲🇨 (**7.429 43.737**): Il faut ensuite vous rendre à Monaco rencontrer **Verstappen** qui va vous diriger vers la prochaine étape puisque son **Jet** est bloqué. 
- **Pays-Bas** 🇳🇱 (**4.541 52.389**): Arrivé aux Pays-Bas il faut résoudre un **code** qui va débloquer le **Jet**, le **code** est **2016**. 
- **Monaco** 🇲🇨 (**7.427 43.739**): Il faut ensuite retourner à Monaco pour ajouter le **Jet** à son inventaire. 
- **Mexique** 🇲🇽 (**-99.091 19.402**): Une fois le **Jet** dans l'inventaire il faut se rendre au **Mexique** pour débloquer la suite de l'histoire en débloquant la **Piste d'atterrissage**. 
- **Mexique** 🇲🇽 (**-99.091 19.402**): Une fois la **Piste d'atterrissage** débloquée, **Checo Perez** apparaît pour vous guider vers votre prochaine étape. 
- **Angleterre** 🇬🇧 (**-1.017 52.072**): Il faut alors se rendre en **Angleterre** pour parler à **Hamilton**, il ne vous répondra que si vous lui rendez son chien **Roscoe** qui est à **Monza**.
- **Italie** 🇮🇹 (**9.290 45.621**): Une fois à **Monza**, ajoutez **Roscoe** à votre inventaire et retournez en **Angleterre**.
- **Angleterre** 🇬🇧 (**-1.017 52.072**): Débloquer **Hamilton** pour qu'il vous aide pour la prochaine étape.
- **Hongrie** 🇭🇺 (**19.250 47.583**): Il faut alors se rendre en **Hongrie** rencontrer **Ocon**. C'est lui qui a volé le trophée. Il faut lui rendre le **casque de Stroll** pour qu'il vous dise où il l'a caché.
- **Canada** 🇨🇦 (**-73.525 45.506**): Il faut alors se rendre au **Canada** et ajouter le **casque** à votre inventaire, retournez ensuite en **Hongrie**.
- **Hongrie** 🇭🇺 (**19.250 47.583**): De retour en **Hongrie**, débloquez **Ocon**, il vous dira de vous rendre à **Singapour** pour retrouver le **trophée**.
- **Singapour** 🇸🇬 (**103.859 1.291**): À **Singapour** il faut résoudre un **code**, le **code** est **1950**.
- **Singapour** 🇸🇬 (**103.859 1.291**): Le **trophée** est alors débloqué, ajoutez-le à votre inventaire et ramenez-le au gala.
- **Arabie Saoudite** 🇸🇦 (**39.104 21.632**): Débloquer le gala en sélectionnant le **trophée** et ajoutez le à la **vitrine**.
- **Arabie Saoudite** 🇸🇦 (**39.104 21.632**): Le jeu est fini, vous pouvez arrêter le chrono en cliquant sur le dernier objet.


---

Bon jeu, et que le meilleur pilote gagne ! 🏆🎮

