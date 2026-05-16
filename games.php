<?php

include "inc/header.php";
include "inc/navbar.php";

/* CONNECT DB */
if (!isset($conn)) {
    require_once "config/database.php";
    $db = new Database();
    $conn = $db->connect();
}

/* REQUIRE LOGIN */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* AJAX EARNING SYSTEM */
if (isset($_POST['action']) && $_POST['action'] === 'earn') {

    $game_id = intval($_POST['game_id']);

    // Fetch RPM
    $stmt = $conn->prepare("SELECT reward_per_minute FROM games WHERE id=? AND status=1 LIMIT 1");
    $stmt->execute([$game_id]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Game not found'
        ]);
        exit;
    }

    $rpm = floatval($game['reward_per_minute']);

    // Every 5 seconds = rpm / 12
    $earn = $rpm / 12;

    // Update user balance
    $stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id=?");
    $stmt->execute([$earn, $user_id]);

    // Fetch updated balance
    $stmt = $conn->prepare("SELECT balance FROM users WHERE id=? LIMIT 1");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'earned' => number_format($earn, 6, '.', ''),
        'balance' => number_format($user['balance'], 6, '.', '')
    ]);

    exit;
}

/* FETCH ACTIVE GAMES */
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

    <link rel="stylesheet" href="style.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        *{
            box-sizing:border-box;
        }

        body{
            margin:0;
            background:#0f172a;
            color:#fff;
            font-family:Arial, sans-serif;
        }

        .container{
            max-width:1200px;
            margin:auto;
            padding:20px;
        }

        .page-header{
            text-align:center;
            margin:30px 0;
        }

        .page-header h1{
            font-size:34px;
            margin-bottom:10px;
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
            gap:22px;
        }

        .card{
            background:#1e293b;
            border-radius:16px;
            overflow:hidden;
            box-shadow:0 10px 25px rgba(0,0,0,0.25);
            transition:0.3s;
        }

        .card:hover{
            transform:translateY(-5px);
        }

        .card img{
            width:100%;
            height:180px;
            object-fit:cover;
        }

        .card-body{
            padding:18px;
        }

        .card-body h3{
            margin-top:0;
            margin-bottom:10px;
        }

        .rpm{
            color:#00ff88;
            font-weight:bold;
            margin-top:10px;
            display:block;
        }

        .play-btn{
            display:block;
            width:100%;
            padding:14px;
            border:none;
            border-radius:10px;
            background:#00aaff;
            color:#fff;
            font-weight:bold;
            cursor:pointer;
            margin-top:15px;
            font-size:15px;
        }

        .play-btn:hover{
            background:#0088cc;
        }

        #gameArea{
            display:none;
            width:100%;
            height:100vh;
            background:#000;
            position:fixed;
            top:0;
            left:0;
            z-index:9999;
        }

        #gameFrame{
            width:100%;
            height:100%;
            border:none;
        }

        #topBar{
            position:absolute;
            top:10px;
            left:10px;
            right:10px;
            z-index:99999;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .earn-box{
            background:rgba(0,0,0,0.7);
            padding:10px 15px;
            border-radius:10px;
            font-weight:bold;
        }

        .close-btn{
            background:red;
            color:#fff;
            border:none;
            padding:10px 16px;
            border-radius:10px;
            cursor:pointer;
            font-weight:bold;
        }

        .status{
            color:#00ff88;
            margin-top:8px;
            font-size:14px;
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

        <p>Play games and earn automatically every 5 seconds</p>
    </div>

    <div class="grid">

        <?php foreach($games as $game): ?>

            <div class="card">

                <?php if(!empty($game['thumbnail'])): ?>
                    <img src="<?= htmlspecialchars($game['thumbnail']) ?>">
                <?php endif; ?>

                <div class="card-body">

                    <h3><?= htmlspecialchars($game['name']) ?></h3>

                    <p>Play and earn rewards continuously.</p>

                    <span class="rpm">
                        $<?= number_format($game['reward_per_minute'], 4) ?>/minute
                    </span>

                    <button
                        class="play-btn"
                        onclick="startGame(
                            '<?= htmlspecialchars($game['crazygames_slug']) ?>',
                            <?= $game['id'] ?>,
                            <?= floatval($game['reward_per_minute']) ?>
                        )"
                    >
                        <i class="fa-solid fa-play"></i>
                        Play Now
                    </button>

                    <div class="status" id="status<?= $game['id'] ?>"></div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>

<!-- GAME PLAYER -->
<div id="gameArea">

    <div id="topBar">

        <div class="earn-box">
            Earned:
            $<span id="earnedAmount">0.000000</span>

            <br>

            Balance:
            $<span id="balanceAmount">0.000000</span>
        </div>

        <button class="close-btn" onclick="closeGame()">
            Exit Game
        </button>

    </div>

    <iframe id="gameFrame"></iframe>

</div>

<script>

let earnInterval = null;
let totalEarned = 0;
let currentGameId = 0;

function startGame(slug, gameId, rpm)
{
    currentGameId = gameId;

    totalEarned = 0;

    document.getElementById("earnedAmount").innerText = "0.000000";

    // Open game
    document.getElementById("gameArea").style.display = "block";

    // Load game
    document.getElementById("gameFrame").src =
        "https://www.crazygames.com/embed/" + slug;

    // Start earning every 5 seconds
    earnInterval = setInterval(function(){

        fetch(window.location.href, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body:
                "action=earn&game_id=" + gameId
        })

        .then(response => response.json())

        .then(data => {

            if(data.status === "success"){

                totalEarned += parseFloat(data.earned);

                document.getElementById("earnedAmount").innerText =
                    totalEarned.toFixed(6);

                document.getElementById("balanceAmount").innerText =
                    data.balance;

                document.getElementById("status" + gameId).innerHTML =
                    "Earned $" + data.earned;

            }

        });

    }, 5000);
}

function closeGame()
{
    clearInterval(earnInterval);

    document.getElementById("gameArea").style.display = "none";

    document.getElementById("gameFrame").src = "";
}

window.addEventListener("beforeunload", function(){
    clearInterval(earnInterval);
});

</script>

<?php include "inc/footer.php"; ?>
