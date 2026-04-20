<?php
include "inc/header.php";
include "inc/navbar.php";

// fetch active games
$stmt = $conn->prepare("SELECT * FROM games WHERE status = 1 ORDER BY id DESC");
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.container{
    max-width:1150px;
    margin:auto;
    padding:20px;
}

/* HEADER */
.page-header{
    text-align:center;
    margin-top:30px;
}

.page-header h1{
    font-size:32px;
}

.page-header p{
    color:#666;
}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
    margin-top:30px;
}

/* CARD */
.card{
    background:#fff;
    border-radius:12px;
    overflow:hidden;
    transition:0.25s;
    box-shadow:0 8px 25px rgba(0,0,0,0.06);
    display:flex;
    flex-direction:column;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 12px 30px rgba(0,0,0,0.08);
}

.card img{
    width:100%;
    height:150px;
    object-fit:cover;
}

/* CONTENT */
.card-body{
    padding:15px;
    flex:1;
}

.card h3{
    margin:5px 0;
}

.card p{
    color:#666;
    font-size:14px;
}

/* FOOTER */
.card-footer{
    padding:15px;
}

/* BUTTON */
.play-btn{
    display:block;
    width:100%;
    padding:10px;
    text-align:center;
    background:#00aaff;
    color:#fff;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
}

.play-btn:hover{
    background:#008ecc;
}

/* NOTICE */
.notice{
    margin-top:10px;
    color:#777;
    font-size:14px;
    text-align:center;
}

/* BADGE */
.badge{
    display:inline-block;
    padding:4px 8px;
    background:#eaf6ff;
    color:#00aaff;
    border-radius:5px;
    font-size:12px;
    margin-top:5px;
}
</style>

<div class="container">

<!-- HEADER -->
<div class="page-header">
    <h1><i class="fa-solid fa-gamepad"></i> Game Center</h1>
    <p>Play games, have fun, and earn real <?php echo htmlspecialchars($currency); ?></p>
</div>

<div class="notice">
    <i class="fa-solid fa-circle-info"></i>
    Login is required to claim and withdraw your earnings.
</div>

<!-- GRID -->
<div class="grid">

<?php if(count($games) > 0): ?>
    <?php foreach($games as $game): ?>
        <div class="card">

            <img src="<?php echo htmlspecialchars($game['thumbnail']); ?>" alt="">

            <div class="card-body">
                <h3><?php echo htmlspecialchars($game['name']); ?></h3>
                <p>Play and earn rewards instantly.</p>

                <span class="badge">
                    <i class="fa-solid fa-coins"></i> Earn <?php echo $currency; ?>
                </span>
            </div>

            <div class="card-footer">
                <a class="play-btn" href="<?php echo htmlspecialchars($game['file_path']); ?>">
                    <i class="fa-solid fa-play"></i> Play Now
                </a>
            </div>

        </div>
    <?php endforeach; ?>

<?php else: ?>
    <p style="text-align:center;">No games available yet.</p>
<?php endif; ?>

</div>

</div>

<?php include "inc/footer.php"; ?>
