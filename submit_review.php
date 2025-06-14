<?php
require_once 'db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Проверка за спам
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $current_time = date('Y-m-d H:i:s');
    $time_limit = date('Y-m-d H:i:s', strtotime('-1 hour')); // 1 час ограничение

    try {
        // Проверка на IP адреса
        $stmt = $pdo->prepare("SELECT * FROM spam_protection WHERE ip_address = ?");
        $stmt->execute([$ip_address]);
        $spam_check = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($spam_check) {
            // Ако има запис за този IP
            if ($spam_check['last_review_time'] > $time_limit) {
                // Ако е изпратил отзив през последния час
                if ($spam_check['review_count'] >= 3) {
                    // Ако е изпратил повече от 3 отзива за последния час
                    echo json_encode(['success' => false, 'message' => 'Твърде много отзиви за кратко време. Моля, опитайте по-късно.']);
                    exit;
                }
                // Обновяване на броя отзиви
                $stmt = $pdo->prepare("UPDATE spam_protection SET review_count = review_count + 1, last_review_time = ? WHERE ip_address = ?");
                $stmt->execute([$current_time, $ip_address]);
            } else {
                // Ако последният отзив е преди повече от час, нулиране на брояча
                $stmt = $pdo->prepare("UPDATE spam_protection SET review_count = 1, last_review_time = ? WHERE ip_address = ?");
                $stmt->execute([$current_time, $ip_address]);
            }
        } else {
            // Първи отзив от този IP
            $stmt = $pdo->prepare("INSERT INTO spam_protection (ip_address, last_review_time) VALUES (?, ?)");
            $stmt->execute([$ip_address, $current_time]);
        }

        // Проверка на входните данни
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $rating = filter_input(INPUT_POST, 'rating', FILTER_VALIDATE_INT);
        $review = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_STRING);
        
        // Допълнителни проверки
        if (strlen($name) < 2 || strlen($name) > 100) {
            echo json_encode(['success' => false, 'message' => 'Името трябва да е между 2 и 100 символа']);
            exit;
        }

        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Невалидна оценка']);
            exit;
        }

        if (strlen($review) < 10 || strlen($review) > 1000) {
            echo json_encode(['success' => false, 'message' => 'Отзивът трябва да е между 10 и 1000 символа']);
            exit;
        }
        
        // Запис на отзива
        $stmt = $pdo->prepare("INSERT INTO reviews (name, rating, review, date) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$name, $rating, $review]);
        
        echo json_encode(['success' => true, 'message' => 'Отзивът е добавен успешно']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Грешка при запис на отзива']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Невалиден метод на заявка']);
}
?> 