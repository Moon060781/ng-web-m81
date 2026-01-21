<?php
header('Content-Type: application/json');

// Database Configuration - UPDATED WITH YOUR CREDENTIALS
$host     = "localhost";
$db_name  = "noorgeec_pf";
$username = "noorgeec_wb";
$password = "Pf_wb_12-30";
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
    
    // Auto-fix for missing column: check if 'site_source' exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM `messages` LIKE 'site_source'");
    if (!$checkColumn->fetch()) {
        // Column does not exist, add it using correct syntax
        $pdo->exec("ALTER TABLE `messages` ADD `site_source` VARCHAR(100) DEFAULT 'Unknown' AFTER `message`");
    }
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Get Form Data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';
$site_source = $_POST['site_source'] ?? 'noorgee.pk/Web';

if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all fields.']);
    exit;
}

try {
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO messages (name, email, message, site_source, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $message, $site_source]);

    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>