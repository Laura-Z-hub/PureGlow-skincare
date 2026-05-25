<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/bootstrap.php';

$data = parseJsonRequest();
$name = getJsonBodyValue($data, 'name');
$phone = getJsonBodyValue($data, 'phone');
$email = getJsonBodyValue($data, 'email');
$skinType = getJsonBodyValue($data, 'skin_type');
$date = getJsonBodyValue($data, 'date');
$time = getJsonBodyValue($data, 'time');

if ($name === '' || $phone === '' || $skinType === '' || $date === '' || $time === '') {
    sendJson(['error' => 'Name, phone, skin type, date and time are required'], 400);
}

if ($email !== '' && !validateEmail($email)) {
    sendJson(['error' => 'Invalid email address'], 400);
}

$stmt = $pdo->prepare(
    'INSERT INTO bookings (name, phone, email, skin_type, appointment_date, appointment_time, created_at)
    VALUES (:name, :phone, :email, :skin_type, :appointment_date, :appointment_time, NOW())'
);
$stmt->execute([
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'skin_type' => $skinType,
    'appointment_date' => $date,
    'appointment_time' => $time,
]);

sendJson(['message' => 'Booking reserved successfully. A confirmation will be sent within 2 hours.'], 201);
