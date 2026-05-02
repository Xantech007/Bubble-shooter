const canvas = document.getElementById('game');
const ctx = canvas.getContext('2d');

let score = 0;
let gameRunning = false;
let gameSpeed = 5;

// Player
const player = {
    x: 100,
    y: 250,
    width: 50,
    height: 60,
    velY: 0,
    jumping: false,
    img: new Image()
};

// Background layers
const bgLayers = [
    { img: new Image(), speed: 1, y: 0 },
    { img: new Image(), speed: 2, y: 0 },
    { img: new Image(), speed: 4, y: 0 }
];

const ground = { y: 310, height: canvas.height - 310 };

// Obstacles
let obstacles = [];
let frame = 0;

// Controls
let keys = {};

// Load images (free/public links)
function loadImages() {
    // Runner character (you can replace with better sprite)
    player.img.src = 'https://i.imgur.com/8v5o5oY.png'; // Simple running character example

    // Backgrounds
    bgLayers[0].img.src = 'https://i.imgur.com/5z9vL8k.jpg'; // Far mountains
    bgLayers[1].img.src = 'https://i.imgur.com/7zK3pL2.png'; // Hills
    bgLayers[2].img.src = 'https://i.imgur.com/JzL9kPq.png'; // Trees/closer layer

    // You can find better free assets on itch.io or opengameart.org
}

function drawBackground() {
    ctx.fillStyle = '#1a1a2e';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    bgLayers.forEach(layer => {
        if (layer.img.complete) {
            const x = (frame * layer.speed) % canvas.width;
            ctx.drawImage(layer.img, -x, layer.y, canvas.width, 250);
            ctx.drawImage(layer.img, canvas.width - x, layer.y, canvas.width, 250);
        }
    });
}

function drawGround() {
    ctx.fillStyle = '#2d2d44';
    ctx.fillRect(0, ground.y, canvas.width, ground.height);
    
    // Simple grass line
    ctx.fillStyle = '#3cb371';
    ctx.fillRect(0, ground.y, canvas.width, 10);
}

function updatePlayer() {
    if (player.jumping) {
        player.velY += 1.2; // gravity
        player.y += player.velY;
        
        if (player.y >= ground.y - player.height) {
            player.y = ground.y - player.height;
            player.jumping = false;
            player.velY = 0;
        }
    }
}

function drawPlayer() {
    if (player.img.complete) {
        ctx.drawImage(player.img, player.x, player.y, player.width, player.height);
    } else {
        // Fallback rectangle
        ctx.fillStyle = '#e74c3c';
        ctx.fillRect(player.x, player.y, player.width, player.height);
    }
}

function createObstacle() {
    if (frame % 80 === 0) {
        obstacles.push({
            x: canvas.width,
            y: ground.y - 50,
            width: 40,
            height: 50,
            img: null
        });
    }
}

function updateObstacles() {
    for (let i = obstacles.length - 1; i >= 0; i--) {
        obstacles[i].x -= gameSpeed;
        
        if (obstacles[i].x + obstacles[i].width < 0) {
            obstacles.splice(i, 1);
            continue;
        }

        // Collision
        if (
            player.x < obstacles[i].x + obstacles[i].width &&
            player.x + player.width > obstacles[i].x &&
            player.y < obstacles[i].y + obstacles[i].height &&
            player.y + player.height > obstacles[i].y
        ) {
            gameOver();
        }
    }
}

function drawObstacles() {
    ctx.fillStyle = '#f39c12';
    obstacles.forEach(obs => {
        ctx.fillRect(obs.x, obs.y, obs.width, obs.height);
        // You can draw images here later
    });
}

function gameLoop() {
    if (!gameRunning) return;

    frame++;
    score += 1;
    document.getElementById('score').textContent = Math.floor(score / 10);

    // Increase difficulty
    if (score % 800 === 0) gameSpeed += 0.5;

    ctx.clearRect(0, 0, canvas.width, canvas.height);

    drawBackground();
    drawGround();
    updatePlayer();
    drawPlayer();
    createObstacle();
    updateObstacles();
    drawObstacles();

    requestAnimationFrame(gameLoop);
}

function jump() {
    if (!player.jumping && gameRunning) {
        player.jumping = true;
        player.velY = -22;
    }
}

function startGame() {
    document.getElementById('start-screen').style.display = 'none';
    gameRunning = true;
    score = 0;
    gameSpeed = 5;
    obstacles = [];
    frame = 0;
    player.y = ground.y - player.height;
    gameLoop();
}

function gameOver() {
    gameRunning = false;
    document.getElementById('final-score').textContent = Math.floor(score / 10);
    document.getElementById('game-over').style.display = 'block';
}

function restartGame() {
    document.getElementById('game-over').style.display = 'none';
    startGame();
}

// Controls
window.addEventListener('keydown', e => {
    if (e.key === ' ' || e.key === 'Spacebar') jump();
});

canvas.addEventListener('touchstart', e => {
    e.preventDefault();
    jump();
});

canvas.addEventListener('click', jump);

// Init
loadImages();
