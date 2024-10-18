let carIcon; // Variable pour l'icône
let car;
let obstacles = [];
let score = 0;
let gameOver = false;

function setup() {
    const canvas = createCanvas(800, 600);
    canvas.parent('game-container');
    car = new Car();
    carIcon = createDiv('<i class="fa-solid fa-car"></i>'); // Création de l'icône de voiture
    carIcon.style('font-size', '50px'); // Ajuster la taille de l'icône
    carIcon.position(car.x, car.y); // Positionner l'icône
    carIcon.id('car-icon'); // ID pour la mise à jour de position
}

function draw() {
    background(240);
    car.move();

    // Mettre à jour la position de l'icône
    carIcon.position(car.x, car.y);

    if (!gameOver) {
        if (frameCount % 60 === 0) {
            obstacles.push(new Obstacle());
        }

        for (let i = obstacles.length - 1; i >= 0; i--) {
            obstacles[i].show();
            obstacles[i].move();

            if (car.hits(obstacles[i])) {
                gameOver = true;
            }

            if (obstacles[i].offscreen()) {
                obstacles.splice(i, 1);
                score++;
            }
        }

        fill(0);
        textSize(24);
        text("Score: " + score, 10, 30);
    } else {
        fill(255, 0, 0);
        textSize(48);
        textAlign(CENTER);
        text("Game Over", width / 2, height / 2);
        textSize(24);
        text("Score: " + score, width / 2, height / 2 + 40);
    }
}

function keyPressed() {
    if (keyCode === LEFT_ARROW) {
        car.setDir(-5);
    } else if (keyCode === RIGHT_ARROW) {
        car.setDir(5);
    }
}

function keyReleased() {
    car.setDir(0);
}

// Classe pour la voiture
class Car {
    constructor() {
        this.width = 50; // Largeur pour le déplacement
        this.height = 100; // Hauteur pour le déplacement
        this.x = width / 2 - this.width / 2;
        this.y = height - this.height - 10;
        this.speed = 0;
    }

    move() {
        this.x += this.speed;
        this.x = constrain(this.x, 0, width - this.width);
    }

    setDir(dir) {
        this.speed = dir;
    }

    hits(obstacle) {
        return this.x < obstacle.x + obstacle.width &&
               this.x + this.width > obstacle.x &&
               this.y < obstacle.y + obstacle.height &&
               this.y + this.height > obstacle.y;
    }
}

// Classe pour les obstacles
class Obstacle {
    constructor() {
        this.width = random(20, 100);
        this.height = 20;
        this.x = random(width - this.width);
        this.y = 0;
    }

    move() {
        this.y += 5;
    }

    offscreen() {
        return this.y > height;
    }

    show() {
        fill(255, 0, 0);
        rect(this.x, this.y, this.width, this.height);
    }
}
