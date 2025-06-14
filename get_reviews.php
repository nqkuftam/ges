<?php
require_once 'db_config.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT * FROM reviews ORDER BY date DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($reviews);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Грешка при извличане на отзивите']);
}
?> 