<?php
session_start();
require_once "config/database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$game_id = isset($_POST['game_id']) ? (int)$_POST['game_id'] : 0;

if ($game_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid game']);
    exit;
}

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user_id'];
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// Check if game exists
$stmt = $conn->prepare("SELECT id FROM games WHERE id = ? AND status = 1");
$stmt->execute([$game_id]);
if ($stmt->rowCount() === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Game not found']);
    exit;
}

// Log play session
$stmt = $conn->prepare("INSERT INTO game_sessions 
    (user_id, game_id, start_time, ip_address) 
    VALUES (?, ?, NOW(), ?)");

$stmt->execute([$user_id, $game_id, $ip]);

$session_id = $conn->lastInsertId();

echo json_encode([
    'status' => 'success',
    'session_id' => $session_id,
    'message' => 'Play session started'
]);
