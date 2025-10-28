<?php
// File: db.php
// Database connection file
 
$host = 'localhost'; // Assuming localhost, change if needed
$dbname = 'dbjf7gcfeh08vu';
$username = 'uiumzmgo1eg2q';
$password = 'kuqi5gwec3tv';
 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
 
// SQL Schema Creation (Run this once to create tables)
// You can run this script separately or include it here for initial setup.
// Comment out after first run to avoid recreating tables.
 
function createTables($pdo) {
    // Users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role ENUM('buyer', 'seller', 'both') DEFAULT 'both',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
 
    // Categories table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL
        )
    ");
 
    // Gigs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS gigs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            category_id INT NOT NULL,
            image VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
        )
    ");
 
    // Orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            gig_id INT NOT NULL,
            buyer_id INT NOT NULL,
            seller_id INT NOT NULL,
            status ENUM('pending', 'accepted', 'rejected', 'completed') DEFAULT 'pending',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (gig_id) REFERENCES gigs(id) ON DELETE CASCADE,
            FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
        )
    ");
 
    // Messages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            message TEXT NOT NULL,
            order_id INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL
        )
    ");
 
    // Insert default categories
    $categories = ['Graphic Design', 'Writing', 'Programming', 'Video Editing', 'Marketing'];
    foreach ($categories as $cat) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
        $stmt->execute([$cat]);
    }
}
 
// Uncomment the line below to create tables on first load, then comment it back.
// createTables($pdo);
?>
