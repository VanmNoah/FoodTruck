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
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['categories'])) {
    $_SESSION['error'] = "Geen gegevens ontvangen.";
    header('Location: dashboard.php#categories-section');
    exit;
}

$categories = $_POST['categories'];

try {
    // Prepare the update statement outside the loop
    $updateCategoryStmt = $pdo->prepare("
        UPDATE categories 
        SET name = ? 
        WHERE id = ?
    ");
    
    // Update each category
    foreach ($categories as $id => $category) {
        $updateCategoryStmt->execute([
            $category['name'],
            $id
        ]);
    }
    
    $_SESSION['success'] = "Categorieën succesvol opgeslagen.";
    
} catch (PDOException $e) {
    $_SESSION['error'] = "Fout bij opslaan van categorieën: " . $e->getMessage();
}

// Redirect back to the dashboard
header('Location: ../../dashboard.php#categories-section');
exit;
?>