<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized");
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['amount'])) {
    die("Invalid request");
}

$amount = (float) $_POST['amount'];

// SECURITY LIMIT (ANTI-CHEAT)
if ($amount <= 0 || $amount > 5) {
    die("Invalid reward amount");
}

$db = new Database();
$conn = $db->connect();

// UPDATE BALANCE
$stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
$stmt->execute([$amount, $user_id]);

// UPDATE SESSION BALANCE
$_SESSION['balance'] += $amount;

echo "Reward claimed successfully! +$" . number_format($amount, 4);
