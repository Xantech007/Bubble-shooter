<?php
include "../inc/header.php";
include "../inc/navbar.php";

if (!isset($_SESSION['highscore'])) $_SESSION['highscore'] = 0;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.page{
    max-width:650px;
    margin:30px auto;
    text-align:center;
}

/* HEADER */
.game-top{
    background:#fff;
    padding:15px;
    border-radius:10px;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
    margin-bottom:15px;
}

.game-top h2{
    margin:0;
}

/* SCORE */
.score-bar{
    margin-top:8px;
    color:#555;
}

/* CANVAS */
.canvas-wrap{
    display:flex;
    justify-content:center;
}

canvas{
    border:2px solid #ddd;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
    touch-action:none;
}

/* BUTTONS */
.controls{
    margin-top:15px;
}

button{
    padding:10px 15px;
    margin:5px;
    border:none;
    border-radius:6px;
    background:#00aaff;
    color:#fff;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#008ecc;
}

/* POPUP */
.popup{
    position:fixed;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
    background:#fff;
    padding:25px;
    border-radius:12px;
    box-shadow:0 10px 40px rgba(0,0,0,0.2);
    display:none;
    z-index:999;
}
</style>

<div class="page">

<div class="game-top">
    <h2><i class="fa-solid fa-bullseye"></i> Bubble Shooter</h2>
    <div class="score-bar">
        Score: <span id="score">0</span> |
        High Score: <?php echo $_SESSION['highscore']; ?>
    </div>
</div>

<div class="canvas-wrap">
    <canvas id="game"></canvas>
</div>

<div class="controls">
    <button onclick="restartGame()"><i class="fa-solid fa-rotate"></i> Restart</button>
    <button onclick="toggleSound()" id="soundBtn">
        <i class="fa-solid fa-volume-high"></i> Sound
    </button>
</div>

</div>

<div id="popup" class="popup"></div>

<audio id="shootSound" src="../sounds/shoot.mp3"></audio>
<audio id="popSound" src="../sounds/pop.mp3"></audio>
<audio id="bgMusic" src="../sounds/bg.mp3" loop></audio>

<script>
const canvas = document.getElementById("game");
const ctx = canvas.getContext("2d");
const scoreEl = document.getElementById("score");

// ===== EARNINGS =====
let startTime = Date.now();
let submitted = false;

// ===== BASE SIZE =====
const BASE_W = 420, BASE_H = 520;
canvas.width = BASE_W;
canvas.height = BASE_H;

// ===== RESPONSIVE =====
function resize(){
    let scale = Math.min(
        window.innerWidth/(BASE_W+40),
        window.innerHeight/(BASE_H+200)
    );
    scale = Math.min(scale,1);

    canvas.style.width = BASE_W*scale+"px";
    canvas.style.height = BASE_H*scale+"px";
}
window.addEventListener("resize",resize);
resize();

// ===== SOUND =====
let sound=true;
const shootS=shootSound,popS=popSound,bg=bgMusic;
bg.volume=0.3;

document.addEventListener("click",()=>{
    if(sound && bg.paused) bg.play().catch(()=>{});
},{once:true});

function toggleSound(){
    sound=!sound;
    if(sound){
        bg.play().catch(()=>{});
        soundBtn.innerHTML='<i class="fa-solid fa-volume-high"></i> Sound';
    }else{
        bg.pause();
        soundBtn.innerHTML='<i class="fa-solid fa-volume-xmark"></i> Mute';
    }
}

// ===== GAME =====
const SIZE=22,ROWS=10,COLS=9;
let grid=[],score=0,gameOver=false;
let shooter={x:210,y:480,angle:0};
let current,next;
let colors=["#ff4d4d","#4dff4d","#4d4dff","#ffff4d","#ff4dff","#4dffff"];

let effects=[],stars=[];
for(let i=0;i<60;i++){
    stars.push({x:Math.random()*BASE_W,y:Math.random()*BASE_H,s:Math.random()*2});
}

function rand(){return colors[Math.floor(Math.random()*colors.length)];}

function init(){
    grid=[];
    for(let r=0;r<ROWS;r++){
        grid[r]=[];
        for(let c=0;c<COLS;c++){
            grid[r][c]=(r<5)?rand():null;
        }
    }
}

// ===== DRAW BG =====
function drawBG(){
    ctx.fillStyle="#000";
    ctx.fillRect(0,0,canvas.width,canvas.height);

    ctx.fillStyle="#fff";
    stars.forEach(s=>{
        ctx.globalAlpha=0.3;
        ctx.beginPath();
        ctx.arc(s.x,s.y,s.s,0,Math.PI*2);
        ctx.fill();
        s.y+=0.2;
        if(s.y>BASE_H) s.y=0;
    });
    ctx.globalAlpha=1;
}

// ===== BUBBLE =====
function drawBubble(x,y,color,scale=1,alpha=1){
    ctx.save();
    ctx.globalAlpha=alpha;
    ctx.shadowColor=color;
    ctx.shadowBlur=15;

    let r=SIZE*scale;

    let g=ctx.createRadialGradient(x-r*0.4,y-r*0.4,r*0.2,x,y,r);
    g.addColorStop(0,"#fff");
    g.addColorStop(0.3,color);
    g.addColorStop(1,"#000");

    ctx.beginPath();
    ctx.arc(x,y,r,0,Math.PI*2);
    ctx.fillStyle=g;
    ctx.fill();

    ctx.beginPath();
    ctx.arc(x-r*0.3,y-r*0.3,r*0.3,0,Math.PI*2);
    ctx.fillStyle="rgba(255,255,255,0.4)";
    ctx.fill();

    ctx.restore();
}

