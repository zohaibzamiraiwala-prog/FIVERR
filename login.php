<?php
// File: login.php
// User login with secure authentication and last_login update
 
session_start();
include 'db.php';
 
// Prevent logged-in users from accessing login
if (isset($_SESSION['user_id'])) {
    echo "<script>window.location.href = 'index.php';</script>";
    exit;
}
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
 
    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, is_active FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
            if ($user && password_verify($password, $user['password_hash'])) {
                if (!$user['is_active']) {
                    $error = "Your account is inactive. Contact support.";
                } else {
                    // Update last_login
                    $stmt_update = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                    $stmt_update->execute([$user['id']]);
 
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    echo "<script>window.location.href = 'index.php';</script>";
                    exit;
                }
            } else {
                $error = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Fiverr Clone</title>
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
        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #1dbf73;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input:focus {
            border-color: #17a55f;
            outline: none;
            box-shadow: 0 0 5px rgba(29, 191, 115, 0.3);
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
            input, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Log In to Your Account</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Enter email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
 
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password" required>
 
            <button type="submit">Log In</button>
        </form>
        <div class="link">
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </div>
    </div>
</body>
</html>
