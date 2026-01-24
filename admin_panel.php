<?php
session_start();

// --- CONFIGURATION ---
$ADMIN_PASSWORD = "123"; // Password to access this panel
$DB_HOST = "localhost";
$DB_NAME = "noorgeec_pf";
$DB_USER = "noorgeec_wb";
$DB_PASS = "Pf_wb_12-30";

// --- AUTHENTICATION ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_panel.php");
    exit;
}

if (isset($_POST['login_password'])) {
    if ($_POST['login_password'] === $ADMIN_PASSWORD) {
        $_SESSION['admin_auth'] = true;
    } else {
        $error = "Invalid Password";
    }
}

$is_authenticated = isset($_SESSION['admin_auth']) && $_SESSION['admin_auth'] === true;

// --- DATABASE CONNECTION ---
$pdo = null;
$db_error = null;
if ($is_authenticated) {
    try {
        $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
    } catch (PDOException $e) {
        $db_error = "Database Connection Failed: " . $e->getMessage();
    }
}

// --- DELETE ACTION ---
if ($is_authenticated && $pdo && isset($_POST['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
        $stmt->execute([$_POST['delete_id']]);
        $success_msg = "Message deleted successfully.";
    } catch (PDOException $e) {
        $db_error = "Failed to delete: " . $e->getMessage();
    }
}

// --- FILTER & FETCH MESSAGES ---
$messages = [];
// DEFAULT SELECTED: noorgee.pk/Web
$filter_source = $_GET['filter_source'] ?? 'noorgee.pk/Web';

if ($is_authenticated && $pdo) {
    try {
        $sql = "SELECT * FROM messages";
        $params = [];

        // Apply Filter if not 'All sites'
        if ($filter_source !== 'All sites') {
            $sql .= " WHERE site_source = ?";
            $params[] = $filter_source;
        }

        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $messages = $stmt->fetchAll();
    } catch (PDOException $e) {
        $db_error = "Failed to fetch messages: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | NoorGee WebMaster</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .table-container { background: white; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; }
    </style>
</head>
<body class="min-h-screen pb-12">

    <!-- LOGIN SCREEN -->
    <?php if (!$is_authenticated): ?>
    <div class="flex items-center justify-center min-h-screen bg-slate-900">
        <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-slate-800">Admin Login</h1>
                <p class="text-slate-500 text-sm">NoorGee WebMaster</p>
            </div>
            <form method="POST" class="space-y-4">
                <input type="password" name="login_password" placeholder="Enter Password" class="w-full border border-gray-300 px-4 py-3 rounded-xl focus:outline-none focus:border-blue-500 transition" required autofocus>
                <?php if (isset($error)): ?>
                    <p class="text-red-500 text-sm text-center font-bold"><?php echo $error; ?></p>
                <?php endif; ?>
                <button type="submit" class="w-full bg-slate-900 text-white font-bold py-3 rounded-xl hover:bg-slate-800 transition">Access Panel</button>
            </form>
        </div>
    </div>
    <?php else: ?>

    <!-- DASHBOARD -->
    <div class="container mx-auto px-4 py-8">
        
        <!-- Header & Filter -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-200 gap-6">
            <div class="flex items-center gap-4">
                <div class="bg-blue-600 text-white p-3 rounded-xl">
                    <i class="fas fa-inbox text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Message Inbox</h1>
                    <p class="text-slate-500 text-xs font-medium uppercase tracking-widest">Active DB: <?php echo htmlspecialchars($DB_NAME); ?></p>
                </div>
            </div>
            
            <div class="flex flex-wrap items-center gap-4">
                <!-- Dropdown Filter -->
                <form method="GET" id="filterForm" class="flex items-center gap-2">
                    <label class="text-xs font-bold text-slate-400 uppercase">Filter Source:</label>
                    <div class="relative">
                        <select name="filter_source" onchange="this.form.submit()" class="appearance-none bg-slate-100 border border-slate-200 text-slate-800 py-2.5 px-4 pr-10 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 font-bold text-sm cursor-pointer shadow-sm">
                            <option value="noorgee.pk/Web" <?php echo $filter_source === 'noorgee.pk/Web' ? 'selected' : ''; ?>>noorgee.pk/Web</option>
                            <option value="noorgee.pk/Dev" <?php echo $filter_source === 'noorgee.pk/Dev' ? 'selected' : ''; ?>>noorgee.pk/Dev</option>
                            <option value="nm.noorgee.pk" <?php echo $filter_source === 'nm.noorgee.pk' ? 'selected' : ''; ?>>nm.noorgee.pk</option>
                            <option value="All sites" <?php echo $filter_source === 'All sites' ? 'selected' : ''; ?>>All sites</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-500">
                            <i class="fas fa-caret-down text-xs"></i>
                        </div>
                    </div>
                </form>

                <div class="h-8 w-[1px] bg-slate-200 hidden md:block"></div>

                <div class="flex gap-2">
                    <a href="index.html" target="_blank" class="px-4 py-2.5 bg-slate-800 text-white rounded-xl font-bold text-xs hover:bg-slate-700 transition shadow-sm">
                        <i class="fas fa-external-link-alt mr-2"></i> WEBSITE
                    </a>
                    <a href="?logout=1" class="px-4 py-2.5 bg-red-50 text-red-600 rounded-xl font-bold text-xs hover:bg-red-100 transition border border-red-100">
                        LOGOUT
                    </a>
                </div>
            </div>
        </div>

        <?php if ($db_error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3">
                <i class="fas fa-exclamation-triangle"></i>
                <span class="text-sm font-medium"><?php echo htmlspecialchars($db_error); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($success_msg)): ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl mb-6 flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-sm font-medium"><?php echo htmlspecialchars($success_msg); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-900 opacity-50 hover:opacity-100 transition"><i class="fas fa-times"></i></button>
            </div>
        <?php endif; ?>

        <!-- Messages Table -->
        <div class="table-container overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 text-[10px] uppercase font-bold tracking-[0.1em]">
                            <th class="p-5">ID & Date</th>
                            <th class="p-5">Website Source</th>
                            <th class="p-5">User Info</th>
                            <th class="p-5">Subject</th>
                            <th class="p-5 w-1/3">Message Content</th>
                            <th class="p-5 text-right">Manage</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                        <?php if (empty($messages)): ?>
                            <tr>
                                <td colspan="6" class="p-12 text-center">
                                    <div class="text-slate-300 mb-2"><i class="fas fa-folder-open text-5xl"></i></div>
                                    <p class="text-slate-400 italic">No messages found for "<?php echo htmlspecialchars($filter_source); ?>".</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="p-5">
                                    <div class="font-mono text-[10px] text-slate-400">#<?php echo htmlspecialchars($msg['id'] ?? '-'); ?></div>
                                    <div class="text-[11px] text-slate-500 mt-1 font-medium"><?php echo date('d M Y, h:i A', strtotime($msg['created_at'])); ?></div>
                                </td>
                                <td class="p-5">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-slate-100 text-slate-600 text-[10px] rounded-lg font-bold border border-slate-200">
                                        <?php echo htmlspecialchars($msg['site_source'] ?? 'Unknown'); ?>
                                    </span>
                                </td>
                                <td class="p-5">
                                    <div class="font-bold text-slate-800"><?php echo htmlspecialchars($msg['name'] ?? 'Guest'); ?></div>
                                    <div class="text-[11px] text-blue-500 font-medium lowercase"><?php echo htmlspecialchars($msg['email'] ?? ''); ?></div>
                                </td>
                                <td class="p-5 font-semibold text-slate-600 italic">
                                    <?php echo htmlspecialchars($msg['subject'] ?? '(No Subject)'); ?>
                                </td>
                                <td class="p-5">
                                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-slate-600 text-xs leading-relaxed max-h-32 overflow-y-auto whitespace-pre-wrap">
                                        <?php echo htmlspecialchars($msg['message'] ?? ''); ?>
                                    </div>
                                </td>
                                <td class="p-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Forward Button (Email) -->
                                        <a href="mailto:?subject=Fwd: <?php echo urlencode($msg['subject']); ?>&body=<?php echo urlencode("Original Message Details:\n\nFrom: " . $msg['name'] . "\nEmail: " . $msg['email'] . "\nDate: " . $msg['created_at'] . "\nSource: " . $msg['site_source'] . "\n\nMessage:\n" . $msg['message']); ?>" 
                                           class="w-9 h-9 flex items-center justify-center bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-600 hover:text-white transition-all shadow-sm border border-emerald-100" title="Forward Message">
                                            <i class="fas fa-share text-xs"></i>
                                        </a>

                                        <!-- Delete Form -->
                                        <form method="POST" onsubmit="return confirm('Confirm permanent deletion of this message?');" class="inline">
                                            <input type="hidden" name="delete_id" value="<?php echo $msg['id']; ?>">
                                            <button type="submit" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-sm border border-red-100" title="Delete Message">
                                                <i class="fas fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-8 text-center text-[10px] text-slate-400 font-bold uppercase tracking-[0.2em]">
            &copy; <?php echo date('Y'); ?> NoorGee WebMaster Solutions | Private Admin Panel
        </div>
    </div>
    <?php endif; ?>

</body>
</html>
