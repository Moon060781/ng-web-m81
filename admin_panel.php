<?php
// Simple Security
$auth_pass = "123"; 
if (!isset($_SERVER['PHP_AUTH_PW']) || $_SERVER['PHP_AUTH_PW'] != $auth_pass) {
    header('WWW-Authenticate: Basic realm="NoorGee Admin"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Access Denied';
    exit;
}

// Database Connection
$host     = "localhost";
$db_name  = "noorgeec_pf";
$username = "noorgeec_wb";
$password = "Pf_wb_12-30";

$filter = isset($_GET['site']) ? $_GET['site'] : 'all';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if site_source column exists, if not add it
    // Standard MySQL doesn't support ADD COLUMN IF NOT EXISTS
    $check = $conn->query("SHOW COLUMNS FROM `messages` LIKE 'site_source'");
    if (!$check->fetch()) {
        $conn->exec("ALTER TABLE messages ADD COLUMN site_source VARCHAR(100) DEFAULT 'Unknown' AFTER message");
    }

    $query = "SELECT * FROM messages";
    if ($filter !== 'all') {
        $query .= " WHERE site_source = :site";
    }
    $query .= " ORDER BY id DESC";

    $stmt = $conn->prepare($query);
    if ($filter !== 'all') {
        $stmt->bindParam(':site', $filter);
    }
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel | NoorGee Unified Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 p-6">
    <div class="max-w-6xl mx-auto">
        <header class="flex flex-col md:flex-row justify-between items-center mb-10 bg-white p-8 rounded-3xl shadow-sm border border-slate-200 gap-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Message Inbox of Web NoorGee</h1>
                <p class="text-slate-500 font-medium">Viewing <?php echo $filter == 'all' ? 'All' : $filter; ?> Messages (<?php echo count($messages); ?>)</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <form action="" method="GET" class="flex items-center bg-slate-100 rounded-xl px-4 py-2 border border-slate-200">
                    <label class="text-xs font-bold text-slate-500 uppercase mr-3">Filter Source:</label>
                    <select name="site" onchange="this.form.submit()" class="bg-transparent border-none outline-none font-bold text-slate-700 text-sm cursor-pointer">
                        <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Messages</option>
                        <option value="noorgee.pk/Web" <?php echo $filter == 'noorgee.pk/Web' ? 'selected' : ''; ?>>noorgee.pk/Web</option>
                        <option value="noorgee.pk/Dev" <?php echo $filter == 'noorgee.pk/Dev' ? 'selected' : ''; ?>>noorgee.pk/Dev</option>
                    </select>
                </form>
                <a href="index.html" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition">Live Site</a>
            </div>
        </header>

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="p-5 font-bold text-slate-700 text-sm uppercase tracking-wider">Info</th>
                            <th class="p-5 font-bold text-slate-700 text-sm uppercase tracking-wider">Source</th>
                            <th class="p-5 font-bold text-slate-700 text-sm uppercase tracking-wider">Message</th>
                            <th class="p-5 font-bold text-slate-700 text-sm uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($messages as $row): ?>
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="p-5">
                                <div class="font-bold text-slate-900"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="text-xs text-slate-400 mt-1"><?php echo date('M d, Y h:i A', strtotime($row['created_at'])); ?></div>
                            </td>
                            <td class="p-5">
                                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border <?php echo strpos($row['site_source'], 'Dev') !== false ? 'bg-purple-50 text-purple-600 border-purple-100' : 'bg-blue-50 text-blue-600 border-blue-100'; ?>">
                                    <?php echo htmlspecialchars($row['site_source']); ?>
                                </span>
                            </td>
                            <td class="p-5">
                                <div class="text-xs text-blue-600 mb-1 font-medium"><?php echo htmlspecialchars($row['email']); ?></div>
                                <div class="text-sm text-slate-600 line-clamp-2 max-w-md"><?php echo nl2br(htmlspecialchars($row['message'])); ?></div>
                            </td>
                            <td class="p-5 text-right">
                                <a href="mailto:<?php echo $row['email']; ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white transition shadow-sm">
                                    <i class="fa-solid fa-reply"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (empty($messages)): ?>
                <div class="py-32 text-center text-slate-300">
                    <i class="fa-solid fa-mailbox text-6xl mb-6 opacity-20"></i>
                    <p class="text-lg font-medium">No messages found in this category.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

