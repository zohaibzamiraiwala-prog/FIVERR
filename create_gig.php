<?php
// File: create_gig.php
// Create gig
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $user_id = $_SESSION['user_id'];
    $image = ''; // Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }
 
    $stmt = $pdo->prepare("INSERT INTO gigs (user_id, title, description, price, category_id, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $title, $description, $price, $category_id, $image]);
    echo "<script>window.location.href = 'manage_gigs.php';</script>";
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Gig</title>
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
        <h2>Create New Gig</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Title" required>
            <textarea name="description" placeholder="Description" required></textarea>
            <input type="number" name="price" placeholder="Price" step="0.01" required>
            <select name="category_id" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="file" name="image">
            <button type="submit">Create</button>
        </form>
        <a href="index.php">Back</a>
    </div>
</body>
</html>
