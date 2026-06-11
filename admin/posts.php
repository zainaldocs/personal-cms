<?php
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle Delete Action
if ($action === 'delete' && $postId > 0) {
    try {
        // Fetch image path to delete it from filesystem
        $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch();
        
        if ($post) {
            if (!empty($post['image']) && file_exists('../assets/images/' . $post['image'])) {
                unlink('../assets/images/' . $post['image']);
            }
            
            $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->execute([$postId]);
            $success = 'Post deleted successfully.';
        } else {
            $error = 'Post not found.';
        }
    } catch (PDOException $e) {
        $error = 'Failed to delete post: ' . $e->getMessage();
    }
    $action = 'list'; // redirect to list view after delete
}

// Handle Form Submission (Add or Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($action === 'add' || $action === 'edit')) {
    $title = trim($_POST['title']);
    $slug = trim($_POST['slug']);
    $content = trim($_POST['content']);
    $status = trim($_POST['status']);
    
    // Auto generate slug if empty
    if (empty($slug)) {
        $slug = slugify($title);
    } else {
        $slug = slugify($slug);
    }
    
    if (empty($title) || empty($content)) {
        $error = 'Title and Content are required.';
    } else {
        try {
            $image_file = '';
            
            // Check if post already has an image (for editing)
            if ($action === 'edit') {
                $stmt = $pdo->prepare("SELECT image FROM posts WHERE id = ?");
                $stmt->execute([$postId]);
                $post = $stmt->fetch();
                if ($post) {
                    $image_file = $post['image'];
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
                // Check if slug is unique
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetchColumn() > 0) {
                    // Append short random code to duplicate slug
                    $slug .= '-' . rand(100, 999);
                }

                $stmt = $pdo->prepare("INSERT INTO posts (title, slug, content, image, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $content, $image_file, $status]);
                $success = 'Post created successfully.';
                $action = 'list';
            } else {
                // Check duplicate slug for other articles
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM posts WHERE slug = ? AND id != ?");
                $stmt->execute([$slug, $postId]);
                if ($stmt->fetchColumn() > 0) {
                    $slug .= '-' . rand(100, 999);
                }

                $stmt = $pdo->prepare("UPDATE posts SET title = ?, slug = ?, content = ?, image = ?, status = ? WHERE id = ?");
                $stmt->execute([$title, $slug, $content, $image_file, $status, $postId]);
                $success = 'Post updated successfully.';
                $action = 'list';
            }
        } catch (Exception $e) {
            $error = 'Error saving post: ' . $e->getMessage();
        }
    }
}

// Fetch all posts (for list view)
$posts = [];
if ($action === 'list') {
    try {
        $posts = $pdo->query("SELECT * FROM posts ORDER BY created_at DESC")->fetchAll();
    } catch (PDOException $e) {
        $error = 'Failed to fetch posts: ' . $e->getMessage();
    }
}

// Fetch single post (for edit view)
$editPost = null;
if ($action === 'edit' && $postId > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        $editPost = $stmt->fetch();
        if (!$editPost) {
            $error = 'Post not found.';
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
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">Blog Posts</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tulis dan atur artikel blog Anda di sini.</p>
            </div>
            <a href="posts.php?action=add" class="inline-flex items-center px-4 py-2.5 border border-transparent rounded-xl shadow-md text-sm font-semibold text-white bg-brand-600 hover:bg-brand-700 transition-all duration-150 transform hover:-translate-y-0.5">
                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Artikel
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md border border-gray-100 dark:border-gray-800 rounded-2xl overflow-hidden">
            <?php if (empty($posts)): ?>
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 4a2 2 0 012 2v6a2 2 0 01-2 2h-2m-4-12a1 1 0 00-1-1H5a1 1 0 00-1 1v3a1 1 0 001 1h6a1 1 0 001-1V6z"/></svg>
                    Belum ada artikel. Klik "Tambah Artikel" di atas untuk menulis artikel pertama Anda!
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-150 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Gambar</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-150 dark:divide-gray-700">
                            <?php foreach ($posts as $post): ?>
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-16 h-10 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center border border-gray-150 dark:border-gray-700">
                                            <?php if (!empty($post['image']) && file_exists('../assets/images/' . $post['image'])): ?>
                                                <img src="<?= BASE_URL . 'assets/images/' . $post['image'] ?>" class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white"><?= sanitize($post['title']) ?></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">/blog/<?= sanitize($post['slug']) ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($post['status'] === 'published'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">Published</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">Draft</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <?= date('d M Y', strtotime($post['created_at'])) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold space-x-2">
                                        <a href="posts.php?action=edit&id=<?= $post['id'] ?>" class="text-brand-600 hover:text-brand-900 dark:text-brand-400 dark:hover:text-brand-300">Edit</a>
                                        <a href="posts.php?action=delete&id=<?= $post['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Hapus</a>
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
            $formTitle = $isEdit ? 'Edit Artikel' : 'Tulis Artikel Baru';
            $titleValue = $isEdit ? $editPost['title'] : '';
            $slugValue = $isEdit ? $editPost['slug'] : '';
            $contentValue = $isEdit ? $editPost['content'] : '';
            $statusValue = $isEdit ? $editPost['status'] : 'draft';
            $imageValue = $isEdit ? $editPost['image'] : '';
        ?>
        <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 pb-5">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white"><?= $formTitle ?></h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola judul, konten, status draft, dan gambar thumbnail.</p>
            </div>
            <a href="posts.php" class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:underline flex items-center">
                <svg class="mr-1 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke daftar
            </a>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content Form (Left) -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl p-6 space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Judul Artikel</label>
                    <input type="text" id="postTitle" name="title" value="<?= sanitize($titleValue) ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Slug (Opsional)</label>
                    <input type="text" id="postSlug" name="slug" value="<?= sanitize($slugValue) ?>" placeholder="Akan dibuat otomatis dari judul jika kosong" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Isi Konten</label>
                    <textarea name="content" rows="15" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white"><?= sanitize($contentValue) ?></textarea>
                </div>
            </div>

            <!-- Settings Sidebar Form (Right) -->
            <div class="space-y-6">
                <!-- Status & Image Card -->
                <div class="bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 shadow-md rounded-2xl p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Status</label>
                        <select name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-brand-500 focus:border-brand-500 sm:text-sm">
                            <option value="draft" <?= $statusValue === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $statusValue === 'published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Thumbnail Gambar</label>
                        <?php if ($isEdit && !empty($imageValue) && file_exists('../assets/images/' . $imageValue)): ?>
                            <div class="my-3 w-full h-32 rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-150 flex items-center justify-center">
                                <img src="<?= BASE_URL . 'assets/images/' . $imageValue ?>" class="w-full h-full object-cover">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" class="mt-2 text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 dark:file:bg-gray-700 dark:file:text-brand-400 hover:file:bg-brand-100 transition-colors">
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-brand-600 text-white rounded-xl font-bold shadow-md hover:bg-brand-700 transition-colors transform hover:-translate-y-0.5 duration-150">
                    Simpan Artikel
                </button>
            </div>
        </form>

        <script>
            // Simple slug autofill on title input
            const titleInput = document.getElementById('postTitle');
            const slugInput = document.getElementById('postSlug');

            if (titleInput && slugInput && slugInput.value === '') {
                titleInput.addEventListener('input', () => {
                    const slug = titleInput.value
                        .toLowerCase()
                        .replace(/[^\w\s-]/g, '') // remove spec chars
                        .replace(/[\s_]+/g, '-')   // replace spaces
                        .replace(/^-+|-+$/g, '');  // trim
                    slugInput.value = slug;
                });
            }
        </script>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
