<?php
// File: index.php
// Homepage showcasing featured and trending gigs
 
session_start();
include 'db.php';
 
// Fetch featured gigs (e.g., top 5 by creation date)
$stmt = $pdo->query("SELECT g.*, u.username, c.name as category FROM gigs g JOIN users u ON g.user_id = u.id JOIN categories c ON g.category_id = c.id ORDER BY g.created_at DESC LIMIT 5");
$featuredGigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
// Fetch categories
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiverr Clone - Homepage</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; color: #000; margin: 0; padding: 0; }
        header { background-color: #1dbf73; color: #fff; padding: 20px; text-align: center; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .gig-card { border: 1px solid #ddd; padding: 15px; margin: 10px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .gig-card:hover { transform: scale(1.05); }
        .gig-card img { max-width: 100%; height: auto; border-radius: 5px; }
        .search-bar { margin: 20px 0; text-align: center; }
        .search-bar input { padding: 10px; width: 300px; border: 1px solid #1dbf73; border-radius: 5px; }
        .search-bar button { padding: 10px 20px; background-color: #1dbf73; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .categories { display: flex; flex-wrap: wrap; justify-content: center; }
        .category { margin: 10px; padding: 10px 20px; background-color: #1dbf73; color: #fff; border-radius: 5px; text-decoration: none; }
        nav { background-color: #1dbf73; padding: 10px; }
        nav a { color: #fff; margin: 0 15px; text-decoration: none; }
        @media (max-width: 768px) { .container { padding: 10px; } .gig-card { margin: 5px; } }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Fiverr Clone</h1>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Hello, <?php echo $_SESSION['username']; ?>! <a href="profile.php" style="color: #fff;">Profile</a> | <a href="logout.php" style="color: #fff;">Logout</a></p>
        <?php else: ?>
            <a href="login.php" style="color: #fff;">Login</a> | <a href="signup.php" style="color: #fff;">Signup</a>
        <?php endif; ?>
    </header>
    <nav>
        <a href="create_gig.php">Create Gig</a>
        <a href="manage_gigs.php">Manage Gigs</a>
        <a href="orders.php">Orders</a>
        <a href="messages.php">Messages</a>
    </nav>
    <div class="container">
        <div class="search-bar">
            <form action="search.php" method="GET">
                <input type="text" name="query" placeholder="Search gigs...">
                <button type="submit">Search</button>
            </form>
        </div>
        <h2>Categories</h2>
        <div class="categories">
            <?php foreach ($categories as $cat): ?>
                <a href="search.php?category=<?php echo $cat['id']; ?>" class="category"><?php echo $cat['name']; ?></a>
            <?php endforeach; ?>
        </div>
        <h2>Featured Gigs</h2>
        <div class="gigs">
            <?php foreach ($featuredGigs as $gig): ?>
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
    </div>
    <script>
        // JS for any client-side needs, but no redirection here
    </script>
</body>
</html>
