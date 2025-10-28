<?php
// File: edit_gig.php
// Edit gig
// Usage: edit_gig.php?id=1
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
if (!isset($_GET['id'])) {
    echo "Gig ID required.";
    exit;
}
$gig_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
 
$stmt = $pdo->prepare("SELECT * FROM gigs WHERE id = ? AND user_id = ?");
$stmt->execute([$gig_id, $user_id]);
$gig = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$gig) {
    echo "Gig not found or not yours.";
    exit;
}
 
$stmt_cat = $pdo->query("SELECT * FROM categories");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $image = $gig['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }
 
    $stmt_update = $pdo->prepare("UPDATE gigs SET title = ?, description = ?, price = ?, category_id = ?, image = ? WHERE id = ?");
    $stmt_update->execute([$title, $description, $price, $category_id, $image, $gig_id]);
    echo "<script>window.location.href = 'manage_gigs.php';</script>";
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Gig</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; color: #000; margin: 0; padding: 0; }
        .form-container { max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #1dbf73; border-radius: 8px; background-color: #f9f9f9; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input, textarea, select { display: block; width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #1dbf73; border-radius: 5px; }
        button { padding: 10px; background-color: #1dbf73; color: #fff; border: none; width: 100%; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background-color: #17a55f; }
        @media (max-width: 768px) { .form-container { margin: 20px; padding: 10px; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Gig</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" value="<?php echo htmlspecialchars($gig['title']); ?>" required>
            <textarea name="description" required><?php echo htmlspecialchars($gig['description']); ?></textarea>
            <input type="number" name="price" value="<?php echo $gig['price']; ?>" step="0.01" required>
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $gig['category_id']) echo 'selected'; ?>><?php echo $cat['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="file" name="image">
            <?php if ($gig['image']): ?>
                <img src="<?php echo htmlspecialchars($gig['image']); ?>" alt="Current Image" style="max-width: 100%; margin: 10px 0;">
            <?php endif; ?>
            <button type="submit">Update</button>
        </form>
        <a href="manage_gigs.php">Back</a>
    </div>
</body>
</html>
