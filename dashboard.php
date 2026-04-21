<?php
include "inc/header.php";
include "inc/navbar.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* CONNECT DB */
if (!isset($conn)) {
    require_once "config/database.php";
    $db = new Database();
    $conn = $db->connect();
}

// FETCH USER
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// TOTAL EARNINGS
$earn = $conn->prepare("SELECT SUM(amount) as total FROM earnings WHERE user_id=?");
$earn->execute([$user_id]);
$totalEarned = $earn->fetch()['total'] ?? 0;

// TOTAL WITHDRAWN (approved only)
$with = $conn->prepare("SELECT SUM(amount) as total FROM withdrawals WHERE user_id=? AND status=1");
$with->execute([$user_id]);
$totalWithdrawn = $with->fetch()['total'] ?? 0;

$balance = $user['balance'] ?? 0;

// Fetch minimum withdrawal from region_settings
$minStmt = $conn->prepare("SELECT min_wdr FROM region_settings LIMIT 1");
$minStmt->execute();
$minWithdrawal = $minStmt->fetchColumn() ?? 10.00;   // default fallback

// EARNINGS HISTORY
$historyStmt = $conn->prepare("SELECT * FROM earnings WHERE user_id=? ORDER BY id DESC LIMIT 10");
$historyStmt->execute([$user_id]);
$earningsHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

// WITHDRAWALS HISTORY
$wdStmt = $conn->prepare("SELECT * FROM withdrawals WHERE user_id=? ORDER BY id DESC LIMIT 10");
$wdStmt->execute([$user_id]);
$withdrawals = $wdStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.container { max-width: 1200px; margin: auto; padding: 20px; }
h1 { font-size: 36px; margin-bottom: 30px; }

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}
.card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    text-align: center;
}
.card h3 { color: #666; font-size: 16px; margin-bottom: 8px; }
.card p { font-size: 28px; font-weight: bold; margin: 0; color: #1e40af; }

/* WITHDRAWAL BOX */
.withdrawal-box {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    margin-bottom: 40px;
}

.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
}
.form-group input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 16px;
}

.btn {
    background: #00aaff;
    color: #fff;
    padding: 14px 28px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 17px;
    font-weight: 600;
}
.btn:hover { background: #0088cc; }

/* TABLES */
table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    margin-bottom: 40px;
}
table th, table td {
    padding: 14px 16px;
    text-align: left;
    border-bottom: 1px solid #eee;
}
table th { background: #f8fafc; font-weight: 600; }

/* STATUS BADGES */
.status {
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 600;
    display: inline-block;
}
.status-0 { background: #fef3c7; color: #d97706; }   /* Yellow - Pending */
.status-1 { background: #d1fae5; color: #10b981; }   /* Green  - Approved */
.status-2 { background: #fee2e2; color: #ef4444; }   /* Red    - Rejected */

.icon-blue { color: #00aaff; }
.icon-green { color: #22c55e; }
</style>

<div class="container">

    <h1><i class="fa-solid fa-chart-line icon-blue"></i> My Dashboard</h1>

    <!-- SUMMARY CARDS -->
    <div class="cards">
        <div class="card">
            <h3>Available Balance</h3>
            <p>$<?php echo number_format($balance, 2); ?></p>
        </div>
        <div class="card">
            <h3>Total Earned</h3>
            <p>$<?php echo number_format($totalEarned, 2); ?></p>
        </div>
        <div class="card">
            <h3>Total Withdrawn</h3>
            <p>$<?php echo number_format($totalWithdrawn, 2); ?></p>
        </div>
    </div>

    <!-- WITHDRAWAL SECTION -->
    <div class="withdrawal-box">
        <h2><i class="fa-solid fa-wallet icon-green"></i> Request Withdrawal</h2>
        <p style="color:#555; margin-bottom:20px;">
            Minimum withdrawal: <strong>$<?php echo number_format($minWithdrawal, 2); ?></strong> • 
            Processed within <strong>15 minutes</strong>
        </p>

        <form method="POST" action="withdrawals.php">
            <div class="form-group">
                <label for="amount">Withdrawal Amount (USD)</label>
                <input type="number" id="amount" name="amount" 
                       step="0.01" 
                       min="<?php echo $minWithdrawal; ?>" 
                       placeholder="Enter amount (min $<?php echo number_format($minWithdrawal, 2); ?>)" 
                       required>
            </div>

            <button type="submit" class="btn">
                <i class="fa-solid fa-arrow-up-from-bracket"></i> Request Withdrawal
            </button>
        </form>
    </div>

    <!-- EARNINGS HISTORY -->
    <h2><i class="fa-solid fa-clock icon-blue"></i> Recent Earnings</h2>
    <table>
        <tr><th>Game</th><th>Amount</th><th>Date</th></tr>
        <?php if (count($earningsHistory) > 0): ?>
            <?php foreach ($earningsHistory as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['game'] ?? 'Game Play'); ?></td>
                <td>$<?php echo number_format($row['amount'], 2); ?></td>
                <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center;padding:30px;">No earnings yet. Start playing!</td></tr>
        <?php endif; ?>
    </table>

    <!-- WITHDRAWALS HISTORY -->
    <h2><i class="fa-solid fa-money-bill-transfer icon-blue"></i> Withdrawal History</h2>
    <table>
        <tr><th>Amount</th><th>Status</th><th>Date</th></tr>
        <?php if (count($withdrawals) > 0): ?>
            <?php foreach ($withdrawals as $w): 
                $statusClass = 'status-' . $w['status'];
                $statusText = match((int)$w['status']) {
                    0 => 'Pending',
                    1 => 'Approved',
                    2 => 'Rejected',
                    default => 'Unknown'
                };
            ?>
            <tr>
                <td>$<?php echo number_format($w['amount'], 2); ?></td>
                <td><span class="status <?php echo $statusClass; ?>"><?php echo $statusText; ?></span></td>
                <td><?php echo date('M d, Y H:i', strtotime($w['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align:center;padding:30px;">No withdrawals yet.</td></tr>
        <?php endif; ?>
    </table>

</div>

<?php include "inc/footer.php"; ?>
