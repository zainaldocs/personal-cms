<?php
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    verify_csrf_token($_POST['csrf_token'] ?? '');

    try {
        $pdo->beginTransaction();

        // Update text settings
        if (isset($_POST['settings']) && is_array($_POST['settings'])) {
            foreach ($_POST['settings'] as $key => $value) {
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
                $stmt->execute([$value, $key]);
            }
        }

        // Handle about_image upload
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            $uploaded_file = upload_image($_FILES['about_image'], '../assets/images');
            
            // Delete old image if exists
            $old_image = get_setting('about_image');
            if (!empty($old_image) && file_exists('../assets/images/' . $old_image)) {
                unlink('../assets/images/' . $old_image);
            }

            // Save new filename to settings
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'about_image'");
            $stmt->execute([$uploaded_file]);
        }

        $pdo->commit();
        $success = 'Settings updated successfully.';
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = 'Error updating settings: ' . $e->getMessage();
    }
}

// Fetch all settings
$settings = [];
try {
    $rows = $pdo->query("SELECT setting_key, setting_value FROM settings")->fetchAll();
    foreach ($rows as $row) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    $error = 'Failed to load settings: ' . $e->getMessage();
}
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Web Settings</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Sesuaikan informasi profil, judul, dan data kontak situs Anda.</p>
        </div>
    </div>

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

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>
        <!-- Site Configuration -->
        <div class="bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl p-6 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">Informasi Umum</h3>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nama Situs / Judul Navbar</label>
                    <input type="text" name="settings[site_name]" value="<?= sanitize($settings['site_name'] ?? '') ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Hero Section Configuration -->
        <div class="bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl p-6 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">Beranda (Hero Section)</h3>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Hero Title (Judul Utama)</label>
                    <input type="text" name="settings[hero_title]" value="<?= sanitize($settings['hero_title'] ?? '') ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Hero Subtitle (Subjudul/Tagline)</label>
                    <input type="text" name="settings[hero_subtitle]" value="<?= sanitize($settings['hero_subtitle'] ?? '') ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- About Page Configuration -->
        <div class="bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl p-6 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">Profil & Halaman About</h3>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">About Title</label>
                    <input type="text" name="settings[about_title]" value="<?= sanitize($settings['about_title'] ?? 'About Me') ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">About Content (Deskripsi Profil)</label>
                    <textarea name="settings[about_text]" rows="5" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm"><?= sanitize($settings['about_text'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Foto Profil (Sekarang)</label>
                    <div class="mt-2 flex items-center space-x-5">
                        <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-700 flex items-center justify-center">
                            <?php if (!empty($settings['about_image']) && file_exists('../assets/images/' . $settings['about_image'])): ?>
                                <img src="<?= BASE_URL . 'assets/images/' . $settings['about_image'] ?>" alt="Profile" class="w-full h-full object-cover">
                            <?php else: ?>
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            <?php endif; ?>
                        </div>
                        <input type="file" name="about_image" class="text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 dark:file:bg-gray-700 dark:file:text-brand-400 hover:file:bg-brand-100 transition-colors">
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact details -->
        <div class="bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl p-6 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">Informasi Kontak</h3>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email Kontak</label>
                    <input type="email" name="settings[contact_email]" value="<?= sanitize($settings['contact_email'] ?? '') ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Nomor Telepon/WhatsApp</label>
                    <input type="text" name="settings[contact_phone]" value="<?= sanitize($settings['contact_phone'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Alamat</label>
                    <input type="text" name="settings[contact_address]" value="<?= sanitize($settings['contact_address'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Social Media configuration -->
        <div class="bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl p-6 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3">Sosial Media</h3>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">GitHub Link</label>
                    <input type="url" name="settings[social_github]" value="<?= sanitize($settings['social_github'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">LinkedIn Link</label>
                    <input type="url" name="settings[social_linkedin]" value="<?= sanitize($settings['social_linkedin'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Twitter/X Link</label>
                    <input type="url" name="settings[social_twitter]" value="<?= sanitize($settings['social_twitter'] ?? '') ?>" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-3">
            <button type="submit" class="px-6 py-3 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 transform hover:-translate-y-0.5 transition-all duration-150">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
