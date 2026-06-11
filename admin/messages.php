<?php
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'view';
$msgId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Delete Action via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    $delId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    
    if ($delId > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
            $stmt->execute([$delId]);
            $success = 'Message deleted successfully.';
            $msgId = 0; // reset selected message
        } catch (PDOException $e) {
            $error = 'Failed to delete message: ' . $e->getMessage();
        }
    }
}

// Mark message as read if selected
$selectedMessage = null;
if ($msgId > 0) {
    try {
        // Fetch message details
        $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
        $stmt->execute([$msgId]);
        $selectedMessage = $stmt->fetch();
        
        if ($selectedMessage) {
            // Update read status if it is unread
            if ($selectedMessage['is_read'] == 0) {
                $updateStmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
                $updateStmt->execute([$msgId]);
                // update local variable for immediate UI response
                $selectedMessage['is_read'] = 1;
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Fetch all messages (newest first)
$messages = [];
try {
    $messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    $error = 'Failed to fetch messages: ' . $e->getMessage();
}
?>

<div class="max-w-6xl mx-auto space-y-6 h-full flex flex-col">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-5">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Inbox Messages</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola dan baca pesan yang dikirim oleh pengunjung web Anda.</p>
        </div>
    </div>

    <!-- Feedback Alerts -->
    <?php if (!empty($success)): ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-xl shadow-sm">
            <div class="flex">
                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="ml-3 text-sm text-green-700 font-medium"><?= sanitize($success) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl shadow-sm">
            <div class="flex">
                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="ml-3 text-sm text-red-700 font-medium"><?= sanitize($error) ?></p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Master-Detail Inbox Wrapper -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 bg-white dark:bg-gray-800 border border-gray-150 dark:border-gray-800 rounded-2xl shadow-md overflow-hidden min-h-[500px]">
        <!-- Message List (Left Side) -->
        <div class="md:col-span-1 border-r border-gray-150 dark:border-gray-700 overflow-y-auto max-h-[600px] no-scrollbar">
            <div class="p-4 bg-gray-50 dark:bg-gray-700/50 border-b border-gray-150 dark:border-gray-700">
                <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Daftar Pesan</span>
            </div>
            
            <?php if (empty($messages)): ?>
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    Belum ada pesan masuk.
                </div>
            <?php else: ?>
                <div class="divide-y divide-gray-100 dark:divide-gray-750">
                    <?php foreach ($messages as $msg): ?>
                        <?php 
                            $isCurrent = $msg['id'] == $msgId;
                            $bgClass = $isCurrent ? 'bg-brand-50/70 dark:bg-gray-700/60 border-l-4 border-brand-500' : 'hover:bg-gray-50/50 dark:hover:bg-gray-700/30';
                            $unreadClass = $msg['is_read'] == 0 ? 'font-bold text-gray-950 dark:text-white' : 'text-gray-600 dark:text-gray-300';
                        ?>
                        <a href="messages.php?id=<?= $msg['id'] ?>" class="block p-4 transition-colors <?= $bgClass ?>">
                            <div class="flex justify-between items-start">
                                <span class="text-sm truncate max-w-[120px] <?= $unreadClass ?>"><?= sanitize($msg['name']) ?></span>
                                <span class="text-xxs text-gray-400 dark:text-gray-500 whitespace-nowrap"><?= date('d M', strtotime($msg['created_at'])) ?></span>
                            </div>
                            <div class="text-sm font-semibold truncate mt-1 text-gray-900 dark:text-white"><?= sanitize($msg['subject']) ?></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5"><?= sanitize($msg['message']) ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Message Detail (Right Side) -->
        <div class="md:col-span-2 p-6 flex flex-col justify-between max-h-[600px] overflow-y-auto">
            <?php if ($selectedMessage): ?>
                <div class="space-y-6">
                    <!-- Sender Details -->
                    <div class="flex justify-between items-start border-b border-gray-150 dark:border-gray-700 pb-5">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= sanitize($selectedMessage['subject']) ?></h3>
                            <div class="mt-2 flex items-center text-sm text-gray-600 dark:text-gray-400 space-x-2">
                                <span class="font-semibold text-gray-800 dark:text-gray-200"><?= sanitize($selectedMessage['name']) ?></span>
                                <span>&lt;<?= sanitize($selectedMessage['email']) ?>&gt;</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400 dark:text-gray-500 block"><?= date('d M Y, H:i', strtotime($selectedMessage['created_at'])) ?></span>
                            <form method="POST" action="messages.php" class="inline-block mt-2" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $selectedMessage['id'] ?>">
                                <button type="submit" class="inline-flex items-center text-xs font-semibold text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 bg-transparent border-none cursor-pointer">
                                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus Pesan
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Message Body -->
                    <div class="text-sm leading-relaxed text-gray-700 dark:text-gray-200 bg-gray-50 dark:bg-gray-900/30 p-5 rounded-2xl border border-gray-100 dark:border-gray-850 min-h-[200px] whitespace-pre-wrap">
                        <?= sanitize($selectedMessage['message']) ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex-1 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 p-12">
                    <svg class="h-16 w-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l8-5.333a2 2 0 012.22 0l8 5.333A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-2.25-1.5a2 2 0 00-2.22 0l-2.25 1.5"/></svg>
                    Pilih pesan dari daftar di sebelah kiri untuk membaca detail.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
