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
    $_SESSION['error'] = "Geen categorie ID opgegeven.";
    header('Location: dashboard.php#categories-section');
    exit;
}

$categoryId = (int)$_GET['id'];

try {
    // Start a transaction to ensure data integrity
    $pdo->beginTransaction();
    
    // First check if the category is associated with any menu items
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM menu_categories WHERE category_id = ?");
    $checkStmt->execute([$categoryId]);
    $count = $checkStmt->fetchColumn();
    
    if ($count > 0) {
        // Category is in use, rollback and show error
        $pdo->rollBack();
        $_SESSION['error'] = "Deze categorie kan niet worden verwijderd omdat deze nog in gebruik is bij " . $count . " menu-items.";
        header('Location: dashboard.php#categories-section');
        exit;
    }
    
    // If not in use, delete the category
    $deleteCategoryStmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $deleteCategoryStmt->execute([$categoryId]);
    
    // Commit the transaction
    $pdo->commit();
    
    $_SESSION['success'] = "Categorie succesvol verwijderd.";
    
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    $_SESSION['error'] = "Fout bij verwijderen van categorie: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: ../../dashboard.php#categories-section');
exit;
?>