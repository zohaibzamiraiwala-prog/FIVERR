<?php
// File: orders.php
// Manage orders (for both buyer and seller)
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
 
// Fetch orders as buyer
$stmt_buyer = $pdo->prepare("SELECT o.*, g.title, u.username as seller FROM orders o JOIN gigs g ON o.gig_id = g.id JOIN users u ON o.seller_id = u.id WHERE o.buyer_id = ?");
$stmt_buyer->execute([$user_id]);
$buyer_orders = $stmt_buyer->fetchAll(PDO::FETCH_ASSOC);
 
// Fetch orders as seller
$stmt_seller = $pdo->prepare("SELECT o.*, g.title, u.username as buyer FROM orders o JOIN gigs g ON o.gig_id = g.id JOIN users u ON o.buyer_id = u.id WHERE o.seller_id = ?");
$stmt_seller->execute([$user_id]);
$seller_orders = $stmt_seller->fetchAll(PDO::FETCH_ASSOC);
 
if (isset($_GET['action']) && isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $action = $_GET['action'];
    if (in_array($action, ['accept', 'reject', 'complete'])) {
        $status = $action == 'accept' ? 'accepted' : ($action == 'reject' ? 'rejected' : 'completed');
        $stmt_update = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND seller_id = ?");
        $stmt_update->execute([$status, $order_id, $user_id]);
        echo "<script>window.location.href = 'orders.php';</script>";
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; color: #000; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .order-card { border: 1px solid #ddd; padding: 15px; margin: 10px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        a { color: #1dbf73; text-decoration: none; }
        a:hover { text-decoration: underline; }
        @media (max-width: 768px) { .container { padding: 10px; } .order-card { margin: 5px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Orders as Buyer</h2>
        <?php foreach ($buyer_orders as $order): ?>
            <div class="order-card">
                <h3>Gig: <?php echo htmlspecialchars($order['title']); ?></h3>
                <p>Seller: <?php echo htmlspecialchars($order['seller']); ?></p>
                <p>Status: <?php echo ucfirst($order['status']); ?></p>
                <a href="messages.php?order_id=<?php echo $order['id']; ?>">Message Seller</a>
            </div>
        <?php endforeach; ?>
 
        <h2>Your Orders as Seller</h2>
        <?php foreach ($seller_orders as $order): ?>
            <div class="order-card">
                <h3>Gig: <?php echo htmlspecialchars($order['title']); ?></h3>
                <p>Buyer: <?php echo htmlspecialchars($order['buyer']); ?></p>
                <p>Status: <?php echo ucfirst($order['status']); ?></p>
                <?php if ($order['status'] == 'pending'): ?>
                    <a href="orders.php?action=accept&id=<?php echo $order['id']; ?>">Accept</a> |
                    <a href="orders.php?action=reject&id=<?php echo $order['id']; ?>">Reject</a>
                <?php elseif ($order['status'] == 'accepted'): ?>
                    <a href="orders.php?action=complete&id=<?php echo $order['id']; ?>">Mark Complete</a>
                <?php endif; ?>
                <a href="messages.php?order_id=<?php echo $order['id']; ?>">Message Buyer</a>
            </div>
        <?php endforeach; ?>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>
