<?php
session_start();
require_once "config/database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['amount'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$amount = (float) $_POST['amount'];

// SECURITY LIMIT
if ($amount <= 0 || $amount > 5) {
    echo json_encode(["status" => "error", "message" => "Invalid reward amount"]);
    exit;
}

$db = new Database();
$conn = $db->connect();

// UPDATE BALANCE
$stmt = $conn->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
$stmt->execute([$amount, $user_id]);

// UPDATE SESSION
$_SESSION['balance'] += $amount;

echo json_encode([
    "status" => "success",
    "message" => "Reward claimed successfully!",
    "new_balance" => number_format($_SESSION['balance'], 4)
]);
