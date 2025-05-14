<?php
require_once 'config.php';

function getProducts($category = null) {
    global $pdo;
    $query = "SELECT * FROM products";

    // If a category is specified, add a WHERE clause to filter products by category
    if ($category) {
        $query .= " WHERE category = :category";
    }

    $stmt = $pdo->prepare($query);
    
    // Bind the category parameter if it's specified
    if ($category) {
        $stmt->bindParam(':category', $category);
    }

    $stmt->execute();

    // Fetch all products
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging: check if products are returned
    if (empty($products)) {
        echo "No products found in the database."; // Debugging message
    }

    return $products;
}
?>
