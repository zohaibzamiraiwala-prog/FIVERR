<?php
// File: order.php
// Place order
// Usage: order.php?gig_id=1
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
if (!isset($_GET['gig_id'])) {
    echo "Gig ID required.";
    exit;
}
$gig_id = $_GET['gig_id'];
$buyer_id = $_SESSION['user_id'];
 
$stmt = $pdo->prepare("SELECT * FROM gigs WHERE id = ?");
$stmt->execute([$gig_id]);
$gig = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$gig) {
    echo "Gig not found.";
    exit;
}
$seller_id = $gig['user_id'];
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt_order = $pdo->prepare("INSERT INTO orders (gig_id, buyer_id, seller_id) VALUES (?, ?, ?)");
    $stmt_order->execute([$gig_id, $buyer_id, $seller_id]);
    echo "<script>window.location.href = 'orders.php';</script>";
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Order</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; color: #000; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #1dbf73; border-radius: 8px; background-color: #f9f9f9; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        button { padding: 10px; background-color: #1dbf73; color: #fff; border: none; width: 100%; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background-color: #17a55f; }
        @media (max-width: 768px) { .container { margin: 20px; padding: 10px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Place Order for <?php echo htmlspecialchars($gig['title']); ?></h2>
        <p>Price: $<?php echo $gig['price']; ?></p>
        <form method="POST">
            <button type="submit">Confirm Order</button>
        </form>
        <a href="search.php">Back</a>
    </div>
</body>
</html>
