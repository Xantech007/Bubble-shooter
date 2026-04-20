<?php
session_start();
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$settings = $conn->query("SELECT * FROM settings WHERE id=1")->fetch(PDO::FETCH_ASSOC);

$currency = $settings['currency'];
$site_name = $settings['site_name'];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $site_name; ?></title>
<style>
body{margin:0;background:#111;color:#fff;font-family:Arial;}
</style>
</head>
<body>
