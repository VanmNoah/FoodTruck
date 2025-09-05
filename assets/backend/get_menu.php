<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$offset = ($page - 1) * $limit;

try {
    // Get total count
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM menu");
    $total = $totalStmt->fetchColumn();

    // Get items for current page
    $stmt = $pdo->prepare("SELECT * FROM menu ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $items = $stmt->fetchAll();

    echo json_encode([
        'items' => $items,
        'hasMore' => ($offset + $limit) < $total
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}