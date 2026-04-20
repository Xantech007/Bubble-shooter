<?php
include "inc/header.php";
include "inc/navbar.php";

// fetch active games
$stmt = $conn->prepare("SELECT * FROM games WHERE status = 1 ORDER BY id DESC");
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
h1{
    margin:20px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
    padding:20px;
}

.card{
    background:#1c1c1c;
    border-radius:10px;
    overflow:hidden;
    transition:0.3s;
    box-shadow:0 0 10px rgba(0,0,0,0.5);
}

.card:hover{
    transform:scale(1.05);
}

.card img{
    width:100%;
    height:140px;
    object-fit:cover;
}

.card h3{
    margin:10px;
}

.play-btn{
    display:block;
    margin:10px;
    padding:10px;
    background:#00c3ff;
    color:#000;
    text-decoration:none;
    border-radius:5px;
    font-weight:bold;
}

.notice{
    margin:10px;
    font-size:14px;
    color:#aaa;
}
</style>

<h1>🎮 Game Center</h1>

<div class="notice">
    💰 Play games to earn <?php echo htmlspecialchars($currency); ?>. Login to claim your rewards.
</div>

<div class="grid">

<?php if(count($games) > 0): ?>
    <?php foreach($games as $game): ?>
        <div class="card">
            <img src="<?php echo htmlspecialchars($game['thumbnail']); ?>" alt="">
            <h3><?php echo htmlspecialchars($game['name']); ?></h3>

            <a class="play-btn" href="<?php echo htmlspecialchars($game['file_path']); ?>">
                ▶ Play
            </a>
        </div>
    <?php endforeach; ?>

<?php else: ?>
    <p>No games available yet.</p>
<?php endif; ?>

</div>

<?php include "inc/footer.php"; ?>
