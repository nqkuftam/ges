<?php
require_once 'db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    
    if (!$name || !$email || !$message) {
        echo json_encode(['success' => false, 'message' => 'Моля, попълнете всички полета']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, date, status) VALUES (?, ?, ?, NOW(), 'new')");
        $stmt->execute([$name, $email, $message]);
        
        // Изпращане на имейл до администратора
        $to = "pavelabushev@yahoo.com";
        $subject = "Ново съобщение от сайта";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        $emailBody = "
            <h2>Ново съобщение от контактната форма</h2>
            <p><strong>Име:</strong> $name</p>
            <p><strong>Имейл:</strong> $email</p>
            <p><strong>Съобщение:</strong></p>
            <p>$message</p>
        ";
        
        mail($to, $subject, $emailBody, $headers);
        
        echo json_encode(['success' => true, 'message' => 'Съобщението е изпратено успешно']);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Грешка при запис на съобщението']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Невалиден метод на заявка']);
}
?> 