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

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['events'])) {
    $_SESSION['error'] = "Geen gegevens ontvangen.";
    header('Location: dashboard.php#events-section');
    exit;
}

$events = $_POST['events'];

try {
    // Prepare the update statement outside the loop
    $updateEventStmt = $pdo->prepare("
        UPDATE events 
        SET name = ?, location = ?, date = ?, time = ? 
        WHERE id = ?
    ");
    
    // Update each event
    foreach ($events as $id => $event) {
        $updateEventStmt->execute([
            $event['name'],
            $event['location'],
            $event['date'],
            $event['time'],
            $id
        ]);
    }
    
    $_SESSION['success'] = "Events succesvol opgeslagen.";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Fout bij opslaan van events: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: ../../dashboard.php#events-section');
exit;
?>