<?php
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';

// Get current user info based on session
$userId = $_SESSION['admin_user_id'];

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token($_POST['csrf_token'] ?? '');
    
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($email)) {
        $error = 'Username and Email are required.';
    } else {
        try {
            // Check if username or email already exists for other users
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $userId]);
            if ($stmt->fetchColumn() > 0) {
                $error = 'Username or Email is already taken by another user.';
            } else {
                if (!empty($newPassword)) {
                    if ($newPassword !== $confirmPassword) {
                        $error = 'New Password and Confirm Password do not match.';
                    } else {
                        // Update with new password
                        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
                        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password_hash = ? WHERE id = ?");
                        $stmt->execute([$username, $email, $hash, $userId]);
                        
                        // Update session username if changed
                        $_SESSION['admin_username'] = $username;
                        $success = 'Profile and Password updated successfully.';
                    }
                } else {
                    // Update without password
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $userId]);
                    
                    // Update session username if changed
                    $_SESSION['admin_username'] = $username;
                    $success = 'Profile updated successfully.';
                }
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch user data after any updates
$stmt = $pdo->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    die('User not found.');
}
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-5">
        <div>
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Profil & Keamanan Akun</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola informasi login dan ubah kata sandi admin Anda.</p>
        </div>
    </div>

    <!-- Alerts -->
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

    <form action="" method="POST" class="bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl p-6 md:p-8 space-y-8">
        <?= csrf_field() ?>
        
        <!-- Informasi Profil -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-3 mb-5">Informasi Dasar</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Username</label>
                    <input type="text" name="username" value="<?= sanitize($user['username']) ?>" required class="mt-1 block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Email Address</label>
                    <input type="email" name="email" value="<?= sanitize($user['email']) ?>" required class="mt-1 block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                </div>
            </div>
        </div>

        <!-- Keamanan / Password -->
        <div>
            <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-5">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ubah Kata Sandi (Opsional)</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Biarkan kosong jika Anda tidak ingin mengubah kata sandi Anda saat ini.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Password Baru</label>
                    <input type="password" name="new_password" placeholder="Minimal 6 karakter" class="mt-1 block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Konfirmasi Password Baru</label>
                    <input type="password" name="confirm_password" placeholder="Ulangi password baru" class="mt-1 block w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white transition-colors">
                </div>
            </div>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold shadow-md transition-all duration-150 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-500 flex items-center">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
