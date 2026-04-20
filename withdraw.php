<?php
session_start();
require_once "config/database.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = $_POST['amount'];

$db = new Database();
$conn = $db->connect();

// check balance
$stmt = $conn->prepare("SELECT balance FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if($amount <= 0 || $amount > $user['balance']){
    die("Invalid amount");
}

// deduct balance
$conn->prepare("UPDATE users SET balance = balance - ? WHERE id=?")
     ->execute([$amount,$user_id]);

// insert withdrawal
$conn->prepare("INSERT INTO withdrawals(user_id,amount) VALUES(?,?)")
     ->execute([$user_id,$amount]);

header("Location: dashboard.php");
