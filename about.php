<?php
$pageTitle = "About Me";
require_once __DIR__ . '/includes/header.php';

// Fetch about profile settings
$aboutTitle = get_setting('about_title', 'About Me');
$aboutText = get_setting('about_text', '');
$aboutImage = get_setting('about_image', '');

// Contact details for additional cards
$contactEmail = get_setting('contact_email');
$contactPhone = get_setting('contact_phone');
$contactAddress = get_setting('contact_address');
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-12">
    <!-- Header Title -->
    <div class="text-center space-y-4">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white"><?= sanitize($aboutTitle) ?></h1>
        <div class="w-16 h-1 bg-brand-500 mx-auto rounded-full"></div>
    </div>

    <!-- Main Content Layout -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
        <!-- Sidebar Info (Left/Top) -->
        <div class="md:col-span-1 space-y-6">
            <!-- Profile Photo -->
            <div class="w-full h-72 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-md bg-gray-50 dark:bg-gray-900 flex items-center justify-center">
                <?php if (!empty($aboutImage) && file_exists('assets/images/' . $aboutImage)): ?>
                    <img src="<?= BASE_URL . 'assets/images/' . $aboutImage ?>" alt="Profile Photo" class="w-full h-full object-cover">
                <?php else: ?>
                    <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <?php endif; ?>
            </div>

            <!-- Mini details card -->
            <div class="bg-gray-50 dark:bg-gray-900 border border-gray-150 dark:border-gray-800 rounded-2xl p-6 space-y-4 shadow-sm">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Kontak & Lokasi</h3>
                <div class="space-y-3 text-sm text-gray-650 dark:text-gray-400">
                    <?php if (!empty($contactEmail)): ?>
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="truncate"><?= sanitize($contactEmail) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contactPhone)): ?>
                        <div class="flex items-center space-x-3">
                            <svg class="w-4 h-4 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span><?= sanitize($contactPhone) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contactAddress)): ?>
                        <div class="flex items-start space-x-3">
                            <svg class="w-4 h-4 text-brand-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span><?= sanitize($contactAddress) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Full Biography Text (Right/Bottom) -->
        <div class="md:col-span-2 bg-white dark:bg-gray-950 p-2 md:p-4 space-y-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Tentang Saya</h2>
            <div class="text-gray-600 dark:text-gray-400 leading-relaxed space-y-4 whitespace-pre-wrap">
                <?= sanitize($aboutText) ?>
            </div>
            <div class="pt-6">
                <a href="<?= BASE_URL ?>portfolio.php" class="inline-flex items-center px-5 py-3 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition-colors transform hover:-translate-y-0.5 duration-150">
                    Lihat Portofolio Saya
                </a>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
