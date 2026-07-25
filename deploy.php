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

$git_user_name = trim(shell_exec("git -c safe.directory=* config user.name 2>/dev/null") ?? "");
$git_user_email = trim(shell_exec("git -c safe.directory=* config user.email 2>/dev/null") ?? "");
$has_identity = (!empty($git_user_name) && !empty($git_user_email));

$output = "";
if ($is_authenticated && isset($_POST['action'])) {
    $action = $_POST['action'];
    if (!function_exists('shell_exec')) {
        $output = "Error: shell_exec() is disabled.";
    } else {
        switch ($action) {
            case 'setup_git':
                $new_name = escapeshellarg($_POST['git_name'] ?? 'antigravity ng web');
                $new_email = escapeshellarg($_POST['git_email'] ?? 'admin@noorgee.pk');
                shell_exec("git -c safe.directory=* config --local user.name $new_name 2>&1 && git -c safe.directory=* config --local user.email $new_email 2>&1");
                $output = "Identity updated.";
                $git_user_name = trim(shell_exec("git -c safe.directory=* config user.name 2>/dev/null") ?? "");
                $git_user_email = trim(shell_exec("git -c safe.directory=* config user.email 2>/dev/null") ?? "");
                $has_identity = (!empty($git_user_name) && !empty($git_user_email));
                break;
            case 'pull':
                $output = shell_exec("git -c safe.directory=* fetch origin 2>&1 && git -c safe.directory=* checkout $TARGET_BRANCH 2>&1 && git -c safe.directory=* pull origin $TARGET_BRANCH 2>&1");
                break;
            case 'force_pull':
                $output = shell_exec("git -c safe.directory=* fetch origin 2>&1 && git -c safe.directory=* reset --hard origin/$TARGET_BRANCH 2>&1 && git -c safe.directory=* clean -fd 2>&1");
                break;
            case 'push':
                if (!$has_identity) {
                    $output = "Identity missing.";
                } else {
                    $msg = !empty($_POST['commit_msg']) ? trim($_POST['commit_msg']) : "Update: " . date('Y-m-d H:i:s');
                    $desc = !empty($_POST['commit_desc']) ? trim($_POST['commit_desc']) : "";
                    $safe_msg = escapeshellarg($msg . ($desc ? "\n\n" . $desc : ""));
                    $output = shell_exec("git -c safe.directory=* add . 2>&1 && git -c safe.directory=* commit -m $safe_msg 2>&1 && git -c safe.directory=* push origin $TARGET_BRANCH 2>&1");
                }
                break;
            case 'revert_last':
                $output = shell_exec("git -c safe.directory=* revert --no-edit HEAD 2>&1 && git -c safe.directory=* push origin $TARGET_BRANCH 2>&1");
                break;
            case 'restore_commit':
                $hash = escapeshellarg($_POST['commit_hash'] ?? '');
                if (!empty($hash)) $output = shell_exec("git -c safe.directory=* reset --hard $hash 2>&1 && git -c safe.directory=* push origin $TARGET_BRANCH --force 2>&1");
                break;
            case 'undo_local':
                $output = shell_exec("git -c safe.directory=* reset --hard HEAD 2>&1 && git -c safe.directory=* clean -fd 2>&1");
                break;
        }
    }
}

$commit_history = [];
if ($is_authenticated) {
    shell_exec("git config --global --add safe.directory '*' 2>/dev/null");
    $delimiter = "|||";
    $record_delimiter = "===END_COMMIT===";
    // Format: Hash | Subject | Date | Body | UnixTimestamp | Author
    $history_raw = shell_exec("git -c safe.directory=* log -15 --format=\"%H{$delimiter}%s{$delimiter}%ad{$delimiter}%b{$delimiter}%at{$delimiter}%an{$record_delimiter}\" --date=format:\"%Y-%m-%d %H:%M\" 2>&1");
    if ($history_raw && !str_contains($history_raw, 'fatal:')) {
        foreach (explode($record_delimiter, trim($history_raw)) as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            $parts = explode($delimiter, $line);
            if (count($parts) >= 4) {
                $commit_history[] = [
                    'hash' => $parts[0] ?? '',
                    'subject' => $parts[1] ?? '',
                    'date' => $parts[2] ?? '',
                    'body' => trim($parts[3] ?? ''),
                    'timestamp' => intval($parts[4] ?? 0),
                    'author' => $parts[5] ?? 'Unknown'
                ];
            }
        }
    }
}
$last_commit_title = $commit_history[0]['subject'] ?? "";
$last_commit_desc = $commit_history[0]['body'] ?? "";
$last_commit_time = $commit_history[0]['timestamp'] ?? 0;

