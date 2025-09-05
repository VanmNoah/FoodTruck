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
    header('Location: dashboard.php#menu-section');
    exit;
}

// Validate required fields
if (empty($_POST['title']) || empty($_POST['description']) || !isset($_POST['price'])) {
    $_SESSION['error'] = "Alle velden zijn verplicht.";
    header('Location: dashboard.php#menu-section');
    exit;
}

$title = trim($_POST['title']);
$description = trim($_POST['description']);
$price = (float)$_POST['price'];
$categories = isset($_POST['categories']) ? $_POST['categories'] : [];

try {
    // Start a transaction to ensure data integrity
    $pdo->beginTransaction();
    
    // Insert the new menu item
    $insertMenuStmt = $pdo->prepare("
        INSERT INTO menu (title, description, price) 
        VALUES (?, ?, ?)
    ");
    $insertMenuStmt->execute([$title, $description, $price]);
    
    // Get the ID of the new menu item
    $menuId = $pdo->lastInsertId();
    
    // Insert category relationships
    if (!empty($categories)) {
        $insertCategoryStmt = $pdo->prepare("
            INSERT INTO menu_categories (menu_id, category_id) 
            VALUES (?, ?)
        ");
        
        foreach ($categories as $categoryId) {
            $insertCategoryStmt->execute([$menuId, $categoryId]);
        }
    }
    
    // Commit the transaction
    $pdo->commit();
    
    $_SESSION['success'] = "Menu-item succesvol toegevoegd.";
    
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    $_SESSION['error'] = "Fout bij toevoegen van menu-item: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: ../../dashboard.php#menu-section');
exit;
?>