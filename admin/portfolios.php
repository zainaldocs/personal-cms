<?php
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$portfolioId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Delete Action
if ($action === 'delete' && $portfolioId > 0) {
    try {
        // Fetch image path to delete it from filesystem
        $stmt = $pdo->prepare("SELECT image FROM portfolios WHERE id = ?");
        $stmt->execute([$portfolioId]);
        $portfolio = $stmt->fetch();
        
        if ($portfolio) {
            if (!empty($portfolio['image']) && file_exists('../assets/images/' . $portfolio['image'])) {
                unlink('../assets/images/' . $portfolio['image']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM portfolios WHERE id = ?");
            $stmt->execute([$portfolioId]);
            $success = 'Portfolio deleted successfully.';
        } else {
            $error = 'Portfolio item not found.';
        }
    } catch (PDOException $e) {
        $error = 'Failed to delete portfolio: ' . $e->getMessage();
    }
    $action = 'list'; // redirect to list view after delete
}

// Handle Form Submission (Add or Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add' || $action === 'edit')) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $project_url = trim($_POST['project_url']);
    $order_index = (int)$_POST['order_index'];
    
    if (empty($title) || empty($description)) {
        $error = 'Title and Description are required.';
    } else {
        try {
            $image_file = '';
            
            // Check if portfolio already has an image (for editing)
            if ($action === 'edit') {
                $stmt = $pdo->prepare("SELECT image FROM portfolios WHERE id = ?");
                $stmt->execute([$portfolioId]);
                $portfolio = $stmt->fetch();
                if ($portfolio) {
                    $image_file = $portfolio['image'];
                }
            }

            // Handle Image Upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploaded = upload_image($_FILES['image'], '../assets/images');
                
                // Delete old image if updating
                if (!empty($image_file) && file_exists('../assets/images/' . $image_file)) {
                    unlink('../assets/images/' . $image_file);
                }
                $image_file = $uploaded;
            }

            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO portfolios (title, description, image, project_url, order_index) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $description, $image_file, $project_url, $order_index]);
                $success = 'Portfolio item created successfully.';
                $action = 'list';
            } else {
                $stmt = $pdo->prepare("UPDATE portfolios SET title = ?, description = ?, image = ?, project_url = ?, order_index = ? WHERE id = ?");
                $stmt->execute([$title, $description, $image_file, $project_url, $order_index, $portfolioId]);
                $success = 'Portfolio item updated successfully.';
                $action = 'list';
            }
        } catch (Exception $e) {
            $error = 'Error saving portfolio: ' . $e->getMessage();
        }
    }
}

// Fetch all portfolios (for list view)
$portfolios = [];
if ($action === 'list') {
    try {
        $portfolios = $pdo->query("SELECT * FROM portfolios ORDER BY order_index ASC, created_at DESC")->fetchAll();
    } catch (PDOException $e) {
        $error = 'Failed to fetch portfolios: ' . $e->getMessage();
    }
}

// Fetch single portfolio (for edit view)
$editPortfolio = null;
if ($action === 'edit' && $portfolioId > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM portfolios WHERE id = ?");
        $stmt->execute([$portfolioId]);
        $editPortfolio = $stmt->fetch();
        if (!$editPortfolio) {
            $error = 'Portfolio item not found.';
            $action = 'list';
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
        $action = 'list';
    }
}
?>

<div class="max-w-6xl mx-auto space-y-6">
    <!-- Feedback Alerts -->
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

    <!-- LIST VIEW -->
    <?php if ($action === 'list'): ?>
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Portfolios</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur dan kelola proyek portofolio Anda di sini.</p>
            </div>
            <a href="portfolios.php?action=add" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition-all duration-150 transform hover:-translate-y-0.5">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Project
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden">
            <?php if (empty($portfolios)): ?>
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Belum ada proyek portofolio. Klik "Tambah Project" di atas untuk menambah proyek pertama Anda!
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-150 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul Proyek</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">URL Proyek</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Urutan (Order)</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-150 dark:divide-gray-700">
                            <?php foreach ($portfolios as $p): ?>
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-16 h-10 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-150 dark:border-gray-700">
                                            <?php if (!empty($p['image']) && file_exists('../assets/images/' . $p['image'])): ?>
                                                <img src="<?= BASE_URL . 'assets/images/' . $p['image'] ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white"><?= sanitize($p['title']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <?php if (!empty($p['project_url'])): ?>
                                            <a href="<?= sanitize($p['project_url']) ?>" target="_blank" class="text-brand-600 dark:text-brand-400 hover:underline"><?= sanitize($p['project_url']) ?></a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <?= $p['order_index'] ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold space-x-2">
                                        <a href="portfolios.php?action=edit&id=<?= $p['id'] ?>" class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300">Edit</a>
                                        <a href="portfolios.php?action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus proyek ini?')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    <!-- ADD OR EDIT FORM VIEW -->
    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <?php 
            $isEdit = $action === 'edit';
            $formTitle = $isEdit ? 'Edit Proyek Portofolio' : 'Tambah Proyek Baru';
            $titleValue = $isEdit ? $editPortfolio['title'] : '';
            $descriptionValue = $isEdit ? $editPortfolio['description'] : '';
            $projectUrlValue = $isEdit ? $editPortfolio['project_url'] : '';
            $orderIndexValue = $isEdit ? $editPortfolio['order_index'] : 0;
            $imageValue = $isEdit ? $editPortfolio['image'] : '';
        ?>
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-5">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white"><?= $formTitle ?></h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola judul, deskripsi, tautan proyek, dan urutan tampil.</p>
            </div>
            <a href="portfolios.php" class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:underline flex items-center">
                <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke daftar
            </a>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content Form (Left) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl p-6 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Judul Proyek</label>
                    <input type="text" name="title" value="<?= sanitize($titleValue) ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Deskripsi Proyek</label>
                    <textarea name="description" rows="10" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?= sanitize($descriptionValue) ?></textarea>
                </div>
            </div>

            <!-- Settings Sidebar Form (Right) -->
            <div class="space-y-6">
                <!-- Status & Image Card -->
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">URL Demo / Repo Proyek</label>
                        <input type="url" name="project_url" value="<?= sanitize($projectUrlValue) ?>" placeholder="https://..." class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Urutan Tampil (Order Index)</label>
                        <input type="number" name="order_index" value="<?= $orderIndexValue ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <span class="text-xs text-gray-500 mt-1 block">Angka lebih kecil akan tampil paling depan.</span>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Gambar Banner Proyek</label>
                        <?php if ($isEdit && !empty($imageValue) && file_exists('../assets/images/' . $imageValue)): ?>
                            <div class="my-3 w-full h-32 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-150 flex items-center justify-center">
                                <img src="<?= BASE_URL . 'assets/images/' . $imageValue ?>" class="w-full h-full object-cover">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="mt-2 text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 dark:file:bg-gray-700 dark:file:text-brand-400 hover:file:bg-brand-100 transition-colors">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-brand-600 text-white rounded-xl font-bold shadow-md hover:bg-brand-700 transition-colors transform hover:-translate-y-0.5 duration-150">
                    Simpan Proyek
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
