<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Race Car Pro</title>

<style>
body{
    margin:0;
    background:#000;
    font-family:Arial;
    text-align:center;
    color:#fff;
}
canvas{
    width:100%;
    max-width:400px;
    background:#111;
    border:3px solid #333;
    border-radius:10px;
    touch-action:none;
}
button{
    margin:10px;
    padding:10px;
    background:#00c3ff;
    border:none;
    border-radius:5px;
    font-weight:bold;
}
</style>
</head>

<body>

<h2>🏎️ Race Car Pro</h2>
Score: <span id="score">0</span>

<br>
<canvas id="game" width="400" height="600"></canvas>
<br>
<button onclick="restartGame()">Restart</button>

<audio id="engineSound" src="sounds/engine.mp3" loop></audio>
<audio id="crashSound" src="sounds/crash.mp3"></audio>

<script>
const canvas = document.getElementById("game");
const ctx = canvas.getContext("2d");

let score=0,speed=4,gameOver=false,shake=0;
let nitro=false;

const engine = engineSound;
const crash = crashSound;
engine.volume=0.3;

// start engine on first interaction
document.addEventListener("click",()=>{
    engine.play().catch(()=>{});
},{once:true});

let car={x:180,y:500,w:40,h:70};

let keys={},obstacles=[];

// ===== DRAW CAR (ENHANCED) =====
function drawCar(x,y){
    ctx.save();

    // glow
    ctx.shadowColor="#0ff";
    ctx.shadowBlur=10;

    // body
    let g=ctx.createLinearGradient(x,y,x,y+70);
    g.addColorStop(0,"#00f");
    g.addColorStop(1,"#00ffcc");

    ctx.fillStyle=g;
    ctx.fillRect(x,y,40,70);

    // windows
    ctx.fillStyle="#111";
    ctx.fillRect(x+5,y+10,30,20);

    // headlights
    ctx.fillStyle="yellow";
    ctx.fillRect(x+5,y,5,5);
    ctx.fillRect(x+30,y,5,5);

    ctx.restore();
}

// ===== ROAD =====
let roadY=0;
function drawRoad(){
    ctx.fillStyle="#222";
    ctx.fillRect(100,0,200,600);

    // side lines
    ctx.strokeStyle="#fff";
    ctx.lineWidth=4;
    ctx.beginPath();
    ctx.moveTo(100,0);
    ctx.lineTo(100,600);
    ctx.moveTo(300,0);
    ctx.lineTo(300,600);
    ctx.stroke();

    // center dash
    ctx.lineWidth=3;
    for(let i=0;i<600;i+=40){
        ctx.beginPath();
        ctx.moveTo(200,i+roadY);
        ctx.lineTo(200,i+20+roadY);
        ctx.stroke();
    }

    roadY += speed;
    if(roadY>40) roadY=0;
}

// ===== OBSTACLES =====
function spawnObstacle(){
    let lane=Math.floor(Math.random()*3);
    let x=120+lane*60;

    obstacles.push({x,y:-80,w:40,h:70});
}

function drawObstacles(){
    for(let i=0;i<obstacles.length;i++){
        let o=obstacles[i];
        o.y += speed;

        ctx.fillStyle="#ff3333";
        ctx.fillRect(o.x,o.y,o.w,o.h);

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

    obstacles=obstacles.filter(o=>o.y<700);
}

// ===== UPDATE =====
function update(){
    if(gameOver) return;

    if(keys["ArrowLeft"]) car.x-=6;
    if(keys["ArrowRight"]) car.x+=6;

    if(nitro){
        speed=8;
    } else {
        speed+=0.002;
    }

    car.x=Math.max(110,Math.min(250,car.x));

    if(Math.random()<0.03) spawnObstacle();

    score++;
    document.getElementById("score").innerText=score;

    // engine pitch simulation
    engine.playbackRate = 1 + speed/10;
}

// ===== DRAW =====
function draw(){
    ctx.save();

    if(shake>0){
        ctx.translate(Math.random()*shake- shake/2, Math.random()*shake- shake/2);
        shake--;
    }

    ctx.clearRect(0,0,400,600);

    drawRoad();
    drawCar(car.x,car.y);
    drawObstacles();

    if(nitro){
        ctx.fillStyle="rgba(0,255,255,0.3)";
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
    e.preventDefault();
    let rect=canvas.getBoundingClientRect();
    let x=e.touches[0].clientX-rect.left;
    car.x=x-20;
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

// START
loop();
</script>

</body>
</html>
