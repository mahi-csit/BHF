<?php
session_start();
// Add authentication here (e.g., check if user is logged in as admin)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'config.php';

    // Sanitize input data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $image = $_FILES['image']['name'];

    if ($name && $description && $price && $category && $image) {
        // Upload the image
        $targetDir = "images/";
        $targetFile = $targetDir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);

        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, image) VALUES (:name, :description, :price, :category, :image)");
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'category' => $category,
                'image' => $image
            ]);

            echo "<p>Product added successfully!</p>";
        } catch (PDOException $e) {
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p>All fields are required!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-size: 14px;
        }
        input, select, textarea {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Add Product</h1>
    <form action="admin.php" method="post" enctype="multipart/form-data">
        <label for="name">Product Name</label>
        <input type="text" id="name" name="name" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" required></textarea>

        <label for="price">Price</label>
        <input type="number" id="price" name="price" step="0.01" required>

        <label for="category">Category</label>
        <select id="category" name="category" required>
            <option value="Sweets">Sweets</option>
            <option value="Pickles">Pickles</option>
            <option value="Hots">Hots</option>
        </select>

        <label for="image">Image</label>
        <input type="file" id="image" name="image" accept="image/*" required>

        <button type="submit">Add Product</button>
    </form>
</div>

</body>
</html>
