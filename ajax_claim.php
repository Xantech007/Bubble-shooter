<?php
session_start();
require_once "config/database.php";

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["message" => "Not logged in"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['session_id'])) {
    echo json_encode(["message" => "Invalid request"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$session_id = (int)$data['session_id'];

$db = new Database();
$conn = $db->connect();

try {

    /* START TRANSACTION */
    $conn->beginTransaction();

    /* LOCK SESSION ROW */
    $stmt = $conn->prepare("
        SELECT * FROM game_sessions 
        WHERE id = ? AND user_id = ? FOR UPDATE
    ");
    $stmt->execute([$session_id, $user_id]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        throw new Exception("Session not found");
    }

    if ($session['claimed'] == 1) {
        throw new Exception("Already claimed");
    }

    if ($session['amount_earned'] <= 0) {
        throw new Exception("No earnings to claim");
    }

    /* UPDATE USER BALANCE */
    $stmt = $conn->prepare("
        UPDATE users 
        SET balance = balance + ? 
        WHERE id = ?
    ");
    $stmt->execute([$session['amount_earned'], $user_id]);

    if ($stmt->rowCount() == 0) {
        throw new Exception("Balance update failed");
    }

    /* MARK SESSION CLAIMED */
    $stmt = $conn->prepare("
        UPDATE game_sessions 
        SET claimed = 1 
        WHERE id = ?
    ");
    $stmt->execute([$session_id]);

    /* OPTIONAL: RECORD */
    $stmt = $conn->prepare("
        INSERT INTO earnings (user_id, session_id, amount)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$user_id, $session_id, $session['amount_earned']]);

    $conn->commit();

    echo json_encode([
        "message" => "Earnings claimed successfully!",
        "amount" => number_format($session['amount_earned'], 2)
    ]);

} catch (Exception $e) {

    $conn->rollBack();

    echo json_encode([
        "message" => $e->getMessage()
    ]);
}
