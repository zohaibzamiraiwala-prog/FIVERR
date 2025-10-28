<?php
// File: profile.php
// Profile management
 
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'login.php';</script>";
    exit;
}
include 'db.php';
 
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->execute([$username, $email, $user_id]);
    $_SESSION['username'] = $username;
    echo "Profile updated.";
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #fff; color: #000; margin: 0; padding: 0; }
        .form-container { max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #1dbf73; border-radius: 8px; background-color: #f9f9f9; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        input { display: block; width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #1dbf73; border-radius: 5px; }
        button { padding: 10px; background-color: #1dbf73; color: #fff; border: none; width: 100%; border-radius: 5px; cursor: pointer; transition: background 0.3s; }
        button:hover { background-color: #17a55f; }
        @media (max-width: 768px) { .form-container { margin: 20px; padding: 10px; } }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Profile</h2>
        <form method="POST">
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <button type="submit">Update</button>
        </form>
        <a href="index.php">Back to Home</a>
    </div>
</body>
</html>
