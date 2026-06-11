<?php
$pageTitle = "Portofolio";
require_once __DIR__ . '/includes/header.php';

// Fetch all portfolios
try {
    $stmt = $pdo->query("SELECT * FROM portfolios ORDER BY order_index ASC, created_at DESC");
    $portfolios = $stmt->fetchAll();
} catch (PDOException $e) {
    $portfolios = [];
}
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-12">
    <!-- Header Title -->
    <div class="text-center space-y-4 max-w-2xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">Portofolio Saya</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base leading-relaxed">Kumpulan proyek, eksperimen, dan karya profesional yang telah saya bangun selama perjalanan karir saya.</p>
        <div class="w-16 h-1 bg-brand-500 mx-auto rounded-full"></div>
    </div>

    <!-- Portfolio Grid -->
    <?php if (empty($portfolios)): ?>
        <div class="text-center py-20 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            Belum ada proyek portofolio yang ditampilkan.
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($portfolios as $p): ?>
                <div class="group bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">
                    <div>
                        <!-- Project Image Banner -->
                        <div class="h-52 bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border-b border-gray-150 dark:border-gray-800">
                            <?php if (!empty($p['image']) && file_exists('assets/images/' . $p['image'])): ?>
                                <img src="<?= BASE_URL . 'assets/images/' . $p['image'] ?>" alt="<?= sanitize($p['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <svg class="h-12 w-12 text-gray-300 dark:text-gray-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <?php endif; ?>
                        </div>

                        <!-- Project content -->
                        <div class="p-6 space-y-3">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white leading-snug group-hover:text-brand-600 transition-colors">
                                <?= sanitize($p['title']) ?>
                            </h3>
                            <div class="text-sm text-gray-600 dark:text-gray-450 leading-relaxed whitespace-pre-wrap">
                                <?= sanitize($p['description']) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Project footer with link -->
                    <div class="px-6 pb-6 pt-2">
                        <?php if (!empty($p['project_url'])): ?>
                            <a href="<?= sanitize($p['project_url']) ?>" target="_blank" class="inline-flex items-center text-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 transition-colors">
                                Kunjungi Project
                                <svg class="ml-1.5 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
