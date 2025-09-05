<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../../includes/db.php';

// Check if ID parameter exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Geen event ID opgegeven.";
    header('Location: dashboard.php#events-section');
    exit;
}

$eventId = (int)$_GET['id'];

try {
    // Delete the event
    $deleteEventStmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $result = $deleteEventStmt->execute([$eventId]);
    
    if ($result) {
        $_SESSION['success'] = "Event succesvol verwijderd.";
    } else {
        $_SESSION['error'] = "Fout bij verwijderen van event.";
    }
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Fout bij verwijderen van event: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: ../../dashboard.php#events-section');
exit;
?>