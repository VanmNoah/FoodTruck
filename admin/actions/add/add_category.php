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
    header('Location: dashboard.php#categories-section');
    exit;
}

// Validate required fields
if (empty($_POST['name'])) {
    $_SESSION['error'] = "Categorienaam is verplicht.";
    header('Location: dashboard.php#categories-section');
    exit;
}

$name = trim($_POST['name']);

try {
    // Check if category with this name already exists
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM categories WHERE name = ?");
    $checkStmt->execute([$name]);
    $count = $checkStmt->fetchColumn();
    
    if ($count > 0) {
        $_SESSION['error'] = "Een categorie met deze naam bestaat al.";
        header('Location: dashboard.php#categories-section');
        exit;
    }
    
    // Insert the new category
    $insertCategoryStmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $insertCategoryStmt->execute([$name]);
    
    $_SESSION['success'] = "Categorie succesvol toegevoegd.";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Fout bij toevoegen van categorie: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: ../../dashboard.php#categories-section');
exit;
?>