$local_hash = "";
$pkt_date = "";

// Status logic: Check if local matches remote
$status_message = "Unknown";
$status_color = "text-slate-400";
if ($is_authenticated) {
    shell_exec("git -c safe.directory=* fetch origin 2>/dev/null");
    $local_hash = trim(shell_exec("git -c safe.directory=* rev-parse HEAD 2>/dev/null"));
    $remote_hash = trim(shell_exec("git -c safe.directory=* rev-parse origin/$TARGET_BRANCH 2>/dev/null"));
    if ($local_hash && $remote_hash && $local_hash === $remote_hash) {
        $status_message = "Applied";
        $status_color = "text-green-400";
    } else {
        $status_message = "Pending / Not Applied";
        $status_color = "text-orange-400";
    }
    
    if ($last_commit_time) {
        try {
            $dt = new DateTime("@$last_commit_time");
            $dt->setTimezone(new DateTimeZone('Asia/Karachi'));
            $pkt_date = $dt->format('d M Y, h:i A');
        } catch (Exception $e) {
            $pkt_date = date('d M Y, h:i A', $last_commit_time);
        }
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
                <!-- Left Column: Actions & Commit History -->
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

                    <!-- Commit History Visual Box -->
                    <div class="bg-slate-900/40 border border-slate-700/50 p-2.5 rounded-xl space-y-2">
                        <div class="flex justify-between items-center px-1">
                            <span class="text-xs font-bold text-blue-400 uppercase flex items-center gap-1.5">
                                <i class="fas fa-history text-xs"></i> Recent Commits
                            </span>
                            <span class="text-[10px] text-slate-400 bg-slate-800 px-2 py-0.5 rounded-full font-mono">
                                <?php echo count($commit_history); ?> Listed
                            </span>
                        </div>

                        <!-- Scrollable Commit Cards -->
                        <div class="space-y-1.5 max-h-44 overflow-y-auto pr-1">
                            <?php if (empty($commit_history)): ?>
                                <div class="text-xs text-slate-500 p-3 text-center bg-slate-950/40 rounded-lg border border-slate-800">
                                    No commits found or Git unavailable.
                                </div>
                            <?php else: ?>
                                <?php foreach ($commit_history as $idx => $c): ?>
                                    <div class="p-2 bg-slate-950/60 hover:bg-slate-800/80 rounded-lg border border-slate-800/80 transition-all text-xs space-y-1 cursor-pointer" onclick="selectCommitForRestore('<?php echo $c['hash']; ?>')">
                                        <div class="flex justify-between items-center text-[10px]">
                                            <span class="font-mono bg-blue-500/20 text-blue-300 px-1.5 py-0.5 rounded font-bold">
                                                <?php echo substr($c['hash'], 0, 7); ?>
                                            </span>
                                            <span class="text-slate-400 font-mono"><?php echo $c['date']; ?></span>
                                        </div>
                                        <div class="font-semibold text-slate-200 text-xs leading-snug truncate">
                                            <?php echo htmlspecialchars($c['subject']); ?>
                                        </div>
                                        <div class="flex justify-between items-center text-[10px] text-slate-400 pt-0.5">
                                            <span class="text-slate-500 truncate max-w-[130px]"><i class="fas fa-user-edit mr-1 text-[9px]"></i><?php echo htmlspecialchars($c['author']); ?></span>
                                            <?php if ($idx === 0): ?>
                                                <span class="text-[9px] bg-green-500/20 text-green-300 font-bold px-1.5 py-0.2 rounded shrink-0">HEAD</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Restore / Revert Dropdown -->
                        <div class="pt-1.5 border-t border-slate-800 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Rollback Tools</span>
                                <form method="POST" onsubmit="return confirm('Revert last?')"><input type="hidden" name="action" value="revert_last">
                                    <button type="submit" class="text-[10px] bg-orange-500/20 hover:bg-orange-500/30 text-orange-400 px-2 py-0.5 rounded border border-orange-500/30 font-bold">Revert Last</button>
                                </form>
                            </div>
                            <select id="commit_select" class="w-full input-field rounded-lg px-2 py-1.5 text-xs outline-none" onchange="handleCommitSelect()">
                                <option value="">Select commit to restore...</option>
                                <?php foreach ($commit_history as $c): ?>
                                    <option value="<?php echo $c['hash']; ?>" data-subject="<?php echo htmlspecialchars($c['subject']); ?>" data-body="<?php echo htmlspecialchars($c['body']); ?>" data-date="<?php echo $c['date']; ?>">
                                        [<?php echo substr($c['hash'], 0, 7); ?>] <?php echo $c['date']; ?> - <?php echo htmlspecialchars($c['subject']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="c_preview" class="hidden bg-slate-800/50 border border-slate-600/50 rounded-lg p-2 space-y-2">
                                <div class="text-xs text-slate-300">
                                    <div class="font-bold text-blue-400 mb-1" id="c_subject"></div>
                                    <div class="text-slate-400 text-[10px] mb-2" id="c_date"></div>
                                    <div class="text-slate-400 italic h-14 overflow-y-auto whitespace-pre-wrap leading-relaxed text-[11px]" id="c_desc"></div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="cancelRestore()" class="flex-1 bg-slate-700/50 hover:bg-slate-600/50 text-slate-300 py-1 rounded text-xs font-bold uppercase border border-slate-600/30">Cancel</button>
                                    <form method="POST" class="flex-1" id="restore_form">
                                        <input type="hidden" name="action" value="restore_commit">
                                        <input type="hidden" name="commit_hash" id="restore_hash">
                                        <button type="submit" onclick="return confirm('Are you sure you want to restore this commit?')" class="w-full bg-red-600/20 hover:bg-red-600/30 text-red-400 py-1 rounded text-xs font-bold uppercase border border-red-600/30">Confirm Restore</button>
                                    </form>
                                </div>
                            </div>
                        </div>
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
                                <?php if (!empty($local_hash)): ?>
                                    <div class="text-[11px] text-slate-500 font-mono ml-1 mb-1">
                                        HEAD Commit: <span class="text-blue-400 font-bold"><?php echo htmlspecialchars($local_hash); ?></span>
                                    </div>
                                <?php endif; ?>
                                <label class="text-xs font-bold text-blue-400 uppercase ml-1 flex justify-between items-start">
                                    <span>Commit Highlight</span>
                                    <span id="time-remaining" class="text-slate-500 lowercase font-normal text-right"></span>
                                </label>
                                <div class="text-xs text-slate-400 mb-1">Commit: <?php echo substr($commit_history[0]['hash'] ?? '', 0, 7); ?></div>
                                <textarea name="commit_msg" rows="2" placeholder="e.g., feat: add new feature" class="w-full input-field rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500 resize-none"><?php echo htmlspecialchars($last_commit_title); ?></textarea>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-bold text-blue-400 uppercase ml-1">Extended Description</label>
                                <textarea name="commit_desc" rows="8" placeholder="Provide more details about the changes..." class="w-full input-field rounded-lg px-3 py-2 text-sm outline-none focus:border-blue-500 resize-none"><?php echo htmlspecialchars($last_commit_desc); ?></textarea>
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
        const pktDate = <?php echo json_encode($pkt_date); ?>;
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
            if (el) {
                let html = "Last update: " + timeStr;
                if (pktDate) {
                    html += "<br><span class='text-[10px] text-slate-500 normal-case block mt-0.5'>" + pktDate + " (PKT)</span>";
                }
                el.innerHTML = html;
            }
        }
        setInterval(updateTime, 10000);
        updateTime();
        
        // Restore commit preview logic
        function handleCommitSelect() {
            const select = document.getElementById('commit_select');
            const preview = document.getElementById('c_preview');
            const selectedOption = select.options[select.selectedIndex];
            
            if (!selectedOption.value) {
                preview.classList.add('hidden');
                return;
            }
            
            const subject = selectedOption.getAttribute('data-subject');
            const body = selectedOption.getAttribute('data-body');
            const date = selectedOption.getAttribute('data-date');
            const hash = selectedOption.value;
            
            document.getElementById('c_subject').innerText = subject;
            document.getElementById('c_date').innerText = 'Date: ' + date + ' | Hash: ' + hash.substring(0, 7);
            document.getElementById('c_desc').innerText = body || 'No extended description.';
            document.getElementById('restore_hash').value = hash;
            
            preview.classList.remove('hidden');
        }
        
        function selectCommitForRestore(hash) {
            const select = document.getElementById('commit_select');
            if (select) {
                select.value = hash;
                handleCommitSelect();
            }
        }
        
        function cancelRestore() {
            document.getElementById('commit_select').value = '';
            document.getElementById('c_preview').classList.add('hidden');
        }
    </script>
</body>
</html>
