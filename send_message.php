<?php
header('Content-Type: application/json');

// Database Configuration
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
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

// Get Form Data
$name        = $_POST['name'] ?? '';
$email       = $_POST['email'] ?? '';
$subject     = $_POST['subject'] ?? 'No Subject';
$contact_no  = $_POST['contact_no'] ?? '';
$message     = $_POST['message'] ?? '';

// Capture Source Domain Automatically
$site_source = $_SERVER['HTTP_REFERER'] ?? 'Direct Access';

// Validation
if (empty($name) || empty($email) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Please fill mandatory fields (Name, Email, Message).']);
    exit;
}

try {
    // Insert into 'messages' table based on your schema
    $sql = "INSERT INTO messages (site_source, name, email, subject, message) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    // Note: If you want to store contact_no, make sure to add that column to your DB as well.
    // For now, I am appending it to the message or subject if the column isn't in your list.
    $full_message = "Contact No: " . $contact_no . "\n\n" . $message;
    
    $stmt->execute([
        $site_source, 
        $name, 
        $email, 
        $subject, 
        $full_message
    ]);

    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
} catch (\PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
?>
