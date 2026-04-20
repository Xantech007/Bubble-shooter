<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Race Car Pro+</title>

<style>
body{
    margin:0;
    background:#000;
    text-align:center;
    color:#fff;
    font-family:Arial;
}
canvas{
    width:100%;
    max-width:400px;
    border:3px solid #333;
    border-radius:10px;
}
button{
    padding:10px;
    margin:10px;
    border:none;
    border-radius:5px;
    background:#00c3ff;
    font-weight:bold;
}
</style>
</head>

<body>

<h2>🏎️ Race Car Pro+</h2>
Score: <span id="score">0</span>

<br>
<canvas id="game" width="400" height="600"></canvas>
<br>
<button onclick="restartGame()">Restart</button>

<!-- SOUNDS -->
<audio id="engine" src="../sounds/engine.mp3" loop></audio>
<audio id="crash" src="../sounds/crash.mp3"></audio>
<audio id="music" src="../sounds/music.mp3" loop></audio>
<audio id="nitroS" src="../sounds/nitro.mp3"></audio>

<script>
const canvas = document.getElementById("game");
const ctx = canvas.getContext("2d");
const scoreEl = document.getElementById("score");

// ===== IMAGE PRELOADER =====
const images = {};
let loaded = 0;
const totalImages = 4;

function loadImage(name, src){
    const img = new Image();
    img.src = src;

    img.onload = () => {
        loaded++;
        if(loaded === totalImages){
            startGame();
        }
    };

    img.onerror = () => {
        console.error("Failed to load:", src);
    };

    images[name] = img;
}

// LOAD IMAGES (IMPORTANT PATHS)
loadImage("player", "../assets/images/player.png");
loadImage("enemy", "../assets/images/enemy.png");
loadImage("road", "../assets/images/road.png");
loadImage("city", "../assets/images/city.png");

// ===== SOUND =====
const engine = document.getElementById("engine");
const crash = document.getElementById("crash");
const music = document.getElementById("music");
const nitroS = document.getElementById("nitroS");

engine.volume = 0.3;
music.volume = 0.3;

document.addEventListener("click",()=>{
    engine.play().catch(()=>{});
    music.play().catch(()=>{});
},{once:true});

// ===== GAME STATE =====
let score=0, speed=4, gameOver=false, shake=0;
let nitro=false;

let car={x:180,y:500,w:40,h:70};
let obstacles=[];
let roadY=0;
let bgY=0;
let keys={};

// ===== DRAW BACKGROUND =====
function drawBG(){
    bgY += speed*0.3;
    if(bgY > 600) bgY = 0;

    ctx.drawImage(images.city,0,bgY-600,400,600);
    ctx.drawImage(images.city,0,bgY,400,600);
}

// ===== DRAW ROAD =====
function drawRoad(){
    roadY += speed;
    if(roadY > 600) roadY = 0;

    ctx.drawImage(images.road,100,roadY-600,200,600);
    ctx.drawImage(images.road,100,roadY,200,600);
}

// ===== DRAW CAR =====
function drawCar(){
    ctx.drawImage(images.player,car.x,car.y,car.w,car.h);
}

// ===== OBSTACLES =====
function spawnObstacle(){
    let lane=Math.floor(Math.random()*3);
    let x=120+lane*60;
    obstacles.push({x:x,y:-80,w:40,h:70});
}

function drawObstacles(){
    for(let i=0;i<obstacles.length;i++){
        let o=obstacles[i];
        o.y += speed;

        ctx.drawImage(images.enemy,o.x,o.y,o.w,o.h);

        // collision
        if(
            car.x < o.x+o.w &&
            car.x+car.w > o.x &&
            car.y < o.y+o.h &&
            car.y+car.h > o.y
        ){
            gameOver=true;
            crash.currentTime=0;
            crash.play();
            shake=20;
        }
    }

    obstacles = obstacles.filter(o=>o.y<700);
}

// ===== UPDATE =====
function update(){
    if(gameOver) return;

    if(keys["ArrowLeft"]) car.x -= 6;
    if(keys["ArrowRight"]) car.x += 6;

    if(nitro){
        speed = 8;
        nitroS.play().catch(()=>{});
    } else {
        speed += 0.002;
    }

    car.x = Math.max(110,Math.min(250,car.x));

    if(Math.random()<0.03) spawnObstacle();

    score++;
    scoreEl.innerText = score;

    engine.playbackRate = 1 + speed/10;
}

// ===== DRAW =====
function draw(){

    // loading screen
    if(loaded < totalImages){
        ctx.fillStyle="#000";
        ctx.fillRect(0,0,400,600);
        ctx.fillStyle="#fff";
        ctx.font="20px Arial";
        ctx.fillText("Loading...",140,300);
        return;
    }

    ctx.save();

    if(shake>0){
        ctx.translate(Math.random()*shake-shake/2,Math.random()*shake-shake/2);
        shake--;
    }

    ctx.clearRect(0,0,400,600);

    drawBG();
    drawRoad();
    drawCar();
    drawObstacles();

    if(nitro){
        ctx.fillStyle="rgba(0,255,255,0.2)";
        ctx.fillRect(0,0,400,600);
    }

    if(gameOver){
        ctx.fillStyle="rgba(0,0,0,0.7)";
        ctx.fillRect(0,0,400,600);
        ctx.fillStyle="#fff";
        ctx.font="28px Arial";
        ctx.fillText("CRASH!",140,280);
    }

    ctx.restore();
}

// ===== LOOP =====
function loop(){
    update();
    draw();
    requestAnimationFrame(loop);
}

// ===== START AFTER LOAD =====
function startGame(){
    loop();
}

// ===== CONTROLS =====
document.addEventListener("keydown",e=>{
    keys[e.key]=true;
    if(e.key===" ") nitro=true;
});
document.addEventListener("keyup",e=>{
    keys[e.key]=false;
    if(e.key===" ") nitro=false;
});

// MOBILE
canvas.addEventListener("touchmove",e=>{
    let rect=canvas.getBoundingClientRect();
    let x=e.touches[0].clientX-rect.left;
    car.x = x-20;
},{passive:false});

canvas.addEventListener("touchstart",()=>nitro=true);
canvas.addEventListener("touchend",()=>nitro=false);

// ===== RESTART =====
function restartGame(){
    score=0;
    speed=4;
    gameOver=false;
    obstacles=[];
    car.x=180;
}
</script>

</body>
</html>
