<?php
session_start();
if (!isset($_SESSION['highscore'])) $_SESSION['highscore'] = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bubble Shooter Pro</title>

<style>
body{margin:0;background:radial-gradient(circle,#050505,#000);font-family:Arial;text-align:center;color:#fff;}
canvas{border:3px solid #333;border-radius:12px;box-shadow:0 0 30px #0ff;width:100%;max-width:500px;touch-action:none;}
button{padding:10px 15px;margin:5px;border:none;border-radius:6px;background:#00c3ff;color:#000;font-weight:bold;}
.popup{
    position:fixed;
    top:40%;
    left:50%;
    transform:translate(-50%,-50%);
    background:#111;
    padding:20px;
    border:2px solid #00c3ff;
    display:none;
}
</style>
</head>

<body>

<h2>🎯 Bubble Shooter Pro</h2>

<div>
Score: <span id="score">0</span> |
High Score: <span><?php echo $_SESSION['highscore']; ?></span>
</div>

<canvas id="game"></canvas>

<br>
<button onclick="restartGame()">Restart</button>
<button onclick="toggleSound()" id="soundBtn">🔊 Sound</button>

<div id="popup" class="popup"></div>

<audio id="shootSound" src="sounds/shoot.mp3"></audio>
<audio id="popSound" src="sounds/pop.mp3"></audio>
<audio id="bgMusic" src="sounds/bg.mp3" loop></audio>

<script>
const canvas = document.getElementById("game");
const ctx = canvas.getContext("2d");
const scoreEl = document.getElementById("score");

// ===== SESSION TRACKING =====
let startTime = Date.now();
let submitted = false;

// ===== BASE SIZE =====
const BASE_W = 420, BASE_H = 520;
canvas.width = BASE_W;
canvas.height = BASE_H;

// ===== RESPONSIVE =====
function resize(){
    let s = Math.min(window.innerWidth/BASE_W, window.innerHeight/BASE_H);
    canvas.style.width = BASE_W*s+"px";
    canvas.style.height = BASE_H*s+"px";
}
window.addEventListener("resize",resize);
resize();

// ===== SOUND =====
let sound = true;
const shootS = shootSound, popS = popSound, bg = bgMusic;
bg.volume = 0.3;

document.addEventListener("click",()=>{
    if(sound && bg.paused) bg.play().catch(()=>{});
},{once:true});

function toggleSound(){
    sound=!sound;
    if(sound){ bg.play().catch(()=>{}); soundBtn.innerText="🔊 Sound";}
    else{ bg.pause(); soundBtn.innerText="🔇 Mute";}
}

// ===== GAME =====
const SIZE=22, ROWS=10, COLS=9;
let grid=[],score=0,gameOver=false;
let shooter={x:210,y:480,angle:0};
let current,next;
let colors=["#ff4d4d","#4dff4d","#4d4dff","#ffff4d","#ff4dff","#4dffff"];

// ===== INIT =====
function init(){
    grid=[];
    for(let r=0;r<ROWS;r++){
        grid[r]=[];
        for(let c=0;c<COLS;c++){
            grid[r][c]=(r<5)?colors[Math.floor(Math.random()*colors.length)]:null;
        }
    }
}

// ===== DRAW =====
function draw(){
    ctx.fillStyle="#000";
    ctx.fillRect(0,0,canvas.width,canvas.height);

    for(let r=0;r<ROWS;r++){
        for(let c=0;c<COLS;c++){
            if(grid[r][c]){
                ctx.beginPath();
                ctx.arc(c*45+40,r*45+40,SIZE,0,Math.PI*2);
                ctx.fillStyle=grid[r][c];
                ctx.fill();
            }
        }
    }

    if(current){
        ctx.beginPath();
        ctx.arc(current.x,current.y,SIZE,0,Math.PI*2);
        ctx.fillStyle=current.color;
        ctx.fill();
    }

    if(gameOver){
        sendEarnings();

        ctx.fillStyle="rgba(0,0,0,0.7)";
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.fillStyle="#fff";
        ctx.fillText("GAME OVER",150,250);
    }
}

// ===== UPDATE =====
function update(){
    if(!current || !current.speed || gameOver) return;

    current.x += Math.cos(current.angle)*current.speed;
    current.y += Math.sin(current.angle)*current.speed;

    if(current.y < 40){
        place();
    }
}

// ===== SHOOT =====
function shoot(){
    if(current.speed||gameOver)return;
    current.speed=8;
    current.angle=shooter.angle;
}

// ===== PLACE =====
function place(){
    let col=Math.round((current.x-40)/45);
    let row=Math.round((current.y-40)/45);

    col=Math.max(0,Math.min(COLS-1,col));
    row=Math.max(0,Math.min(ROWS-1,row));

    grid[row][col]=current.color;

    score+=10;
    scoreEl.innerText=score;

    current=next;
    next={x:210,y:480,color:colors[Math.floor(Math.random()*colors.length)],speed:0};
}

// ===== EARNINGS =====
function sendEarnings(){
    if(submitted) return;
    submitted = true;

    let duration = Math.floor((Date.now()-startTime)/1000);
    if(duration < 3) return;

    fetch("../api/earn.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"duration="+duration+"&score="+score
    })
    .then(res=>res.json())
    .then(data=>{
        let popup=document.getElementById("popup");
        popup.style.display="block";

        if(data.status==="credited"){
            popup.innerHTML="💰 Earned: "+data.currency+" "+parseFloat(data.amount).toFixed(2);
        }else{
            popup.innerHTML="👤 Guest: "+data.currency+" "+parseFloat(data.amount).toFixed(2)+"<br>Login to claim!";
        }
    });
}

// ===== INPUT =====
canvas.addEventListener("mousemove",e=>{
    let rect=canvas.getBoundingClientRect();
    let x=e.clientX-rect.left;
    let y=e.clientY-rect.top;
    shooter.angle=Math.atan2(y-480,x-210);
});
canvas.addEventListener("click",shoot);

// ===== RESTART =====
function restartGame(){
    score=0;
    gameOver=false;
    submitted=false;
    startTime = Date.now();
    init();
}

// ===== LOOP =====
function loop(){
    update();
    draw();
    requestAnimationFrame(loop);
}

// START
init();
current={x:210,y:480,color:colors[0],speed:0};
next={x:210,y:480,color:colors[1],speed:0};
loop();
</script>

</body>
</html>
