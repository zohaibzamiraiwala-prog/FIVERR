<?php
// File: search.php
// Search and filter gigs
 
session_start();
include 'db.php';
 
$query = isset($_GET['query']) ? $_GET['query'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
 
$sql = "SELECT g.*, u.username, c.name as category FROM gigs g JOIN users u ON g.user_id = u.id JOIN categories c ON g.category_id = c.id WHERE 1=1";
$params = [];
 
if ($query) {
    $sql .= " AND (g.title LIKE ? OR g.description LIKE ?)";
    $params[] = "%$query%";
    $params[] = "%$query%";
}
if ($category) {
    $sql .= " AND g.category_id = ?";
    $params[] = $category;
}
if ($min_price) {
    $sql .= " AND g.price >= ?";
    $params[] = $min_price;
}
if ($max_price) {
    $sql .= " AND g.price <= ?";
    $params[] = $max_price;
}
 
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$gigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
$stmt_cat = $pdo->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Gigs</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; color: #000; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .filter-form { margin: 20px 0; }
        .filter-form input, select { padding: 10px; margin: 5px; border: 1px solid #1dbf73; border-radius: 5px; }
        .filter-form button { padding: 10px 20px; background-color: #1dbf73; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .gig-card { border: 1px solid #ddd; padding: 15px; margin: 10px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .gig-card:hover { transform: scale(1.05); }
        .gig-card img { max-width: 100%; height: auto; border-radius: 5px; }
        @media (max-width: 768px) { .container { padding: 10px; } .gig-card { margin: 5px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Search Results</h2>
        <form class="filter-form" method="GET">
            <input type="text" name="query" value="<?php echo htmlspecialchars($query); ?>" placeholder="Keyword">
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $category) echo 'selected'; ?>><?php echo $cat['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="min_price" value="<?php echo htmlspecialchars($min_price); ?>" placeholder="Min Price">
            <input type="number" name="max_price" value="<?php echo htmlspecialchars($max_price); ?>" placeholder="Max Price">
            <button type="submit">Filter</button>
        </form>
        <div class="gigs">
            <?php foreach ($gigs as $gig): ?>
                <div class="gig-card">
                    <h3><?php echo htmlspecialchars($gig['title']); ?></h3>
                    <p>By: <?php echo htmlspecialchars($gig['username']); ?> | Category: <?php echo htmlspecialchars($gig['category']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($gig['description'])); ?></p>
                    <p>Price: $<?php echo $gig['price']; ?></p>
                    <?php if ($gig['image']): ?>
                        <img src="<?php echo htmlspecialchars($gig['image']); ?>" alt="Gig Image">
                    <?php endif; ?>
                    <a href="order.php?gig_id=<?php echo $gig['id']; ?>">Order Now</a>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>
