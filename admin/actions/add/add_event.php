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
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Geen gegevens ontvangen.";
    header('Location: dashboard.php#events-section');
    exit;
}

// Validate required fields
if (empty($_POST['name']) || empty($_POST['location']) || empty($_POST['date']) || empty($_POST['time'])) {
    $_SESSION['error'] = "Alle velden zijn verplicht.";
    header('Location: dashboard.php#events-section');
    exit;
}

$name = trim($_POST['name']);
$location = trim($_POST['location']);
$date = $_POST['date'];
$time = $_POST['time'];

try {
    // Insert the new event
    $insertEventStmt = $pdo->prepare("
        INSERT INTO events (name, location, date, time) 
        VALUES (?, ?, ?, ?)
    ");
    $insertEventStmt->execute([$name, $location, $date, $time]);
    
    $_SESSION['success'] = "Event succesvol toegevoegd.";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Fout bij toevoegen van event: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: dashboard.php#events-section');
exit;
?>