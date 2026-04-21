<?php
include "inc/header.php";
include "inc/navbar.php";

/* CONNECT DB (if not already connected in header) */
if (!isset($conn)) {
    require_once "config/database.php";
    $db = new Database();
    $conn = $db->connect();
}

// Fetch active games
$stmt = $conn->prepare("SELECT * FROM games WHERE status = 1 ORDER BY id DESC");
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- FONT AWESOME -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.container {
    max-width: 1200px;
    margin: auto;
    padding: 20px;
}

/* HEADER */
.page-header {
    text-align: center;
    margin-top: 40px;
    margin-bottom: 20px;
}
.page-header h1 {
    font-size: 36px;
    margin-bottom: 10px;
}
.page-header p {
    color: #555;
    font-size: 18px;
}

/* NOTICE */
.notice {
    background: #fff3cd;
    color: #856404;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    text-align: center;
    font-size: 15px;
    display: none; /* Hidden by default - shown only for guests */
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

/* CARD */
.card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    transition: 0.3s ease;
    box-shadow: 0 8px 25px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
}
.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}
.card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

/* CONTENT */
.card-body {
    padding: 18px;
    flex: 1;
}
.card h3 {
    margin: 8px 0 10px;
    font-size: 20px;
}
.card p {
    color: #666;
    font-size: 14.5px;
    line-height: 1.5;
}

/* BADGE */
.badge {
    display: inline-block;
    padding: 5px 10px;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 6px;
    font-size: 13px;
    margin-top: 8px;
}

/* BUTTON */
.play-btn {
    display: block;
    width: 100%;
    padding: 12px;
    text-align: center;
    background: #00aaff;
    color: #fff;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
}
.play-btn:hover {
    background: #0088cc;
}

/* ICON COLORS */
.icon-blue { color: #00aaff; }
</style>

<div class="container">

    <!-- HEADER -->
    <div class="page-header">
        <h1><i class="fa-solid fa-gamepad icon-blue"></i> Game Center</h1>
        <p>Play exciting games and earn real money in USD</p>
    </div>

    <!-- LOGIN NOTICE - Hidden when user is logged in -->
    <?php if (!isset($_SESSION['user_id'])): ?>
    <div class="notice" style="display: block;">
        <i class="fa-solid fa-circle-info"></i>
        <strong>Login is required</strong> to claim and withdraw your earnings.
    </div>
    <?php endif; ?>

    <!-- GAMES GRID -->
    <div class="grid">
        <?php if (count($games) > 0): ?>
            <?php foreach ($games as $game): ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($game['thumbnail']); ?>" alt="<?php echo htmlspecialchars($game['name']); ?>">
                    <div class="card-body">
                        <h3><?php echo htmlspecialchars($game['name']); ?></h3>
                        <p>Play instantly and earn real rewards based on your performance.</p>
                        <span class="badge">
                            <i class="fa-solid fa-coins"></i> Earn $
                        </span>
                    </div>
                    <div class="card-footer" style="padding:15px;">
                        <a class="play-btn" href="<?php echo htmlspecialchars($game['file_path']); ?>">
                            <i class="fa-solid fa-play"></i> Play Now
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center; grid-column: 1 / -1; padding: 40px;">
                No games available at the moment. Please check back later.
            </p>
        <?php endif; ?>
    </div>

</div>

<?php include "inc/footer.php"; ?>
