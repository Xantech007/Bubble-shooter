<?php

include "inc/header.php";
include "inc/navbar.php";

/* CONNECT DB */
if (!isset($conn)) {
    require_once "config/database.php";
    $db = new Database();
    $conn = $db->connect();
}

/* =========================
   AJAX EARNING SYSTEM
========================= */

if (isset($_POST['earn_game_id'])) {

    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Login required'
        ]);
        exit;
    }

    $userId = intval($_SESSION['user_id']);
    $gameId = intval($_POST['earn_game_id']);

    // Fetch game RPM
    $stmt = $conn->prepare("SELECT reward_per_min FROM games WHERE id = ? AND status = 1 LIMIT 1");
    $stmt->execute([$gameId]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Game not found'
        ]);
        exit;
    }

    $rpm = floatval($game['reward_per_min']);

    // Every 5 seconds:
    // reward = rpm / 12
    $reward = $rpm / 12;

    // Update user balance
    $stmt = $conn->prepare("
        UPDATE users 
        SET balance = balance + ? 
        WHERE id = ?
    ");
    $stmt->execute([$reward, $userId]);

    // Fetch updated balance
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'earned' => number_format($reward, 8, '.', ''),
        'balance' => number_format($user['balance'], 8, '.', '')
    ]);

    exit;
}

// Fetch active games
$stmt = $conn->prepare("SELECT * FROM games WHERE status = 1 ORDER BY id DESC");
$stmt->execute();
$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>GameWARE - Play & Earn</title>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="dark" />

    <link rel="icon" type="image/png" href="assets/favicon.png" />
    <link rel="stylesheet" href="style.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>

        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            font-family:Arial, sans-serif;
            background:#f5f7fb;
        }

        .container{
            max-width:1200px;
            margin:auto;
            padding:15px;
        }

        .page-header{
            text-align:center;
            margin:40px 0 25px;
        }

        .page-header h1{
            font-size:32px;
            margin-bottom:8px;
        }

        .notice{
            background:#fff3cd;
            color:#856404;
            padding:14px 20px;
            border-radius:10px;
            margin-bottom:30px;
            text-align:center;
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(280px, 1fr));
            gap:22px;
            margin-top:20px;
        }

        .card{
            background:#fff;
            border-radius:16px;
            overflow:hidden;
            box-shadow:0 8px 25px rgba(0,0,0,0.07);
            transition:0.3s;
        }

        .card:hover{
            transform:translateY(-6px);
            box-shadow:0 15px 35px rgba(0,0,0,0.12);
        }

        .card img{
            width:100%;
            height:170px;
            object-fit:cover;
        }

        .card-body{
            padding:18px 20px;
        }

        .play-btn{
            display:block;
            width:100%;
            padding:14px;
            text-align:center;
            background:#00aaff;
            color:#fff;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
            margin-top:15px;
            border:none;
            cursor:pointer;
        }

        .play-btn:hover{
            background:#0088cc;
        }

        .game-frame-wrap{
            width:100%;
            margin-top:30px;
            display:none;
        }

        .game-frame{
            width:100%;
            height:80vh;
            border:none;
            border-radius:15px;
            background:#000;
        }

        .earning-box{
            margin-top:20px;
            background:#fff;
            padding:18px;
            border-radius:12px;
            box-shadow:0 4px 10px rgba(0,0,0,0.05);
            display:none;
        }

        .earning-box h3{
            margin:0 0 10px;
        }

        .stop-btn{
            margin-top:15px;
            background:#ff3b30;
            color:#fff;
            border:none;
            padding:12px 20px;
            border-radius:10px;
            cursor:pointer;
            font-weight:600;
        }

        .stop-btn:hover{
            background:#d93025;
        }

    </style>
</head>

<body>

