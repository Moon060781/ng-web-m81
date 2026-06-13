<?php
/**
 * NG WebMaster - Git Deployment Tool
 * Optimized UX: Compact Single-Page Layout with Date/Time & Descriptions
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$PASSWORD = "123";
$TARGET_BRANCH = "main-m81";

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

$git_user_name = trim(shell_exec("git config user.name 2>/dev/null") ?? "");
$git_user_email = trim(shell_exec("git config user.email 2>/dev/null") ?? "");
$has_identity = (!empty($git_user_name) && !empty($git_user_email));

$output = "";
if ($is_authenticated && isset($_POST['action'])) {
    $action = $_POST['action'];
    if (!function_exists('shell_exec')) {
        $output = "Error: shell_exec() is disabled.";
    } else {
        switch ($action) {
            case 'setup_git':
                $new_name = escapeshellarg($_POST['git_name'] ?? 'Nooruddin');
                $new_email = escapeshellarg($_POST['git_email'] ?? 'admin@noorgee.pk');
                shell_exec("git config --local user.name $new_name 2>&1 && git config --local user.email $new_email 2>&1");
                $output = "Identity updated.";
                $git_user_name = trim(shell_exec("git config user.name 2>/dev/null") ?? "");
                $git_user_email = trim(shell_exec("git config user.email 2>/dev/null") ?? "");
                $has_identity = (!empty($git_user_name) && !empty($git_user_email));
                break;
            case 'pull':
                $output = shell_exec("git fetch origin 2>&1 && git checkout $TARGET_BRANCH 2>&1 && git pull origin $TARGET_BRANCH 2>&1");
                break;
            case 'force_pull':
                $output = shell_exec("git fetch origin 2>&1 && git reset --hard origin/$TARGET_BRANCH 2>&1 && git clean -fd 2>&1");
                break;
            case 'push':
                if (!$has_identity) {
                    $output = "Identity missing.";
                } else {
                    $msg = !empty($_POST['commit_msg']) ? trim($_POST['commit_msg']) : "Update: " . date('Y-m-d H:i:s');
                    $desc = !empty($_POST['commit_desc']) ? trim($_POST['commit_desc']) : "";
                    // Ensure the message follows conventional format if possible, otherwise use as is
                    $safe_msg = escapeshellarg($msg . ($desc ? "\n\n" . $desc : ""));
                    $output = shell_exec("git add . 2>&1 && git commit -m $safe_msg 2>&1 && git push origin $TARGET_BRANCH 2>&1");
                }
                break;
            case 'revert_last':
                $output = shell_exec("git revert --no-edit HEAD 2>&1 && git push origin $TARGET_BRANCH 2>&1");
                break;
            case 'restore_commit':
                $hash = escapeshellarg($_POST['commit_hash'] ?? '');
                if (!empty($hash)) $output = shell_exec("git reset --hard $hash 2>&1 && git push origin $TARGET_BRANCH --force 2>&1");
                break;
            case 'undo_local':
                $output = shell_exec("git reset --hard HEAD 2>&1 && git clean -fd 2>&1");
                break;
        }
    }
}

$commit_history = [];
if ($is_authenticated) {
    $delimiter = "|||";
    $record_delimiter = "===END_COMMIT===";
    // Format: Hash | Subject | Date | Body | UnixTimestamp
    $history_raw = shell_exec("git log -10 --format='%H$delimiter%s$delimiter%ad$delimiter%b$delimiter%at$record_delimiter' --date=format:'%Y-%m-%d %H:%M' 2>/dev/null");
    if ($history_raw) {
        foreach (explode($record_delimiter, trim($history_raw)) as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $parts = explode($delimiter, $line);
            $commit_history[] = [
                'hash' => $parts[0] ?? '',
                'subject' => $parts[1] ?? '',
                'date' => $parts[2] ?? '',
                'body' => trim($parts[3] ?? ''),
                'timestamp' => $parts[4] ?? 0
            ];
        }
    }
}
$last_commit_title = $commit_history[0]['subject'] ?? "";
$last_commit_desc = $commit_history[0]['body'] ?? "";
$last_commit_time = $commit_history[0]['timestamp'] ?? 0;

// Status logic: Check if local matches remote
$status_message = "Unknown";
$status_color = "text-slate-400";
if ($is_authenticated) {
    shell_exec("git fetch origin 2>/dev/null");
    $local_hash = trim(shell_exec("git rev-parse HEAD 2>/dev/null"));
    $remote_hash = trim(shell_exec("git rev-parse origin/$TARGET_BRANCH 2>/dev/null"));
    if ($local_hash === $remote_hash) {
        $status_message = "Applied";
        $status_color = "text-green-400";
    } else {
        $status_message = "Pending / Not Applied";
        $status_color = "text-orange-400";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NG Deployer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #0f172a; color: white; font-family: 'Inter', sans-serif; overflow: hidden; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
        .terminal-box { background: #000; border-left: 3px solid #3b82f6; height: 110px; overflow-y: auto; }
        .input-field { background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(71, 85, 105, 0.5); }
        .btn-action { transition: all 0.2s; }
        .btn-action:hover { transform: translateY(-1px); }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    </style>
</head>
<body class="h-screen flex items-center justify-center p-2">
    <div class="w-full max-w-4xl glass rounded-2xl p-4 shadow-2xl border-t-2 border-blue-500 flex flex-col gap-3">
        <!-- Header & Nav -->
        <div class="flex justify-between items-center border-b border-white/5 pb-2">
            <div class="flex items-center gap-4">
                <h1 class="text-xl font-bold text-blue-400">NG WebMaster <span class="text-xs bg-blue-500/20 px-1.5 py-0.5 rounded text-blue-300">DEPLOY</span></h1>
                <div class="flex gap-3 text-sm font-bold">
                    <a href="index.html" class="text-slate-400 hover:text-blue-400"><i class="fas fa-home mr-1"></i>Home</a>
                    <a href="send_message.php" class="text-slate-400 hover:text-blue-400"><i class="fas fa-envelope mr-1"></i>Message</a>
                    <button onclick="location.reload()" class="text-slate-400 hover:text-blue-400"><i class="fas fa-sync-alt mr-1"></i>Refresh</button>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-xs uppercase tracking-wider text-slate-400">Status: <span class="font-mono <?php echo $status_color; ?>"><?php echo $status_message; ?></span></div>
                <div class="text-xs uppercase tracking-wider text-slate-400">Branch: <span class="text-white font-mono"><?php echo $TARGET_BRANCH; ?></span></div>
            </div>
        </div>

        <?php if (!$is_authenticated): ?>
            <form method="POST" class="py-10 max-w-xs mx-auto w-full space-y-3">
                <input type="password" name="password" placeholder="Password" class="w-full input-field rounded-lg px-4 py-2 text-center text-sm outline-none focus:border-blue-500">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 py-2.5 rounded-lg font-bold text-base">Access</button>
            </form>
        <?php else: ?>
            <div class="grid grid-cols-12 gap-4">
                <!-- Left Column: Actions -->
                <div class="col-span-5 space-y-3">
                    <!-- Pull Actions -->
                    <div class="grid grid-cols-2 gap-2">
                        <form method="POST"><input type="hidden" name="action" value="pull">
                            <button type="submit" class="w-full py-2 bg-green-600/10 hover:bg-green-600/20 border border-green-600/30 rounded-xl text-xs font-bold text-green-400 btn-action">
                                <i class="fas fa-download mb-1 block text-base"></i> Pull
                            </button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Force Pull?')"><input type="hidden" name="action" value="force_pull">
                            <button type="submit" class="w-full py-2 bg-blue-600/10 hover:bg-blue-600/20 border border-blue-600/30 rounded-xl text-xs font-bold text-blue-400 btn-action">
                                <i class="fas fa-sync mb-1 block text-base"></i> Force
                            </button>
                        </form>
                    </div>

                    <!-- Restore/Revert -->
                    <div class="bg-slate-900/40 border border-slate-700/50 p-3 rounded-xl space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-blue-400 uppercase">Restore History</span>
                            <form method="POST" onsubmit="return confirm('Revert last?')"><input type="hidden" name="action" value="revert_last">
                                <button type="submit" class="text-[11px] bg-orange-500/20 hover:bg-orange-500/30 text-orange-400 px-2 py-0.5 rounded border border-orange-500/30">Revert Last</button>
                            </form>
                        </div>
                        <form method="POST" class="space-y-2">
                            <input type="hidden" name="action" value="restore_commit">
                            <select name="commit_hash" class="w-full input-field rounded-lg px-2 py-1.5 text-xs outline-none" onchange="document.getElementById('c_desc').innerText = this.options[this.selectedIndex].getAttribute('data-body') || 'No extended description.'">
                                <option value="">Select commit...</option>
                                <?php foreach ($commit_history as $c): ?>
                                    <option value="<?php echo $c['hash']; ?>" data-body="<?php echo htmlspecialchars($c['body']); ?>">
                                        [<?php echo substr($c['hash'], 0, 7); ?>] <?php echo $c['date']; ?> - <?php echo htmlspecialchars($c['subject']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="c_desc" class="text-xs text-slate-400 italic h-20 overflow-y-auto px-1 leading-relaxed whitespace-pre-wrap">Select a commit to see details.</div>
                            <button type="submit" class="w-full bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 py-2 rounded-lg text-xs font-bold uppercase border border-blue-500/30">Restore Version</button>
                        </form>
                    </div>

                    <!-- Footer Links -->
                    <div class="flex gap-2">
                        <form method="POST" onsubmit="return confirm('Undo local?')" class="flex-1"><input type="hidden" name="action" value="undo_local">
                            <button type="submit" class="w-full bg-red-900/10 hover:bg-red-900/20 border border-red-900/30 py-2 rounded-lg text-xs text-red-400 font-bold uppercase">Undo Local</button>
                        </form>
                        <a href="?logout=1" class="flex-1 bg-slate-800 hover:bg-slate-700 py-2 rounded-lg text-xs text-slate-400 font-bold uppercase text-center flex items-center justify-center">Sign Out</a>
                    </div>
                </div>

                <!-- Right Column: Push & Terminal -->
                <div class="col-span-7 space-y-3">
                    <form method="POST" class="space-y-2">
                        <input type="hidden" name="action" value="push">
                        <div class="grid grid-cols-1 gap-2">
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-blue-400 uppercase ml-1 flex justify-between">
                                    <span>Commit Highlight</span>
                                    <span id="time-remaining" class="text-slate-500 lowercase font-normal"></span>
                                </label>
                                <textarea name="commit_msg" rows="2" placeholder="e.g., feat: add new feature" class="w-full input-field rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500 resize-none"></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-blue-400 uppercase ml-1">Extended Description</label>
                                <textarea name="commit_desc" rows="8" placeholder="Provide more details about the changes..." class="w-full input-field rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500 resize-none"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 py-3 rounded-xl font-bold text-base flex items-center justify-center gap-2 shadow-lg shadow-blue-900/20">
                            Push to GitHub <i class="fas fa-cloud-upload-alt"></i>
                        </button>
                    </form>

                    <!-- Terminal -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-slate-500 uppercase ml-1">Terminal Output</label>
                        <pre class="terminal-box p-3 rounded-xl text-xs font-mono leading-relaxed whitespace-pre-wrap"><?php 
                            if ($output) {
                                $h = htmlspecialchars($output);
                                $h = str_ireplace(['error','fatal','aborting'], '<span class="text-red-400">$&</span>', $h);
                                $h = str_ireplace(['success','head is now at'], '<span class="text-green-400">$&</span>', $h);
                                echo $h;
                            } else { echo '<span class="text-slate-600">Waiting for action...</span>'; }
                        ?></pre>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script>
        const lastUpdate = <?php echo $last_commit_time; ?>;
        function updateTime() {
            if (!lastUpdate) return;
            const now = Math.floor(Date.now() / 1000);
            const diff = now - lastUpdate;
            
            let timeStr = "";
            if (diff < 60) timeStr = diff + "s ago";
            else if (diff < 3600) timeStr = Math.floor(diff / 60) + "m ago";
            else if (diff < 86400) timeStr = Math.floor(diff / 3600) + "h ago";
            else timeStr = Math.floor(diff / 86400) + "d ago";
            
            const el = document.getElementById('time-remaining');
            if (el) el.innerText = "Last update: " + timeStr;
        }
        setInterval(updateTime, 10000);
        updateTime();
    </script>
</body>
</html>
