<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/db.php';

header('Content-Type: application/json');

try {
    // Test database connection
    $pdo->query("SELECT 1");
    
    $stmt = $pdo->query("SELECT name, location, DATE_FORMAT(date, '%d-%m-%Y') as date, 
                         TIME_FORMAT(time, '%H:%i') as time 
                         FROM events 
                         WHERE date >= CURDATE() 
                         ORDER BY date, time");
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Convert location string to coordinates
    foreach ($events as &$event) {
        $coordinates = explode(',', $event['location']);
        if (count($coordinates) === 2) {
            $event['coordinates'] = [
                floatval(trim($coordinates[0])),
                floatval(trim($coordinates[1]))
            ];
        } else {
            $event['coordinates'] = [50.6403, 4.6667]; // Center point Belgium (approximate)
        }
    }

    echo json_encode($events);
} catch (PDOException $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}