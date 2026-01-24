<?php
/**
 * NG WebMaster - Git Deployment Tool
 * Updated with Identity Diagnostics & Error Capture
 */

// 1. Enable Error Reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$PASSWORD = "123";

// Set your specific branch name
$TARGET_BRANCH = "main-m81";

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

// Diagnostics: Check if Git identity is set
$git_user_name = trim(shell_exec("git config user.name 2>/dev/null") ?? "");
$git_user_email = trim(shell_exec("git config user.email 2>/dev/null") ?? "");
$has_identity = (!empty($git_user_name) && !empty($git_user_email));

// Logic to fetch the latest commit info
$last_commit_title = "";
$last_commit_desc = "";
if ($is_authenticated) {
    $last_commit_title = trim(shell_exec("git log -1 --format=%s 2>/dev/null") ?? "");
    $last_commit_desc = trim(shell_exec("git log -1 --format=%b 2>/dev/null") ?? "");
}

// Git Command Execution
$output = "";
if ($is_authenticated && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if (!function_exists('shell_exec')) {
        $output = "Error: shell_exec() is disabled on this server.";
    } else {
        switch ($action) {
            case 'pull':
                $cmd = "git fetch origin 2>&1 && git checkout $TARGET_BRANCH 2>&1 && git pull origin $TARGET_BRANCH 2>&1";
                $output = shell_exec($cmd);
                break;
            case 'force_pull':
                $cmd = "git fetch origin 2>&1 && git reset --hard origin/$TARGET_BRANCH 2>&1 && git clean -fd 2>&1";
                $output = shell_exec($cmd);
                break;
            case 'push':
                if (!$has_identity) {
                    $output = "CRITICAL ERROR: Git identity (name/email) not set on server.\nRun: git config --global user.email \"you@example.com\" && git config --global user.name \"Your Name\" in your server terminal.";
                } else {
                    $msg = !empty($_POST['commit_msg']) ? $_POST['commit_msg'] : "Live Update: " . date('Y-m-d H:i:s');
                    $desc = !empty($_POST['commit_desc']) ? $_POST['commit_desc'] : "";
                    $full_msg = $msg . ($desc ? "\n\n" . $desc : "");
                    $safe_msg = escapeshellarg($full_msg);
                    
                    // Combined add, commit, and push with full error capture (2>&1)
                    $cmd = "git add . 2>&1 && git commit -m $safe_msg 2>&1 && git push origin $TARGET_BRANCH 2>&1";
                    $output = shell_exec($cmd);
                }
                break;
            case 'revert':
                $cmd = "git reset --hard HEAD 2>&1 && git clean -fd 2>&1";
                $output = shell_exec($cmd);
                break;
        }
    }
    // Refresh commit info after actions
    $last_commit_title = trim(shell_exec("git log -1 --format=%s 2>/dev/null") ?? "");
    $last_commit_desc = trim(shell_exec("git log -1 --format=%b 2>/dev/null") ?? "");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NG Deployer | Branch: <?php echo $TARGET_BRANCH; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; color: white; font-family: 'Inter', sans-serif; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
        .terminal-box { background: #000; border-left: 4px solid #3b82f6; box-shadow: inset 0 0 20px rgba(59, 130, 246, 0.1); }
        .term-success { color: #4ade80; font-weight: bold; }
        .term-error { color: #f87171; font-weight: bold; }
        .term-info { color: #60a5fa; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-xl w-full glass rounded-3xl p-8 shadow-2xl border-t-4 border-blue-500">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-blue-400 tracking-tight">NG WebMaster <span class="text-xs align-top font-normal bg-blue-500/20 px-2 py-0.5 rounded ml-1 text-blue-300">DEPLOY</span></h1>
            <p class="text-slate-400 text-xs uppercase tracking-widest mt-2">Active Branch: <span class="text-white font-mono font-bold"><?php echo $TARGET_BRANCH; ?></span></p>
        </div>

        <?php if ($is_authenticated && !$has_identity): ?>
            <div class="bg-red-500/20 border border-red-500/50 p-3 rounded-xl mb-6 text-center">
                <p class="text-red-400 text-[10px] font-bold uppercase tracking-widest"><i class="fas fa-exclamation-triangle mr-2"></i>Git Identity Missing</p>
                <p class="text-slate-300 text-[9px] mt-1 italic">Commit/Push may fail. Run 'git config' on server terminal.</p>
            </div>
        <?php endif; ?>

        <?php if (!$is_authenticated): ?>
            <form method="POST" class="space-y-4">
                <input type="password" name="password" placeholder="Admin Password" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 focus:border-blue-500 outline-none text-center">
                <?php if (isset($error)): ?>
                    <p class="text-red-400 text-xs text-center font-bold"><?php echo $error; ?></p>
                <?php endif; ?>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 py-3 rounded-xl font-bold transition-all">Access Terminal</button>
            </form>
        <?php else: ?>
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-b border-white/5 pb-4">
                    <form method="POST">
                        <input type="hidden" name="action" value="pull">
                        <button type="submit" class="w-full h-full flex flex-col items-center justify-center bg-green-600/10 hover:bg-green-600/20 border border-green-600/30 p-4 rounded-2xl transition-all group">
                            <i class="fas fa-cloud-download-alt text-xl text-green-500 mb-2"></i>
                            <span class="block font-bold text-green-400 text-sm">Standard Pull</span>
                        </button>
                    </form>

                    <form method="POST" onsubmit="return confirm('FORCE PULL: Overwrite local changes?')">
                        <input type="hidden" name="action" value="force_pull">
                        <button type="submit" class="w-full h-full flex flex-col items-center justify-center bg-blue-600/10 hover:bg-blue-600/20 border border-blue-600/30 p-4 rounded-2xl transition-all group">
                            <i class="fas fa-sync-alt text-xl text-blue-500 mb-2"></i>
                            <span class="block font-bold text-blue-400 text-sm">Force Pull</span>
                        </button>
                    </form>
                </div>

                <form method="POST" class="space-y-3">
                    <input type="hidden" name="action" value="push">
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase text-blue-400 font-bold ml-1">Commit Highlight</label>
                        <input type="text" name="commit_msg" value="<?php echo htmlspecialchars($last_commit_title); ?>" class="w-full bg-slate-900/80 border border-slate-600 rounded-xl px-4 py-2 text-sm focus:border-blue-500 outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] uppercase text-blue-400 font-bold ml-1">Extended Description</label>
                        <textarea name="commit_desc" rows="2" class="w-full bg-slate-900/80 border border-slate-600 rounded-xl px-4 py-2 text-sm focus:border-blue-500 outline-none"><?php echo htmlspecialchars($last_commit_desc); ?></textarea>
                    </div>
                    <button type="submit" class="w-full flex items-center justify-between bg-blue-600 hover:bg-blue-500 p-4 rounded-2xl transition-all shadow-lg shadow-blue-900/20">
                        <div class="text-left font-bold text-white">Push to GitHub <span class="text-[9px] block opacity-50 uppercase">Update remote repo</span></div>
                        <i class="fas fa-cloud-upload-alt text-xl text-white"></i>
                    </button>
                </form>

                <div class="flex gap-3">
                    <form method="POST" onsubmit="return confirm('Undo all local changes?')" class="flex-1">
                        <input type="hidden" name="action" value="revert">
                        <button type="submit" class="w-full bg-red-900/20 hover:bg-red-900/40 border border-red-900/50 p-3 rounded-xl text-xs text-red-400 font-bold uppercase tracking-tight">Undo Local</button>
                    </form>
                    <a href="?logout=1" class="flex-1 bg-slate-800 hover:bg-slate-700 p-3 rounded-xl text-xs text-slate-400 font-bold uppercase text-center tracking-tight">Sign Out</a>
                </div>
            </div>

            <?php if ($output): ?>
                <div class="mt-8">
                    <label class="text-[10px] uppercase tracking-widest text-slate-500 font-bold ml-1">Terminal History</label>
                    <pre class="terminal-box p-5 rounded-2xl text-[10px] font-mono leading-relaxed overflow-x-auto whitespace-pre-wrap"><?php 
                        $highlighted = htmlspecialchars($output);
                        $highlighted = str_ireplace('error', '<span class="term-error">error</span>', $highlighted);
                        $highlighted = str_ireplace('fatal', '<span class="term-error">fatal</span>', $highlighted);
                        $highlighted = str_ireplace('Aborting', '<span class="term-error">Aborting</span>', $highlighted);
                        $highlighted = str_ireplace('HEAD is now at', '<span class="term-success">HEAD is now at</span>', $highlighted);
                        $highlighted = str_ireplace('success', '<span class="term-success">success</span>', $highlighted);
                        $highlighted = str_ireplace('Already up to date', '<span class="term-info">Already up to date</span>', $highlighted);
                        echo $highlighted; 
                    ?></pre>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>
</html>
