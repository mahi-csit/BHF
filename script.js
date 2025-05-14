function openModal(modalId) {
    document.getElementById(modalId).style.display = 'flex';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function addToCart(productId, productName, price) {
    // Fetch request to add the product to the cart
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: productId, name: productName, price: price })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('cart-count').textContent = data.cart_count;
            alert(`${productName} added to cart!`);
        } else {
            alert('Error adding to cart');
        }
    });
}

// Event listener to handle checkout form submission
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    e.preventDefault();  // Prevent the form from submitting normally
    const formData = new FormData(this);  // Collect form data

    // Send the order data to process_order.php
    fetch('process_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close checkout modal and show confirmation modal
            closeModal('checkout-modal');
            openModal('confirmation-modal');
            document.getElementById('order-id').textContent = data.order_id;  // Display the order ID
            document.getElementById('cart-count').textContent = '0';  // Reset cart count
            document.querySelector('.cart-items').innerHTML = '<p>Your cart is empty</p>';
            document.querySelector('.cart-summary').innerHTML = `
                <p>Subtotal: <span>$0.00</span></p>
                <p>Delivery Fee: <span>$2.99</span></p>
                <p>Tax: <span>$0.00</span></p>
                <p><strong>Total: <span>$0.00</span></strong></p>
            `;
        } else {
            alert('Error: ' + data.error);
        }
    });
});
