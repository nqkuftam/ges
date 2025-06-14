<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $apartments = filter_input(INPUT_POST, 'apartments', FILTER_SANITIZE_NUMBER_INT);
    $hasElevator = filter_input(INPUT_POST, 'hasElevator', FILTER_SANITIZE_STRING);
    $totalPrice = filter_input(INPUT_POST, 'totalPrice', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    
    if (!$name || !$email || !$phone || !$address || !$apartments || !$totalPrice) {
        echo json_encode(['success' => false, 'message' => 'Моля, попълнете всички полета']);
        exit;
    }
    
    // Изпращане на имейл до администратора
    $to = "pavelabushev@yahoo.com";
    $subject = "Нова заявка за оферта";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $emailBody = "
        <h2>Нова заявка за оферта</h2>
        <p><strong>Име:</strong> $name</p>
        <p><strong>Имейл:</strong> $email</p>
        <p><strong>Телефон:</strong> $phone</p>
        <p><strong>Адрес:</strong> $address</p>
        <p><strong>Брой апартаменти:</strong> $apartments</p>
        <p><strong>Обслужване на асансьор:</strong> " . ($hasElevator === 'true' ? 'Да' : 'Не') . "</p>
        <p><strong>Обща цена:</strong> $totalPrice лв.</p>
    ";
    
    if(mail($to, $subject, $emailBody, $headers)) {
        echo json_encode(['success' => true, 'message' => 'Заявката е изпратена успешно']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Грешка при изпращане на имейла']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Невалиден метод на заявка']);
}
?> 