<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

// Helper to determine active class for navigation
$currentPage = basename($_SERVER['PHP_SELF']);
function isFrontendActive($pageNames, $currentPage) {
    $pages = is_array($pageNames) ? $pageNames : [$pageNames];
    return in_array($currentPage, $pages) ? 'text-brand-600 dark:text-brand-400 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:text-brand-600 dark:hover:text-brand-400 font-medium';
}

$siteName = get_setting('site_name', 'Personal CMS');
$metaTitle = isset($pageTitle) ? $pageTitle . " - " . $siteName : $siteName;
$metaDesc = isset($pageDesc) ? $pageDesc : get_setting('hero_subtitle', 'Personal CMS');
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($metaTitle) ?></title>
    <meta name="description" content="<?= sanitize($metaDesc) ?>">
    <!-- Tailwind Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0f7ff',
                            100: '#e0effe',
                            200: '#bae0fd',
                            300: '#7cc8fc',
                            400: '#38acf8',
                            500: '#0ea0e6',
                            600: '#0280c7',
                            700: '#0366a1',
                            800: '#075685',
                            900: '#0c486e',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-white dark:bg-gray-950 text-gray-900 dark:text-gray-150 min-h-full flex flex-col transition-colors duration-200">
    
    <!-- Navbar Header -->
    <header class="sticky top-0 z-50 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-900">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="<?= BASE_URL ?>index.php" class="text-xl font-bold bg-gradient-to-r from-brand-600 to-blue-400 bg-clip-text text-transparent tracking-tight">
                        <?= sanitize($siteName) ?>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="<?= BASE_URL ?>index.php" class="text-sm transition-colors duration-150 <?= isFrontendActive('index.php', $currentPage) ?>">Beranda</a>
                    <a href="<?= BASE_URL ?>about.php" class="text-sm transition-colors duration-150 <?= isFrontendActive('about.php', $currentPage) ?>">About</a>
                    <a href="<?= BASE_URL ?>portfolio.php" class="text-sm transition-colors duration-150 <?= isFrontendActive('portfolio.php', $currentPage) ?>">Portofolio</a>
                    <a href="<?= BASE_URL ?>blog.php" class="text-sm transition-colors duration-150 <?= isFrontendActive(['blog.php', 'single.php'], $currentPage) ?>">Blog</a>
                    <a href="<?= BASE_URL ?>contact.php" class="text-sm transition-colors duration-150 <?= isFrontendActive('contact.php', $currentPage) ?>">Kontak</a>
                    
                    <!-- Theme Toggle -->
                    <button id="themeToggleBtn" class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition-colors">
                        <svg id="sunIconFront" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                        <svg id="moonIconFront" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                </nav>

                <!-- Mobile Menu and Theme Toggle -->
                <div class="flex items-center md:hidden space-x-2">
                    <button id="mobileThemeToggleBtn" class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition-colors">
                        <svg id="sunIconFrontMobile" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                        <svg id="moonIconFrontMobile" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    </button>
                    <button id="mobileNavbarBtn" class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu Overlay -->
        <div id="mobileNavbarMenu" class="md:hidden hidden bg-white dark:bg-gray-950 border-b border-gray-100 dark:border-gray-900 px-4 pt-2 pb-4 space-y-1">
            <a href="<?= BASE_URL ?>index.php" class="block px-3 py-2 rounded-lg text-base font-medium transition-colors <?= isFrontendActive('index.php', $currentPage) ?>">Beranda</a>
            <a href="<?= BASE_URL ?>about.php" class="block px-3 py-2 rounded-lg text-base font-medium transition-colors <?= isFrontendActive('about.php', $currentPage) ?>">About</a>
            <a href="<?= BASE_URL ?>portfolio.php" class="block px-3 py-2 rounded-lg text-base font-medium transition-colors <?= isFrontendActive('portfolio.php', $currentPage) ?>">Portofolio</a>
            <a href="<?= BASE_URL ?>blog.php" class="block px-3 py-2 rounded-lg text-base font-medium transition-colors <?= isFrontendActive(['blog.php', 'single.php'], $currentPage) ?>">Blog</a>
            <a href="<?= BASE_URL ?>contact.php" class="block px-3 py-2 rounded-lg text-base font-medium transition-colors <?= isFrontendActive('contact.php', $currentPage) ?>">Kontak</a>
        </div>
    </header>

    <!-- Main Wrapper -->
    <main class="flex-grow">