// ===== DRAW =====
function draw(){
    drawBG();

    for(let r=0;r<ROWS;r++){
        for(let c=0;c<COLS;c++){
            if(grid[r][c]){
                drawBubble(c*45+40,r*45+40,grid[r][c]);
            }
        }
    }

    ctx.setLineDash([5,5]);
    ctx.strokeStyle="rgba(255,255,255,0.4)";
    ctx.beginPath();
    ctx.moveTo(shooter.x,shooter.y);
    ctx.lineTo(
        shooter.x+Math.cos(shooter.angle)*300,
        shooter.y+Math.sin(shooter.angle)*300
    );
    ctx.stroke();
    ctx.setLineDash([]);

    ctx.strokeStyle="#0ff";
    ctx.lineWidth=10;
    ctx.beginPath();
    ctx.moveTo(shooter.x,shooter.y);
    ctx.lineTo(
        shooter.x+Math.cos(shooter.angle)*50,
        shooter.y+Math.sin(shooter.angle)*50
    );
    ctx.stroke();

    if(current) drawBubble(current.x,current.y,current.color);
    if(next) drawBubble(80,490,next.color);

    drawFX();

    if(gameOver){
        sendEarnings();

        ctx.fillStyle="rgba(0,0,0,0.8)";
        ctx.fillRect(0,0,canvas.width,canvas.height);
        ctx.fillStyle="#fff";
        ctx.font="26px Arial";
        ctx.fillText("GAME OVER",120,250);
        ctx.fillText("Score: "+score,140,300);
    }
}

// ===== EFFECTS =====
function drawFX(){
    for(let i=effects.length-1;i>=0;i--){
        let e=effects[i];
        drawBubble(e.x,e.y,"#fff",1+(20-e.life)/10,e.life/20);
        e.life--;
        if(e.life<=0) effects.splice(i,1);
    }
}

// ===== UPDATE =====
function update(){
    if(!current || !current.speed || gameOver) return;

    current.x+=Math.cos(current.angle)*current.speed;
    current.y+=Math.sin(current.angle)*current.speed;

    if(current.x<SIZE || current.x>canvas.width-SIZE){
        current.angle=Math.PI-current.angle;
    }

    if(current.y<40){ place(); return; }

    for(let r=0;r<ROWS;r++){
        for(let c=0;c<COLS;c++){
            if(grid[r][c]){
                let dx=current.x-(c*45+40);
                let dy=current.y-(r*45+40);
                if(Math.sqrt(dx*dx+dy*dy)<SIZE*2){
                    place();
                    return;
                }
            }
        }
    }
}

// ===== SHOOT =====
function shoot(){
    if(current.speed||gameOver)return;

    if(sound){
        shootS.currentTime=0;
        shootS.play().catch(()=>{});
    }

    current.speed=10;
    current.angle=shooter.angle;
}

// ===== PLACE =====
function place(){
    let col=Math.round((current.x-40)/45);
    let row=Math.round((current.y-40)/45);

    col=Math.max(0,Math.min(COLS-1,col));
    row=Math.max(0,Math.min(ROWS-1,row));

    grid[row][col]=current.color;

    match(row,col);

    current=next;
    next={x:210,y:480,color:rand(),speed:0};

    if(row>=ROWS-1) gameOver=true;
}

// ===== MATCH =====
function match(r,c){
    let color=grid[r][c];
    let stack=[[r,c]],seen={},m=[];

    while(stack.length){
        let [y,x]=stack.pop();
        let k=y+"_"+x;
        if(seen[k]) continue;
        seen[k]=true;

        if(grid[y] && grid[y][x]===color){
            m.push([y,x]);
            [[1,0],[-1,0],[0,1],[0,-1]].forEach(d=>stack.push([y+d[0],x+d[1]]));
        }
    }

    if(m.length>=3){
        if(sound){
            popS.currentTime=0;
            popS.play().catch(()=>{});
        }

        m.forEach(([y,x])=>{
            effects.push({x:x*45+40,y:y*45+40,life:20});
            grid[y][x]=null;
        });

        score+=m.length*10;
        scoreEl.innerText=score;
    }
}

// ===== EARNINGS =====
function sendEarnings(){
    if(submitted) return;
    submitted=true;

    let duration=Math.floor((Date.now()-startTime)/1000);
    if(duration<3) return;

    fetch("../api/earn.php",{
        method:"POST",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},
        body:"duration="+duration+"&score="+score
    })
    .then(res=>res.json())
    .then(data=>{
        let popup=document.getElementById("popup");
        popup.style.display="block";
        popup.innerHTML=`<h3>Earned</h3><p>${data.currency} ${data.amount}</p>`;
    });
}

// ===== INPUT =====
canvas.addEventListener("mousemove",e=>{
    let r=canvas.getBoundingClientRect();
    let x=(e.clientX-r.left)*(canvas.width/r.width);
    let y=(e.clientY-r.top)*(canvas.height/r.height);
    shooter.angle=Math.atan2(y-shooter.y,x-shooter.x);
});
canvas.addEventListener("click",shoot);

// ===== RESTART =====
function restartGame(){
    score=0;
    gameOver=false;
    submitted=false;
    startTime=Date.now();
    init();
    current={x:210,y:480,color:rand(),speed:0};
    next={x:210,y:480,color:rand(),speed:0};
    document.getElementById("popup").style.display="none";
}

// START
init();
current={x:210,y:480,color:rand(),speed:0};
next={x:210,y:480,color:rand(),speed:0};

function loop(){
    if(!gameOver) update();
    draw();
    requestAnimationFrame(loop);
}
loop();
</script>

<?php include "../inc/footer.php"; ?>
