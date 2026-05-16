<?php

include "inc/header.php";
include "inc/navbar.php";

/* CONNECT DB */
if (!isset($conn)) {
    require_once "config/database.php";
    $db = new Database();
    $conn = $db->connect();
}

/* LIVE EARNING AJAX */
if (isset($_POST['earn_reward'])) {

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    header('Content-Type: application/json');

    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Not logged in"
        ]);
        exit;
    }

    $user_id = intval($_SESSION['user_id']);
    $game_id = intval($_POST['game_id']);
    $amount = floatval($_POST['amount']);

    // SECURITY CHECK
    if ($amount <= 0 || $amount > 100) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid amount"
        ]);
        exit;
    }

    // VERIFY GAME EXISTS
    $stmt = $conn->prepare("
        SELECT reward_per_minute
        FROM games
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$game_id]);

    $game = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$game) {
        echo json_encode([
            "status" => "error",
            "message" => "Game not found"
        ]);
        exit;
    }

    // UPDATE BALANCE
    $stmt = $conn->prepare("
        UPDATE users
        SET balance = balance + ?
        WHERE id = ?
    ");

    $stmt->execute([$amount, $user_id]);

    echo json_encode([
        "status" => "success"
    ]);

    exit;
}

// Fetch active games
$stmt = $conn->prepare("
    SELECT *
    FROM games
    WHERE status = 1
    ORDER BY id DESC
");

$stmt->execute();

$games = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>GameWARE - Play & Earn</title>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="icon" type="image/png" href="assets/favicon.png" />
    <link rel="stylesheet" href="style.css" />

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>

        *{
            box-sizing:border-box;
        }

        body{
            background:#f4f7fb;
            margin:0;
            font-family:Arial,sans-serif;
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
            margin:0;
            font-size:32px;
        }

        .page-header p{
            color:#777;
        }

        .grid{
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
            gap:22px;
        }

        .card{
            background:#fff;
            border-radius:16px;
            overflow:hidden;
            box-shadow:0 8px 25px rgba(0,0,0,0.07);
            transition:0.2s;
        }

        .card:hover{
            transform:translateY(-4px);
        }

        .card img{
            width:100%;
            height:170px;
            object-fit:cover;
        }

        .card-body{
            padding:18px;
        }

        .card-body h3{
            margin-top:0;
            margin-bottom:10px;
        }

        .earn-rate{
            color:#00aaff;
            font-weight:bold;
            margin-bottom:12px;
        }

        .play-btn{
            display:block;
            padding:12px;
            text-align:center;
            background:#00aaff;
            color:#fff;
            border-radius:10px;
            text-decoration:none;
            margin-top:10px;
            font-weight:bold;
        }

        .play-btn:hover{
            opacity:0.9;
        }

    </style>

</head>

<body>

<main>

    <div class="container">

        <div class="page-header">
            <h1>
                <i class="fa-solid fa-gamepad"></i>
                GameWARE
            </h1>

            <p>Play games and earn money live</p>
        </div>

        <div class="grid">

            <?php foreach ($games as $game): ?>

                <div class="card">

                    <img src="<?= htmlspecialchars($game['thumbnail']) ?>">

                    <div class="card-body">

                        <h3>
                            <?= htmlspecialchars($game['name']) ?>
                        </h3>

                        <div class="earn-rate">
                            $<?= number_format($game['reward_per_minute'], 4) ?>/min
                        </div>

                        <a href="#"
                           class="play-btn"
                           onclick="loadGameWARE(
                               '<?= htmlspecialchars($game['crazygames_slug']) ?>',
                               <?= intval($game['id']) ?>,
                               <?= floatval($game['reward_per_minute']) ?>
                           ); return false;">

                            Play Now

                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</main>

<script>

let earningInterval = null;

let currentGameId = null;
let currentRPM = 0;

// START GAME
function loadGameWARE(slug, gameId, rpm = 0) {

    currentGameId = gameId;
    currentRPM = parseFloat(rpm);

    // SAVE STATE
    localStorage.setItem('playingGameId', gameId);
    localStorage.setItem('playingRPM', rpm);

    // START EARNING
    startEarningSystem();

    // OPEN GAME
    window.location.href = "?game=" + encodeURIComponent(slug);
}

// START LIVE EARNING LOOP
function startEarningSystem() {

    // PREVENT MULTIPLE LOOPS
    if (earningInterval) {
        clearInterval(earningInterval);
    }

    earningInterval = setInterval(() => {

        if (!currentGameId || currentRPM <= 0) {
            return;
        }

        // RPM / 12 EVERY 5 SECONDS
        let earned = currentRPM / 12;

        // UPDATE BALANCE LIVE
        updateBalanceUI(earned);

        // SAVE TO DATABASE
        fetch(window.location.href, {

            method: "POST",

            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },

            body:
                "earn_reward=1" +
                "&game_id=" + currentGameId +
                "&amount=" + earned

        });

    }, 5000);
}

// UPDATE NAV BALANCE
function updateBalanceUI(amount) {

    const balanceEl = document.getElementById("navBalance");

    if (!balanceEl) return;

    let currentBalance = parseFloat(
        balanceEl.innerText.replace(/[^0-9.]/g, '')
    ) || 0;

    let updatedBalance = currentBalance + parseFloat(amount);

    balanceEl.innerText =
        "$" + updatedBalance.toFixed(4);
}

// RESTORE EARNING AFTER RELOAD
document.addEventListener("DOMContentLoaded", () => {

    const params =
        new URLSearchParams(window.location.search);

    // USER INSIDE GAME
    if (params.get('game')) {

        currentGameId =
            localStorage.getItem('playingGameId');

        currentRPM =
            parseFloat(localStorage.getItem('playingRPM')) || 0;

        if (currentGameId && currentRPM > 0) {

            startEarningSystem();

            if (typeof setPlayingState === "function") {
                setPlayingState(true);
            }
        }

    } else {

        // STOP EARNING IF USER LEFT GAME
        clearInterval(earningInterval);

        localStorage.removeItem('playingGameId');
        localStorage.removeItem('playingRPM');

        if (typeof setPlayingState === "function") {
            setPlayingState(false);
        }
    }
});

// STOP LOOP ON TAB CLOSE
window.addEventListener("beforeunload", () => {

    clearInterval(earningInterval);

});

</script>

<?php include "inc/footer.php"; ?>
