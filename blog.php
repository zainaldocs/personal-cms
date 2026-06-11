<?php
$pageTitle = "Blog & Artikel";
require_once __DIR__ . '/includes/header.php';

// Pagination setup
$postsPerPage = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $postsPerPage;

$posts = [];
$totalPosts = 0;
$totalPages = 0;

try {
    // Get total posts count for pagination calculations
    $countStmt = $pdo->query("SELECT COUNT(*) FROM posts WHERE status = 'published'");
    $totalPosts = $countStmt->fetchColumn();
    $totalPages = ceil($totalPosts / $postsPerPage);

    // Fetch posts for the current page
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bindValue(1, $postsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    // Fallback/log
    $posts = [];
}
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-12">
    <!-- Header Title -->
    <div class="text-center space-y-4 max-w-2xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">Blog & Artikel</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base leading-relaxed">Berbagi pengetahuan, tutorial, dan pemikiran seputar dunia pemrograman dan pengembangan teknologi.</p>
        <div class="w-16 h-1 bg-brand-500 mx-auto rounded-full"></div>
    </div>

    <!-- Blog Grid -->
    <?php if (empty($posts)): ?>
        <div class="text-center py-20 text-gray-500 dark:text-gray-400">
            <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-700 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 012 2v6a2 2 0 01-2 2h-2m-4-12a1 1 0 00-1-1H5a1 1 0 00-1 1v3a1 1 0 001 1h6a1 1 0 001-1V6z"/></svg>
            Belum ada tulisan artikel blog saat ini.
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($posts as $post): ?>
                <div class="group bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Thumbnail Image -->
                        <div class="h-48 bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden border-b border-gray-150 dark:border-gray-800">
                            <?php if (!empty($post['image']) && file_exists('assets/images/' . $post['image'])): ?>
                                <img src="<?= BASE_URL . 'assets/images/' . $post['image'] ?>" alt="<?= sanitize($post['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <svg class="h-12 w-12 text-gray-300 dark:text-gray-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <?php endif; ?>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6 space-y-3">
                            <span class="text-xs text-gray-400 dark:text-gray-500 font-semibold uppercase tracking-wider block">
                                <?= date('d M Y', strtotime($post['created_at'])) ?>
                            </span>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug group-hover:text-brand-600 transition-colors">
                                <a href="<?= BASE_URL ?>single.php?slug=<?= sanitize($post['slug']) ?>">
                                    <?= sanitize($post['title']) ?>
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
                                <?= sanitize(strip_tags($post['content'])) ?>
                            </p>
                        </div>
                    </div>

                    <!-- Card Action -->
                    <div class="px-6 pb-6 pt-2">
                        <a href="<?= BASE_URL ?>single.php?slug=<?= sanitize($post['slug']) ?>" class="text-sm font-bold text-brand-600 dark:text-brand-400 hover:text-brand-700 transition-colors">
                            Baca Selengkapnya &rarr;
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination Controls -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center items-center space-x-2 pt-8">
                <!-- Previous Page -->
                <?php if ($page > 1): ?>
                    <a href="blog.php?page=<?= $page - 1 ?>" class="px-4 py-2 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-250 bg-white dark:bg-gray-900 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                        &larr; Prev
                    </a>
                <?php endif; ?>

                <!-- Page numbers -->
                <span class="text-sm text-gray-500">Page <?= $page ?> of <?= $totalPages ?></span>

                <!-- Next Page -->
                <?php if ($page < $totalPages): ?>
                    <a href="blog.php?page=<?= $page + 1 ?>" class="px-4 py-2 border border-gray-200 dark:border-gray-800 rounded-xl text-sm font-semibold text-gray-700 dark:text-gray-250 bg-white dark:bg-gray-900 shadow-sm hover:bg-gray-50 dark:hover:bg-gray-800">
                        Next &rarr;
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
