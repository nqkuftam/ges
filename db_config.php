<?php
$host = '23.95.246.156';
$dbname = 'geshome_db';
$username = 'geshomereviews';
$password = 'E$L2NEN9uk';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Грешка при връзка с базата данни: " . $e->getMessage();
    die();
}
?> 