<div class="container">

    <div class="page-header">
        <h1>
            <i class="fa-solid fa-gamepad"></i>
            GameWARE
        </h1>

        <p>
            Play games and earn automatically every 5 seconds
        </p>
    </div>

    <?php if (!isset($_SESSION['user_id'])): ?>

        <div class="notice">
            <i class="fa-solid fa-circle-info"></i>
            <strong>Login required</strong>
            to earn while playing.
        </div>

    <?php endif; ?>

    <!-- GAME LIST -->
    <div class="grid" id="gamesGrid">

        <?php if (count($games) > 0): ?>

            <?php foreach ($games as $game): ?>

                <div class="card">

                    <?php if (!empty($game['thumbnail'])): ?>

                        <img
                            src="<?= htmlspecialchars($game['thumbnail']) ?>"
                            alt="<?= htmlspecialchars($game['name']) ?>"
                        >

                    <?php endif; ?>

                    <div class="card-body">

                        <h3>
                            <?= htmlspecialchars($game['name']) ?>
                        </h3>

                        <p>
                            Play and earn automatically.
                        </p>

                        <?php if (!empty($game['reward_per_min'])): ?>

                            <strong style="color:#00aa00;">

                                $<?= number_format($game['reward_per_min'], 4) ?>/min

                            </strong>

                        <?php endif; ?>

                        <button
                            class="play-btn"
                            onclick="startGame(
                                '<?= htmlspecialchars($game['crazygames_slug']) ?>',
                                <?= $game['id'] ?>
                            )"
                        >
                            <i class="fa-solid fa-play"></i>
                            Play Now
                        </button>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p style="text-align:center; grid-column:1/-1; padding:80px 20px;">
                No games available at the moment.
            </p>

        <?php endif; ?>

    </div>

    <!-- EARNING BOX -->
    <div class="earning-box" id="earningBox">

        <h3>
            <i class="fa-solid fa-coins"></i>
            Earnings Active
        </h3>

        <p>
            Current Status:
            <strong id="earningStatus">
                Running...
            </strong>
        </p>

        <p>
            Total Balance:
            <strong id="userBalance">
                Loading...
            </strong>
        </p>

        <button class="stop-btn" onclick="stopGame()">
            Stop Playing
        </button>

    </div>

    <!-- GAME FRAME -->
    <div class="game-frame-wrap" id="gameWrap">

        <iframe
            id="gameFrame"
            class="game-frame"
            allowfullscreen
        ></iframe>

    </div>

</div>

<script>

let earningInterval = null;
let currentGameId = null;

function startGame(slug, gameId)
{

    <?php if (!isset($_SESSION['user_id'])): ?>

        alert("Please login first.");
        return;

    <?php endif; ?>

    currentGameId = gameId;

    // Show game frame
    document.getElementById('gameWrap').style.display = 'block';

    // Show earning box
    document.getElementById('earningBox').style.display = 'block';

    // Load game
    document.getElementById('gameFrame').src =
        "https://games.crazygames.com/en_US/" + slug + "/index.html";

    // Clear previous interval
    if (earningInterval) {
        clearInterval(earningInterval);
    }

    // Start earning every 5 seconds
    earningInterval = setInterval(function(){

        fetch(window.location.href, {

            method: 'POST',

            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },

            body: 'earn_game_id=' + currentGameId

        })
        .then(response => response.json())
        .then(data => {

            if(data.status === 'success'){

                document.getElementById('earningStatus').innerHTML =
                    'Earned $' + data.earned + ' this cycle';

                document.getElementById('userBalance').innerHTML =
                    '$' + data.balance;

            }

        });

    }, 5000);

}

function stopGame()
{

    // Stop interval
    clearInterval(earningInterval);

    earningInterval = null;

    // Hide game
    document.getElementById('gameWrap').style.display = 'none';

    // Clear iframe
    document.getElementById('gameFrame').src = '';

    // Update status
    document.getElementById('earningStatus').innerHTML =
        'Stopped';

}

window.addEventListener('beforeunload', function(){

    clearInterval(earningInterval);

});

</script>

<?php include "inc/footer.php"; ?>
