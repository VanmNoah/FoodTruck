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
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['menu'])) {
    $_SESSION['error'] = "Geen gegevens ontvangen.";
    header('Location: dashboard.php#menu-section');
    exit;
}

$menu = $_POST['menu'];

try {
    // Start a transaction to ensure data integrity
    $pdo->beginTransaction();
    
    // Update each menu item
    foreach ($menu as $id => $item) {
        // Update the menu item
        $updateMenuStmt = $pdo->prepare("
            UPDATE menu 
            SET title = ?, description = ?, price = ? 
            WHERE id = ?
        ");
        $updateMenuStmt->execute([
            $item['title'],
            $item['description'],
            $item['price'],
            $id
        ]);
        
        // Delete existing category relationships
        $deleteCategoriesStmt = $pdo->prepare("DELETE FROM menu_categories WHERE menu_id = ?");
        $deleteCategoriesStmt->execute([$id]);
        
        // Insert new category relationships
        if (isset($item['categories']) && is_array($item['categories'])) {
            $insertCategoryStmt = $pdo->prepare("
                INSERT INTO menu_categories (menu_id, category_id) 
                VALUES (?, ?)
            ");
            
            foreach ($item['categories'] as $categoryId) {
                $insertCategoryStmt->execute([$id, $categoryId]);
            }
        }
    }
    
    // Commit the transaction
    $pdo->commit();
    
    $_SESSION['success'] = "Menu-items succesvol opgeslagen.";
    
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    $_SESSION['error'] = "Fout bij opslaan van menu-items: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: ../../dashboard.php#menu-section');
exit;
?>