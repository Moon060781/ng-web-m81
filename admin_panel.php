<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Center | Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .message-card {
            transition: all 0.2s ease;
        }
        .message-card.unread {
            border-left: 4px solid #5d4037;
            background-color: #fffaf0;
        }
        .selected-card {
            background-color: #f1f5f9 !important;
            border-color: #5d4037 !important;
        }
        #bulkActions {
            transition: all 0.3s ease;
            transform: translateY(100%);
        }
        #bulkActions.active {
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="index.html" class="text-slate-500 hover:text-slate-900 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-inbox text-amber-800"></i>
                    Message Center
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold px-3 py-1 bg-slate-100 rounded-full text-slate-600">
                    Logged in as Admin
                </span>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8 pb-32">
        <!-- Message Controls -->
        <div class="flex flex-wrap items-center justify-between mb-8 gap-4">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer bg-white px-4 py-2 rounded-xl border border-slate-200 hover:border-amber-800 transition">
                    <input type="checkbox" id="selectAll" class="w-4 h-4 accent-amber-800">
                    <span class="text-sm font-bold">Select All</span>
                </label>
                <div class="text-slate-500 text-sm font-medium">
                    Total: <span id="totalCount">3</span> messages
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="refreshMessages()" class="p-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 transition">
                    <i class="fa-solid fa-rotate"></i>
                </button>
            </div>
        </div>

        <!-- Messages Container -->
        <div id="messagesList" class="space-y-4">
            <!-- Mock Message 1 -->
            <div class="message-card unread bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative group" data-id="1">
                <div class="flex gap-4">
                    <div class="pt-1">
                        <input type="checkbox" class="msg-checkbox w-5 h-5 accent-amber-800 cursor-pointer" onchange="updateSelection()">
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap justify-between items-start gap-2 mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <span class="name">Athar Hussain</span>
                                    <span class="bg-amber-100 text-amber-800 text-[10px] px-2 py-0.5 rounded uppercase">Unread</span>
                                </h3>
                                <div class="text-sm text-slate-500 mt-1 flex flex-wrap gap-x-4">
                                    <span class="email"><i class="fa-solid fa-envelope mr-1"></i> athar@example.com</span>
                                    <span class="phone"><i class="fa-solid fa-phone mr-1"></i> +92 332 3320369</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 block mb-2">Jan 25, 2026 - 10:30 AM</span>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                    <button onclick="copyInfo(this)" title="Copy Contact Info" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white transition">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                    <button onclick="markRead(1)" title="Mark as Read" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-green-600 hover:text-white transition">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                    <button onclick="deleteMsg(1)" title="Delete Message" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-red-600 hover:text-white transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-slate-50 pt-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 subject">Subject: New Portfolio Inquiry</h4>
                            <p class="text-slate-700 leading-relaxed message-text">I am interested in building a new custom PHP website for my business. Please let me know your availability.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mock Message 2 -->
            <div class="message-card bg-white p-6 rounded-2xl border border-slate-200 shadow-sm relative group" data-id="2">
                <div class="flex gap-4">
                    <div class="pt-1">
                        <input type="checkbox" class="msg-checkbox w-5 h-5 accent-amber-800 cursor-pointer" onchange="updateSelection()">
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap justify-between items-start gap-2 mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <span class="name">Noorgee Partner</span>
                                </h3>
                                <div class="text-sm text-slate-500 mt-1 flex flex-wrap gap-x-4">
                                    <span class="email"><i class="fa-solid fa-envelope mr-1"></i> partner@noorgee.pk</span>
                                    <span class="phone"><i class="fa-solid fa-phone mr-1"></i> +971 50 1234567</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-400 block mb-2">Jan 24, 2026 - 02:15 PM</span>
                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition">
                                    <button onclick="copyInfo(this)" title="Copy Contact Info" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-blue-600 hover:text-white transition">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                    <button onclick="deleteMsg(2)" title="Delete Message" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-red-600 hover:text-white transition">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-slate-50 pt-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 subject">Subject: Server Migration</h4>
                            <p class="text-slate-700 leading-relaxed message-text">We need to discuss the upcoming migration for the UAE regional servers.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bulk Actions Floating Bar -->
    <div id="bulkActions" class="fixed bottom-0 left-0 right-0 bg-slate-900 text-white p-4 shadow-[0_-10px_30px_rgba(0,0,0,0.3)] z-50">
        <div class="container mx-auto flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="bg-amber-800 text-white text-xs font-bold px-3 py-1 rounded-full" id="selectedCount">0 Selected</span>
                <p class="hidden md:block text-slate-400 text-sm">Choose an action for the selected messages</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="bulkMarkRead()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-green-600 transition text-sm font-bold">
                    <i class="fa-solid fa-check-double mr-2"></i> Mark Read
                </a>
                <button onclick="bulkDelete()" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-red-600 transition text-sm font-bold">
                    <i class="fa-solid fa-trash-can mr-2"></i> Delete All
                </a>
                <button onclick="clearSelection()" class="px-4 py-2 rounded-xl text-slate-400 hover:text-white transition text-sm font-bold">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="fixed top-20 right-4 px-6 py-3 bg-slate-900 text-white rounded-xl shadow-2xl translate-x-[200%] transition-transform z-[60] flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-green-400"></i>
        <span id="toastMessage">Action successful</span>
    </div>

    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.msg-checkbox').forEach(cb => {
                cb.checked = isChecked;
                toggleCardHighlight(cb);
            });
            updateSelection();
        });

        function toggleCardHighlight(checkbox) {
            const card = checkbox.closest('.message-card');
            if (checkbox.checked) {
                card.classList.add('selected-card');
            } else {
                card.classList.remove('selected-card');
            }
        }

        function updateSelection() {
            const checkboxes = document.querySelectorAll('.msg-checkbox');
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            const selectAllCb = document.getElementById('selectAll');
            
            // Highlight cards
            checkboxes.forEach(cb => toggleCardHighlight(cb));

            // Update Select All state
            selectAllCb.checked = selected.length === checkboxes.length && checkboxes.length > 0;
            selectAllCb.indeterminate = selected.length > 0 && selected.length < checkboxes.length;

            // Update bulk actions bar
            const bar = document.getElementById('bulkActions');
            const countLabel = document.getElementById('selectedCount');
            
            if (selected.length > 0) {
                bar.classList.add('active');
                countLabel.innerText = `${selected.length} Selected`;
            } else {
                bar.classList.remove('active');
            }
        }

        function clearSelection() {
            document.querySelectorAll('.msg-checkbox').forEach(cb => cb.checked = false);
            document.getElementById('selectAll').checked = false;
            updateSelection();
        }

        // Copy Functionality
        function copyInfo(btn) {
            const card = btn.closest('.message-card');
            const name = card.querySelector('.name').innerText;
            const email = card.querySelector('.email').innerText.replace(/\s+/g, ' ').trim();
            const phone = card.querySelector('.phone').innerText.replace(/\s+/g, ' ').trim();
            const subject = card.querySelector('.subject').innerText;
            const message = card.querySelector('.message-text').innerText;

            const textToCopy = `Name: ${name}\nEmail: ${email}\nPhone: ${phone}\nSubject: ${subject}\nMessage: ${message}`;

            const textArea = document.createElement("textarea");
            textArea.value = textToCopy;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                showToast("Contact information copied to clipboard!");
            } catch (err) {
                showToast("Failed to copy text.", true);
            }
            document.body.removeChild(textArea);
        }

        // Action Handlers (Mock logic for frontend)
        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            const msgEl = document.getElementById('toastMessage');
            msgEl.innerText = msg;
            toast.classList.remove('translate-x-[200%]');
            setTimeout(() => toast.classList.add('translate-x-[200%]'), 3000);
        }

        function markRead(id) {
            showToast(`Message #${id} marked as read`);
            // In a real app, this would trigger an AJAX call to mark_read.php
        }

        function deleteMsg(id) {
            if(confirm('Are you sure you want to delete this message?')) {
                const card = document.querySelector(`.message-card[data-id="${id}"]`);
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                    updateSelection();
                    showToast("Message deleted successfully");
                }, 300);
            }
        }

        function bulkMarkRead() {
            const ids = Array.from(document.querySelectorAll('.msg-checkbox:checked'))
                            .map(cb => cb.closest('.message-card').dataset.id);
            showToast(`Marked ${ids.length} messages as read`);
            clearSelection();
        }

        function bulkDelete() {
            const selected = Array.from(document.querySelectorAll('.msg-checkbox:checked'));
            if(confirm(`Delete ${selected.length} messages permanently?`)) {
                selected.forEach(cb => cb.closest('.message-card').remove());
                updateSelection();
                showToast(`Deleted ${selected.length} messages`);
            }
        }

        function refreshMessages() {
            location.reload();
        }
    </script>
</body>
</html>
