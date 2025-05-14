<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input data
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $payment_method = filter_input(INPUT_POST, 'payment', FILTER_SANITIZE_STRING);
    $total = isset($_SESSION['cart_total']) ? $_SESSION['cart_total'] : 0;

    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Invalid email format']);
        exit;
    }

    if (!preg_match('/^\d{10}$/', $phone)) {
        echo json_encode(['success' => false, 'error' => 'Invalid phone number']);
        exit;
    }

    // Proceed if inputs are valid
    if ($full_name && $email && $phone && $address && $payment_method && $total > 0) {
        try {
            // Insert the order into the orders table
            $stmt = $pdo->prepare("
                INSERT INTO orders (full_name, email, phone, address, payment_method, total)
                VALUES (:full_name, :email, :phone, :address, :payment_method, :total)
            ");
            $stmt->execute([
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'payment_method' => $payment_method,
                'total' => $total
            ]);
            $order_id = $pdo->lastInsertId();

            // Insert the ordered products into the order_items table
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $stmt = $pdo->prepare("
                        INSERT INTO order_items (order_id, product_id, quantity, price)
                        VALUES (:order_id, :product_id, :quantity, :price)
                    ");
                    $stmt->execute([
                        'order_id' => $order_id,
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }
            }

            // Clear the cart after the order is processed
            unset($_SESSION['cart']);
            unset($_SESSION['cart_total']);

            // Return success with the order ID
            echo json_encode(['success' => true, 'order_id' => $order_id]);

        } catch (PDOException $e) {
            // Handle database errors
            echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Return error if any required input is missing
        echo json_encode(['success' => false, 'error' => 'Invalid input or empty cart']);
    }
}
?>
