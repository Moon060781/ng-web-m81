<?php
/**
 * NG WebMaster - Git Deployment Tool
 * Password: 123
 */

// 1. Enable Error Reporting (To diagnose why the screen was blank)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$PASSWORD = "123";

// Authentication Logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: deploy.php");
    exit;
}

if (isset($_POST['password'])) {
    if ($_POST['password'] === $PASSWORD) {
        $_SESSION['auth'] = true;
    } else {
        $error = "Invalid Password";
    }
}

$is_authenticated = isset($_SESSION['auth']) && $_SESSION['auth'] === true;

// Git Command Execution
$output = "";
if ($is_authenticated && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    // Check if shell_exec is enabled on your hosting
    if (!function_exists('shell_exec')) {
        $output = "Error: shell_exec() is disabled on this server. Please contact hosting support.";
    } else {
        switch ($action) {
            case 'pull':
                // Get updates from GitHub to Live Site
                $output = shell_exec("git pull origin main 2>&1");
                break;
            case 'push':
                // Send updates from Live Site to GitHub
                $cmd = "git add . && git commit -m 'Update from Live cPanel: " . date('Y-m-d H:i:s') . "' && git push origin main 2>&1";
                $output = shell_exec($cmd);
                break;
            case 'revert':
                // Hard reset to last commit (Undo local changes)
                $output = shell_exec("git reset --hard HEAD 2>&1");
                break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NG Deployer | Git Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; color: white; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
        .bounce { animation: bounce 2s infinite; }
        @keyframes bounce { 0%, 20%, 50%, 80%, 100% {transform: translateY(0);} 40% {transform: translateY(-5px);} 60% {transform: translateY(-3px);} }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full glass rounded-3xl p-8 shadow-2xl">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-blue-400">NG WebMaster</h1>
            <p class="text-slate-400 text-sm">Deployment & Version Control</p>
        </div>

        <?php if (!$is_authenticated): ?>
            <!-- Login Form -->
            <form method="POST" class="space-y-4">
                <div>
                    <input type="password" name="password" placeholder="Enter Admin Password" 
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:outline-none focus:border-blue-500 transition-all text-center" autofocus>
                </div>
                <?php if (isset($error)): ?>
                    <div class="bg-red-500/10 border border-red-500/20 p-3 rounded-lg text-red-400 text-xs text-center">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 py-3 rounded-xl font-bold transition-all">
                    Login to Terminal
                </button>
            </form>
        <?php else: ?>
            <!-- Action Panel -->
            <div class="space-y-4">
                
                <form method="POST">
                    <input type="hidden" name="action" value="pull">
                    <button type="submit" class="w-full flex items-center justify-between bg-green-600/20 hover:bg-green-600/30 border border-green-600/50 p-4 rounded-2xl transition-all group">
                        <span class="font-semibold text-green-400">Pull from GitHub</span>
                        <i class="fas fa-download group-hover:bounce"></i>
                    </button>
                </form>

                <form method="POST">
                    <input type="hidden" name="action" value="push">
                    <button type="submit" class="w-full flex items-center justify-between bg-blue-600/20 hover:bg-blue-600/30 border border-blue-600/50 p-4 rounded-2xl transition-all group">
                        <span class="font-semibold text-blue-400">Push to GitHub</span>
                        <i class="fas fa-upload"></i>
                    </button>
                </form>

                <form method="POST" onsubmit="return confirm('Are you sure? This will delete all uncommitted changes on the live site.')">
                    <input type="hidden" name="action" value="revert">
                    <button type="submit" class="w-full flex items-center justify-between bg-orange-600/20 hover:bg-orange-600/30 border border-orange-600/50 p-4 rounded-2xl transition-all group">
                        <span class="font-semibold text-orange-400">Revert (Undo)</span>
                        <i class="fas fa-undo"></i>
                    </button>
                </form>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <a href="index.html" class="flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-700 p-3 rounded-xl text-sm transition-all">
                        <i class="fas fa-home text-blue-400"></i> Go Home
                    </a>
                    <a href="?logout=1" class="flex items-center justify-center gap-2 bg-slate-800 hover:bg-red-900/30 p-3 rounded-xl text-sm transition-all text-red-400">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>

            <?php if ($output): ?>
                <div class="mt-6">
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 mb-2 block font-bold text-center">Terminal Result</label>
                    <pre class="bg-black/80 p-4 rounded-xl text-[11px] font-mono text-green-400 overflow-x-auto border border-slate-800 shadow-inner leading-relaxed"><?php echo htmlspecialchars($output); ?></pre>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="mt-8 text-center text-[10px] text-slate-600 uppercase tracking-widest">
            &copy; <?php echo date('Y'); ?> NoorGee WebMaster Solutions
        </div>
    </div>

</body>
</html>
