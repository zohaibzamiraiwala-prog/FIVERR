<?php
// File: signup.php
// User signup with enhanced fields and secure authentication
 
session_start();
include 'db.php';
 
// Prevent logged-in users from accessing signup
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'synapse.php';</script>";
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $bio = trim($_POST['bio'] ?? '');
    $role = $_POST['role'];
    $profile_picture = '';
 
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        $error = "All required fields must be filled.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    } elseif (!in_array($role, ['buyer', 'seller', 'both'])) {
        $error = "Invalid role selected.";
    } else {
        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);
            $file_ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $profile_picture = $target_dir . uniqid() . '.' . $file_ext;
            if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture)) {
                $error = "Failed to upload profile picture.";
            }
        }
 
        // Proceed with registration if no errors
        if (!isset($error)) {
            try {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, role, bio, profile_picture) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$username, $email, $password_hash, $role, $bio, $profile_picture]);
                echo "<script>window.location.href = 'login.php';</script>";
                exit;
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry
                    $error = "Username or email already exists.";
                } else {
                    $error = "Error: " . $e->getMessage();
                }
            }
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Fiverr Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #ffffff, #e6f4ea);
            color: #222;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
            transition: transform 0.3s ease;
        }
        .form-container:hover {
            transform: translateY(-5px);
        }
        h2 {
            color: #1dbf73;
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
        }
        .error {
            color: #d32f2f;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #1dbf73;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #17a55f;
            outline: none;
            box-shadow: 0 0 5px rgba(29, 191, 115, 0.3);
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #1dbf73;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button:hover {
            background-color: #17a55f;
            transform: scale(1.02);
        }
        .link {
            text-align: center;
            margin-top: 15px;
        }
        .link a {
            color: #1dbf73;
            text-decoration: none;
            font-weight: bold;
        }
        .link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
                margin: 20px;
            }
            h2 {
                font-size: 20px;
            }
            input, textarea, select, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create Your Account</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
 
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
 
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password (min 8 characters)" required>
 
            <label for="role">Role</label>
            <select id="role" name="role" required>
                <option value="buyer" <?php echo (isset($_POST['role']) && $_POST['role'] == 'buyer') ? 'selected' : ''; ?>>Buyer</option>
                <option value="seller" <?php echo (isset($_POST['role']) && $_POST['role'] == 'seller') ? 'selected' : ''; ?>>Seller</option>
                <option value="both" <?php echo (isset($_POST['role']) && $_POST['role'] == 'both') ? 'selected' : ''; ?>>Both</option>
            </select>
 
            <label for="bio">Bio (Optional)</label>
            <textarea id="bio" name="bio" placeholder="Tell us about yourself"><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
 
            <label for="profile_picture">Profile Picture (Optional)</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
 
            <button type="submit">Sign Up</button>
        </form>
        <div class="link">
            <p>Already have an account? <a href="login.php">Log In</a></p>
        </div>
    </div>
</body>
</html>
