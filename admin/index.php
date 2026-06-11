<?php
require_once __DIR__ . '/includes/header.php';

// Fetch stats counts
try {
    // Posts count
    $postCount = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    // Portfolios count
    $portfolioCount = $pdo->query("SELECT COUNT(*) FROM portfolios")->fetchColumn();
    // Total messages & unread messages count
    $messageCount = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
    $unreadMessageCount = $pdo->query("SELECT COUNT(*) FROM messages WHERE is_read = 0")->fetchColumn();
    
    // Fetch 5 most recent unread/read messages
    $stmt = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");
    $recentMessages = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="max-w-7xl mx-auto space-y-8">
    <!-- Header Welcome Card -->
    <div class="bg-gradient-to-r from-brand-600 to-blue-500 rounded-3xl p-6 md:p-8 text-white shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div>
            <h2 class="text-2xl md:text-3xl font-extrabold tracking-tight">Selamat Datang, <?= sanitize($_SESSION['admin_username']) ?>!</h2>
            <p class="mt-2 text-brand-100 max-w-xl text-sm md:text-base">Kelola postingan blog, item portofolio, pesan kontak, dan pengaturan tampilan situs utama Anda dari panel ini.</p>
        </div>
        <div class="flex space-x-2">
            <a href="<?= BASE_URL ?>admin/posts.php" class="px-4 py-2.5 bg-white text-brand-700 font-semibold rounded-xl text-sm shadow-md hover:bg-brand-50 transition-colors transform hover:-translate-y-0.5 duration-150">Tulis Artikel</a>
            <a href="<?= BASE_URL ?>" target="_blank" class="px-4 py-2.5 bg-brand-700 text-white font-semibold rounded-xl text-sm border border-brand-500 hover:bg-brand-800 transition-colors transform hover:-translate-y-0.5 duration-150">Lihat Website</a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Post Stat -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl p-6 transition-all duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 012 2v6a2 2 0 01-2 2h-2m-4-12a1 1 0 00-1-1H5a1 1 0 00-1 1v3a1 1 0 001 1h6a1 1 0 001-1V6z"/></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Artikel</dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= $postCount ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                <a href="<?= BASE_URL ?>admin/posts.php" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700">Kelola artikel &rarr;</a>
            </div>
        </div>

        <!-- Portfolio Stat -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl p-6 transition-all duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Portofolio</dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= $portfolioCount ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                <a href="<?= BASE_URL ?>admin/portfolios.php" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700">Kelola project &rarr;</a>
            </div>
        </div>

        <!-- Messages Stat -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl p-6 transition-all duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pesan Unread</dt>
                        <dd class="flex items-baseline">
                            <div class="text-3xl font-extrabold text-gray-900 dark:text-white"><?= $unreadMessageCount ?></div>
                            <div class="ml-2 text-xs font-semibold text-gray-500">dari <?= $messageCount ?></div>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                <a href="<?= BASE_URL ?>admin/messages.php" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700">Buka inbox &rarr;</a>
            </div>
        </div>

        <!-- Quick Settings Stat -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl p-6 transition-all duration-200">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-yellow-50 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Web Settings</dt>
                        <dd class="flex items-baseline">
                            <div class="text-base font-extrabold text-gray-900 dark:text-white">Konfigurasi</div>
                        </dd>
                    </dl>
                </div>
            </div>
            <div class="mt-4 border-t border-gray-100 dark:border-gray-700/50 pt-4">
                <a href="<?= BASE_URL ?>admin/settings.php" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700">Ubah info web &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Recent Messages Table -->
    <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl overflow-hidden transition-all duration-200">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-base font-extrabold text-gray-900 dark:text-white">Pesan Masuk Terbaru</h3>
            <a href="<?= BASE_URL ?>admin/messages.php" class="text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700">Lihat semua &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <?php if (empty($recentMessages)): ?>
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Belum ada pesan masuk.
                </div>
            <?php else: ?>
                <table class="min-w-full divide-y divide-gray-150 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subjek</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-150 dark:divide-gray-700">
                        <?php foreach ($recentMessages as $msg): ?>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white"><?= sanitize($msg['name']) ?></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400"><?= sanitize($msg['email']) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 dark:text-white font-medium"><?= sanitize($msg['subject']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?= date('d M Y, H:i', strtotime($msg['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($msg['is_read'] == 0): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Belum Dibaca</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400">Sudah Dibaca</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
