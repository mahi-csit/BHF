
<?php
// Start the session to manage cart data
session_start();
require_once 'fetch_products.php';

// Get category from URL if set, otherwise use null for all products
$category = isset($_GET['category']) ? $_GET['category'] : null;

// Fetch products based on the selected category
$products = getProducts($category);

// Get the cart from session or initialize an empty array
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Order fresh and delicious food from Delicious Bites">
    <title>Bhimavaram Home Foods</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body>
    <header>
        <h1>Bhimavaram Home Foods</h1>
        <nav>
            <a href="#menu">Menu</a>
            <a href="#about">About</a>
            <a href="#contact">Contact</a>
        </nav>
        <div class="cart" onclick="openModal('cart-modal')">
            🛒 <span id="cart-count" aria-live="polite"><?php echo count($cart); ?></span>
        </div>
    </header>

    <main>
        <section class="hero">
            <h2>Fresh & Delicious Food</h2>
            <p>Order your favorite meals online and enjoy free delivery on your first order!</p>
            <a href="#menu" class="btn">Order Now</a>
        </section>

        <section id="menu" class="menu">
            <div class="filters">
                <button onclick="window.location.href='?category='">All</button>
                <button onclick="window.location.href='?category=Sweets'">Sweets</button>
                <button onclick="window.location.href='?category=Pickles'">Pickles</button>
                <button onclick="window.location.href='?category=Hots'">Hots</button>
            </div>
            <div class="menu-items carousel-scroll">
                <?php if (empty($products)): ?>
                    <p>No products found.</p>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <div class="menu-item">
                            <img src="images/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <p>$<?php echo number_format($product['price'], 2); ?></p>
                            <button class="btn" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">Add to Cart</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <section id="about" class="about">
            <h2>Delicious Bites</h2>
            <p>Serving quality food since 2010. We pride ourselves on using fresh ingredients and creating memorable dining experiences.</p>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            </div>
            <div class="opening-hours">
                <h3>Opening Hours</h3>
                <p>Monday - Friday: 11:00 AM - 10:00 PM</p>
                <p>Saturday - Sunday: 10:00 AM - 11:00 PM</p>
                <p>Holidays: 12:00 PM - 9:00 PM</p>
            </div>
            <div class="contact">
                <h3>Contact Us</h3>
                <p>📍 123 Tasty Street, Foodville</p>
                <p>📞 (555) 123-4567</p>
                <p>✉️ contact@deliciousbites.com</p>
            </div>
            <div class="newsletter">
                <h3>Newsletter</h3>
                <p>Subscribe to get special offers and updates.</p>
                <form>
                    <input type="email" placeholder="Enter your email" required>
                    <button type="submit" class="btn">Subscribe</button>
                </form>
            </div>
        </section>

    <footer class="footer">
        <p>© 2025 Bhimavaram Home Foods. All rights reserved.</p>
        <div>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Sitemap</a>
        </div>
    </footer>

    <div class="modal" id="cart-modal" role="dialog" aria-modal="true" aria-labelledby="cart-title">
        <div class="modal-content">
            <h2 id="cart-title">Your Cart</h2>
            <button class="close-btn" aria-label="Close cart modal" onclick="closeModal('cart-modal')">×</button>
            <div class="cart-items">
                <?php if (empty($cart)): ?>
                    <p>Your cart is empty</p>
                <?php else: ?>
                    <?php
                    $subtotal = 0;
                    foreach ($cart as $item):
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->execute([$item['id']]);
                        $product = $stmt->fetch(PDO::FETCH_ASSOC);
                        $subtotal += $product['price'] * $item['quantity'];
                    ?>
                        <div class="cart-item">
                            <p><?php echo htmlspecialchars($product['name']); ?> x <?php echo $item['quantity']; ?></p>
                            <p>$<?php echo number_format($product['price'] * $item['quantity'], 2); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="cart-summary">
                <p>Subtotal: <span>$<?php echo number_format($subtotal, 2); ?></span></p>
                <p>Delivery Fee: <span>$2.99</span></p>
                <p>Tax: <span>$<?php echo number_format($subtotal * 0.1, 2); ?></span></p>
                <p><strong>Total: <span>$<?php echo number_format($subtotal + 2.99 + ($subtotal * 0.1), 2); ?></span></strong></p>
            </div>
            <button class="btn" onclick="openModal('checkout-modal'); closeModal('cart-modal')">Proceed to Checkout</button>
        </div>
    </div>

    <div class="modal" id="checkout-modal" role="dialog" aria-modal="true" aria-labelledby="checkout-title">
        <div class="modal-content">
            <h2 id="checkout-title">Checkout</h2>
            <button class="close-btn" aria-label="Close checkout modal" onclick="closeModal('checkout-modal')">×</button>
            <form id="checkout-form">
                <div class="form-group">
                    <label for="full-name">Full Name</label>
                    <input type="text" id="full-name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <input type="text" id="address" name="address" required>
                </div>
                <div class="form-group">
                    <label for="payment">Payment Method</label>
                    <select id="payment" name="payment" required>
                        <option value="">Select Payment Method</option>
                        <option value="credit-card">Credit Card</option>
                        <option value="cash">Cash on Delivery</option>
                    </select>
                </div>
                <button type="submit" class="btn">Place Order</button>
            </form>
        </div>
    </div>

    <div class="modal" id="confirmation-modal" role="dialog" aria-modal="true" aria-labelledby="confirmation-title">
        <div class="modal-content">
            <h2 id="confirmation-title">Order Placed Successfully!</h2>
            <p>✓ Thank you for your order. We're preparing your delicious meal.</p>
            <p>Order ID: <span id="order-id"></span></p>
            <p>Estimated delivery time: 30-45 minutes</p>
            <button class="btn" onclick="closeModal('confirmation-modal')">Close</button>
        </div>
    </div>
</main>

    <script src="js/script.js"></script>
</body>
</html> 
