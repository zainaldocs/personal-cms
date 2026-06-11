<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Force login check for all admin files including this header
check_login();

// Helper to determine active class
$currentPage = basename($_SERVER['PHP_SELF']);
function isActive($pageName, $currentPage) {
    return $pageName === $currentPage ? 'bg-brand-50 text-brand-600 dark:bg-gray-700 dark:text-brand-400 font-semibold' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-gray-900 dark:hover:text-white';
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?= sanitize(get_setting('site_name', 'Personal CMS')) ?></title>
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
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
    <div class="flex h-full overflow-hidden">
        <!-- Sidebar for Desktop -->
        <aside class="hidden md:flex md:flex-shrink-0">
            <div class="flex flex-col w-64 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800">
                <div class="flex items-center h-16 flex-shrink-0 px-6 border-b border-gray-150 dark:border-gray-800">
                    <span class="text-xl font-bold bg-gradient-to-r from-brand-600 to-blue-400 bg-clip-text text-transparent">CMS Admin</span>
                </div>
                <div class="flex-1 flex flex-col overflow-y-auto no-scrollbar pt-5 pb-4">
                    <nav class="flex-1 px-4 space-y-1">
                        <!-- Dashboard -->
                        <a href="<?= BASE_URL ?>admin/index.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('index.php', $currentPage) ?>">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"/></svg>
                            Dashboard
                        </a>
                        <!-- Blog Posts -->
                        <a href="<?= BASE_URL ?>admin/posts.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('posts.php', $currentPage) ?>">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 012 2v6a2 2 0 01-2 2h-2m-4-12a1 1 0 00-1-1H5a1 1 0 00-1 1v3a1 1 0 001 1h6a1 1 0 001-1V6z"/></svg>
                            Blog Posts
                        </a>
                        <!-- Portfolios -->
                        <a href="<?= BASE_URL ?>admin/portfolios.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('portfolios.php', $currentPage) ?>">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Portfolios
                        </a>
                        <!-- Messages -->
                        <a href="<?= BASE_URL ?>admin/messages.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('messages.php', $currentPage) ?>">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Inbox Messages
                        </a>
                        <!-- Settings -->
                        <a href="<?= BASE_URL ?>admin/settings.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('settings.php', $currentPage) ?>">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Web Settings
                        </a>
                        <!-- Profile -->
                        <a href="<?= BASE_URL ?>admin/profile.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('profile.php', $currentPage) ?>">
                            <svg class="mr-3 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Admin
                        </a>
                    </nav>
                </div>
                <div class="flex-shrink-0 flex border-t border-gray-200 dark:border-gray-800 p-4">
                    <div class="flex items-center w-full justify-between">
                        <div class="flex items-center">
                            <div class="w-9 h-9 rounded-full bg-brand-500 flex items-center justify-center text-white font-bold">
                                <?= strtoupper(substr($_SESSION['admin_username'], 0, 1)) ?>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-200"><?= sanitize($_SESSION['admin_username']) ?></p>
                                <a href="<?= BASE_URL ?>" target="_blank" class="text-xs text-brand-500 hover:underline">View Site</a>
                            </div>
                        </div>
                        <a href="<?= BASE_URL ?>admin/logout.php" class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors" title="Sign Out">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-col w-0 flex-1 overflow-hidden">
            <!-- Top Navbar for Mobile & Header Actions -->
            <header class="relative z-10 flex-shrink-0 flex h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-800 transition-colors duration-200">
                <button id="mobileMenuButton" class="px-4 border-r border-gray-200 dark:border-gray-850 text-gray-500 md:hidden focus:outline-none focus:ring-2 focus:ring-inset focus:ring-brand-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                
                <div class="flex-1 px-4 flex justify-between">
                    <div class="flex-1 flex items-center">
                        <h1 class="text-lg font-bold text-gray-800 dark:text-white md:ml-4">Admin Dashboard</h1>
                    </div>
                    <div class="ml-4 flex items-center md:ml-6 space-x-2">
                        <!-- Theme Toggle -->
                        <button id="adminThemeToggle" class="p-2 rounded-full text-gray-500 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none">
                            <svg id="adminSunIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m12.728 12.728l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z"/></svg>
                            <svg id="adminMoonIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Mobile Navigation Overlay -->
            <div id="mobileMenu" class="fixed inset-0 z-40 md:hidden hidden">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75" id="mobileMenuCloseBg"></div>
                <div class="fixed inset-y-0 left-0 flex flex-col w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-800 pt-5 pb-4">
                    <div class="flex items-center flex-shrink-0 px-6 pb-4 border-b border-gray-150 dark:border-gray-800">
                        <span class="text-xl font-bold bg-gradient-to-r from-brand-600 to-blue-400 bg-clip-text text-transparent">CMS Admin</span>
                    </div>
                    <nav class="mt-5 px-4 space-y-1 flex-1 overflow-y-auto no-scrollbar">
                        <a href="<?= BASE_URL ?>admin/index.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('index.php', $currentPage) ?>">
                            Dashboard
                        </a>
                        <a href="<?= BASE_URL ?>admin/posts.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('posts.php', $currentPage) ?>">
                            Blog Posts
                        </a>
                        <a href="<?= BASE_URL ?>admin/portfolios.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('portfolios.php', $currentPage) ?>">
                            Portfolios
                        </a>
                        <a href="<?= BASE_URL ?>admin/messages.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('messages.php', $currentPage) ?>">
                            Inbox Messages
                        </a>
                        <a href="<?= BASE_URL ?>admin/settings.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('settings.php', $currentPage) ?>">
                            Web Settings
                        </a>
                        <a href="<?= BASE_URL ?>admin/profile.php" class="flex items-center px-4 py-2.5 text-sm rounded-xl transition-all duration-150 <?= isActive('profile.php', $currentPage) ?>">
                            Profil Admin
                        </a>
                    </nav>
                    <div class="border-t border-gray-200 dark:border-gray-800 p-4 flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200"><?= sanitize($_SESSION['admin_username']) ?></span>
                        <a href="<?= BASE_URL ?>admin/logout.php" class="text-sm text-red-500 font-semibold">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Page Inner Scroll Area -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
