<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['id'];
    $product_name = $data['name'];
    $price = $data['price'];

    if ($product_id && $product_name && $price) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] === $product_id) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product_id,
                'name' => $product_name,
                'price' => $price,
                'quantity' => 1
            ];
        }

        $subtotal = 0;
        foreach ($_SESSION['cart'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        $_SESSION['cart_total'] = $subtotal + 2.99 + ($subtotal * 0.1);

        echo json_encode([
            'success' => true,
            'cart_count' => count($_SESSION['cart'])
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
    }
}
?>