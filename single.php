<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$post = null;

if (!empty($slug)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'published'");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
    } catch (PDOException $e) {
        $post = null;
    }
}

$pageTitle = $post ? $post['title'] : "Artikel Tidak Ditemukan";
if ($post) {
    $pageDesc = substr(strip_tags($post['content']), 0, 160);
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
    <?php if (!$post): ?>
        <div class="text-center py-20 space-y-6">
            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Artikel Tidak Ditemukan</h2>
            <p class="text-gray-500 dark:text-gray-400">Maaf, artikel yang Anda cari tidak ada atau telah dihapus.</p>
            <div class="pt-4">
                <a href="blog.php" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-xl text-sm shadow-md transition-colors inline-block">
                    Kembali ke Blog
                </a>
            </div>
        </div>
    <?php else: ?>
        <article class="space-y-8">
            <!-- Back Button & Meta -->
            <div class="space-y-4">
                <a href="blog.php" class="text-sm font-semibold text-gray-500 hover:text-brand-600 transition-colors inline-flex items-center">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Blog
                </a>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white leading-tight">
                    <?= sanitize($post['title']) ?>
                </h1>
                <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400">
                    <span>Oleh Admin</span>
                    <span>&bull;</span>
                    <span><?= date('d M Y', strtotime($post['created_at'])) ?></span>
                </div>
            </div>

            <!-- Banner Image -->
            <?php if (!empty($post['image']) && file_exists('assets/images/' . $post['image'])): ?>
                <div class="w-full h-[350px] sm:h-[450px] rounded-3xl overflow-hidden shadow-lg border border-gray-100 dark:border-gray-900">
                    <img src="<?= BASE_URL . 'assets/images/' . $post['image'] ?>" alt="<?= sanitize($post['title']) ?>" class="w-full h-full object-cover">
                </div>
            <?php endif; ?>

            <!-- Article Body -->
            <div class="text-gray-700 dark:text-gray-300 leading-relaxed text-base sm:text-lg whitespace-pre-wrap space-y-4">
                <?= nl2br(sanitize($post['content'])) ?>
            </div>
        </article>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
