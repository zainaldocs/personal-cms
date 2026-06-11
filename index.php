<?php
require_once __DIR__ . '/includes/header.php';

// Fetch hero and profiles settings
$heroTitle = get_setting('hero_title', 'Hi, I am Zainal Arifin');
$heroSubtitle = get_setting('hero_subtitle', 'Fullstack Developer');
$aboutText = get_setting('about_text', '');
$aboutImage = get_setting('about_image', '');

// Fetch 3 latest portfolios
try {
    $portfolioStmt = $pdo->query("SELECT * FROM portfolios ORDER BY order_index ASC, created_at DESC LIMIT 3");
    $latestPortfolios = $portfolioStmt->fetchAll();
} catch (PDOException $e) {
    $latestPortfolios = [];
}

// Fetch 3 latest published blog posts
try {
    $blogStmt = $pdo->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
    $latestPosts = $blogStmt->fetchAll();
} catch (PDOException $e) {
    $latestPosts = [];
}
?>

<!-- Hero Section -->
<section class="relative bg-white dark:bg-gray-950 py-20 md:py-32 overflow-hidden transition-colors duration-200">
    <!-- Subtle Background Accents -->
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-7xl h-full pointer-events-none opacity-40">
        <div class="absolute top-[-10%] left-[5%] w-[300px] h-[300px] rounded-full bg-brand-200 dark:bg-brand-900/10 blur-[80px]"></div>
        <div class="absolute bottom-[10%] right-[5%] w-[250px] h-[250px] rounded-full bg-blue-100 dark:bg-blue-900/10 blur-[80px]"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center space-y-6">
            <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-brand-50 text-brand-600 dark:bg-brand-900/30 dark:text-brand-400 border border-brand-100 dark:border-brand-900/50">
                Welcome to my digital space
            </span>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white leading-tight">
                <?= sanitize($heroTitle) ?>
            </h1>
            <p class="max-w-2xl mx-auto text-lg md:text-xl text-gray-500 dark:text-gray-400">
                <?= sanitize($heroSubtitle) ?>
            </p>
            <div class="pt-4 flex flex-wrap justify-center gap-4">
                <a href="<?= BASE_URL ?>portfolio.php" class="px-6 py-3 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition-all duration-150 transform hover:-translate-y-0.5">
                    Lihat Portofolio
                </a>
                <a href="<?= BASE_URL ?>contact.php" class="px-6 py-3 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-250 bg-white dark:bg-gray-900 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-850 transition-all duration-150 transform hover:-translate-y-0.5">
                    Hubungi Saya
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Profile Summary Section -->
<section class="bg-gray-50 dark:bg-gray-900/30 py-20 border-t border-b border-gray-100 dark:border-gray-900 transition-colors duration-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Profile Image -->
            <div class="flex justify-center">
                <div class="relative w-64 h-64 md:w-80 md:h-80 rounded-3xl overflow-hidden border-4 border-white dark:border-gray-800 shadow-xl bg-white dark:bg-gray-800 flex items-center justify-center">
                    <?php if (!empty($aboutImage) && file_exists('assets/images/' . $aboutImage)): ?>
                        <img src="<?= BASE_URL . 'assets/images/' . $aboutImage ?>" alt="Zainal Arifin" class="w-full h-full object-cover">
                    <?php else: ?>
                        <svg class="w-32 h-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <?php endif; ?>
                </div>
            </div>

            <!-- About Content -->
            <div class="space-y-6">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">About Me</h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    <?= nl2br(sanitize(substr($aboutText, 0, 350))) ?><?= strlen($aboutText) > 350 ? '...' : '' ?>
                </p>
                <div class="pt-2">
                    <a href="<?= BASE_URL ?>about.php" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300 inline-flex items-center">
                        Selengkapnya &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Portfolios Section -->
<section class="bg-white dark:bg-gray-950 py-20 transition-colors duration-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Portofolio Terbaru</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Beberapa project terbaru yang telah saya selesaikan.</p>
            </div>
            <a href="<?= BASE_URL ?>portfolio.php" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:underline">Semua Project &rarr;</a>
        </div>

        <?php if (empty($latestPortfolios)): ?>
            <div class="text-center py-12 text-gray-500">Belum ada portofolio.</div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($latestPortfolios as $p): ?>
                    <div class="group bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between overflow-hidden">
                        <div>
                            <div class="h-48 bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border-b border-gray-150 dark:border-gray-800">
                                <?php if (!empty($p['image']) && file_exists('assets/images/' . $p['image'])): ?>
                                    <img src="<?= BASE_URL . 'assets/images/' . $p['image'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white"><?= sanitize($p['title']) ?></h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
                                    <?= sanitize($p['description']) ?>
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2">
                            <?php if (!empty($p['project_url'])): ?>
                                <a href="<?= sanitize($p['project_url']) ?>" target="_blank" class="inline-flex items-center text-sm font-semibold text-brand-600 dark:text-brand-400 hover:text-brand-700">
                                    View Project
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Latest Blog Section -->
<section class="bg-gray-50 dark:bg-gray-900/30 py-20 border-t border-gray-100 dark:border-gray-900 transition-colors duration-200">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Tulisan Terbaru</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Artikel, tips, dan pemikiran saya seputar teknologi.</p>
            </div>
            <a href="<?= BASE_URL ?>blog.php" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:underline">Semua Artikel &rarr;</a>
        </div>

        <?php if (empty($latestPosts)): ?>
            <div class="text-center py-12 text-gray-500">Belum ada tulisan blog.</div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($latestPosts as $post): ?>
                    <div class="group bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="h-44 bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border-b border-gray-150 dark:border-gray-800">
                                <?php if (!empty($post['image']) && file_exists('assets/images/' . $post['image'])): ?>
                                    <img src="<?= BASE_URL . 'assets/images/' . $post['image'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="p-6">
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-wider block mb-1">
                                    <?= date('d M Y', strtotime($post['created_at'])) ?>
                                </span>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">
                                    <a href="<?= BASE_URL ?>single.php?slug=<?= sanitize($post['slug']) ?>" class="hover:text-brand-600 transition-colors">
                                        <?= sanitize($post['title']) ?>
                                    </a>
                                </h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
                                    <?= sanitize(strip_tags($post['content'])) ?>
                                </p>
                            </div>
                        </div>
                        <div class="px-6 pb-6 pt-2">
                            <a href="<?= BASE_URL ?>single.php?slug=<?= sanitize($post['slug']) ?>" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700">
                                Selengkapnya &rarr;
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
