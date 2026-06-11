<?php
$pageTitle = "Hubungi Saya";
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = 'Pesan Anda berhasil dikirim! Saya akan segera menghubungi Anda.';
            
            // Clear inputs on success
            $name = $email = $subject = $message = '';
        } catch (PDOException $e) {
            $error = 'Gagal mengirim pesan: ' . $e->getMessage();
        }
    }
}

// Fetch settings for contact cards
$contactEmail = get_setting('contact_email');
$contactPhone = get_setting('contact_phone');
$contactAddress = get_setting('contact_address');
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 space-y-12">
    <!-- Header Title -->
    <div class="text-center space-y-4 max-w-2xl mx-auto">
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-900 dark:text-white">Hubungi Saya</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm sm:text-base leading-relaxed">Punya pertanyaan, tawaran kerja sama, atau sekedar ingin menyapa? Kirim pesan Anda di bawah ini.</p>
        <div class="w-16 h-1 bg-brand-500 mx-auto rounded-full"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Contact Cards (Left) -->
        <div class="space-y-6">
            <!-- Email card -->
            <?php if (!empty($contactEmail)): ?>
                <div class="bg-gray-50 dark:bg-gray-900 border border-gray-150 dark:border-gray-800 rounded-2xl p-6 shadow-sm flex items-start space-x-4">
                    <div class="p-3 bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Email</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 break-all"><?= sanitize($contactEmail) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Phone card -->
            <?php if (!empty($contactPhone)): ?>
                <div class="bg-gray-50 dark:bg-gray-900 border border-gray-150 dark:border-gray-800 rounded-2xl p-6 shadow-sm flex items-start space-x-4">
                    <div class="p-3 bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Telepon / WA</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400"><?= sanitize($contactPhone) ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Address card -->
            <?php if (!empty($contactAddress)): ?>
                <div class="bg-gray-50 dark:bg-gray-900 border border-gray-150 dark:border-gray-800 rounded-2xl p-6 shadow-sm flex items-start space-x-4">
                    <div class="p-3 bg-brand-50 dark:bg-brand-900/30 text-brand-600 dark:text-brand-400 rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Alamat</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed"><?= sanitize($contactAddress) ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contact Form (Right) -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 border border-gray-150 dark:border-gray-800 shadow-md rounded-2xl p-8 space-y-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white pb-3 border-b border-gray-100 dark:border-gray-800">Kirim Pesan</h2>
            
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

            <form action="" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Lengkap</label>
                        <input type="text" name="name" value="<?= sanitize($name ?? '') ?>" required class="mt-1 block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Alamat Email</label>
                        <input type="email" name="email" value="<?= sanitize($email ?? '') ?>" required class="mt-1 block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Subjek</label>
                    <input type="text" name="subject" value="<?= sanitize($subject ?? '') ?>" required class="mt-1 block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Pesan</label>
                    <textarea name="message" rows="6" required class="mt-1 block w-full px-3 py-2.5 border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white"><?= sanitize($message ?? '') ?></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transform hover:-translate-y-0.5 transition-all duration-150">
                        Kirim Pesan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
