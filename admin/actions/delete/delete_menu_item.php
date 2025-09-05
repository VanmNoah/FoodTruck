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
    $_SESSION['error'] = "Geen menu-item ID opgegeven.";
    header('Location: dashboard.php#menu-section');
    exit;
}

$menuId = (int)$_GET['id'];

try {
    // Start a transaction to ensure data integrity
    $pdo->beginTransaction();
    
    // First delete from menu_categories (foreign key relationship)
    $deleteMenuCategoriesStmt = $pdo->prepare("DELETE FROM menu_categories WHERE menu_id = ?");
    $deleteMenuCategoriesStmt->execute([$menuId]);
    
    // Then delete the menu item
    $deleteMenuStmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
    $deleteMenuStmt->execute([$menuId]);
    
    // Commit the transaction
    $pdo->commit();
    
    $_SESSION['success'] = "Menu-item succesvol verwijderd.";
    
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    $_SESSION['error'] = "Fout bij verwijderen van menu-item: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: ../../dashboard.php#menu-section');
exit;
?>