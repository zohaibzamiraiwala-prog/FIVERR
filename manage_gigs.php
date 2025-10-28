<?php
// File: manage_gigs.php
// Manage user's gigs
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM gigs WHERE user_id = ?");
$stmt->execute([$user_id]);
$gigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gigs</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; color: #000; margin: 0; padding: 0; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .gig-card { border: 1px solid #ddd; padding: 15px; margin: 10px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .gig-card:hover { transform: scale(1.05); }
        a { color: #1dbf73; text-decoration: none; }
        a:hover { text-decoration: underline; }
        @media (max-width: 768px) { .container { padding: 10px; } .gig-card { margin: 5px; } }
    </style>
</head>
<body>
    <div class="container">
        <h2>Your Gigs</h2>
        <a href="create_gig.php">Create New Gig</a>
        <div class="gigs">
            <?php foreach ($gigs as $gig): ?>
                <div class="gig-card">
                    <h3><?php echo htmlspecialchars($gig['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($gig['description'])); ?></p>
                    <p>Price: $<?php echo $gig['price']; ?></p>
                    <a href="edit_gig.php?id=<?php echo $gig['id']; ?>">Edit</a> |
                    <a href="delete_gig.php?id=<?php echo $gig['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>
