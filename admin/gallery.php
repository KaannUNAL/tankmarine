<?php
/* ===================== admin/gallery.php ===================== */
require_once 'header.php';

// Kategori işlemleri
if (isset($_POST['add_category'])) {
    $name = $_POST['category_name'];
    $slug = slugify(trToEn($name));
    $pdo->prepare("INSERT INTO gallery_categories (name, slug) VALUES (?, ?)")->execute([$name, $slug]);
    flash('success', 'Kategori eklendi!');
    redirect('/admin/gallery.php');
}

if (isset($_GET['delete_category'])) {
    $pdo->prepare("DELETE FROM gallery_categories WHERE id = ?")->execute([$_GET['delete_category']]);
    flash('success', 'Kategori silindi!');
    redirect('/admin/gallery.php');
}

// Galeri öğesi işlemleri
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT image FROM gallery WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $item = $stmt->fetch();
    if ($item && $item['image']) deleteFile($item['image']);
    $pdo->prepare("DELETE FROM gallery WHERE id = ?")->execute([$_GET['delete']]);
    flash('success', 'Fotoğraf silindi!');
    redirect('/admin/gallery.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_gallery'])) {
    $category_id = $_POST['category_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $active = isset($_POST['active']) ? 1 : 0;
    
    if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        foreach ($_FILES['images']['name'] as $key => $name) {
            if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['images']['name'][$key],
                    'type' => $_FILES['images']['type'][$key],
                    'tmp_name' => $_FILES['images']['tmp_name'][$key],
                    'error' => $_FILES['images']['error'][$key],
                    'size' => $_FILES['images']['size'][$key]
                ];
                
                $image = uploadFile($file, 'gallery');
                if ($image) {
                    $stmt = $pdo->prepare("INSERT INTO gallery (category_id, title, image, description, active) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$category_id, $title, $image, $description, $active]);
                }
            }
        }
        flash('success', 'Fotoğraflar eklendi!');
        redirect('/admin/gallery.php');
    }
}

$categories = $pdo->query("SELECT * FROM gallery_categories ORDER BY sort_order ASC")->fetchAll();
$filter_cat = $_GET['category'] ?? null;

if ($filter_cat) {
    $stmt = $pdo->prepare("SELECT g.*, gc.name as category_name FROM gallery g 
                          LEFT JOIN gallery_categories gc ON g.category_id = gc.id 
                          WHERE g.category_id = ? ORDER BY g.sort_order ASC");
    $stmt->execute([$filter_cat]);
} else {
    $stmt = $pdo->query("SELECT g.*, gc.name as category_name FROM gallery g 
                        LEFT JOIN gallery_categories gc ON g.category_id = gc.id 
                        ORDER BY g.sort_order ASC");
}
$gallery_items = $stmt->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<!-- Kategori Yönetimi -->
<div class="content-card">
    <div class="content-header">
        <h2>Galeri Kategorileri</h2>
    </div>

    <form method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
        <input type="text" name="category_name" class="form-control" placeholder="Yeni kategori adı" required style="max-width: 300px;">
        <button type="submit" name="add_category" class="btn btn-success">
            <i class="fas fa-plus"></i> Kategori Ekle
        </button>
    </form>

    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <?php foreach($categories as $cat): ?>
        <div style="display: flex; align-items: center; gap: 10px; padding: 10px 20px; background: var(--light); border-radius: 50px;">
            <span><?php echo htmlspecialchars($cat['name']); ?></span>
            <a href="gallery.php?delete_category=<?php echo $cat['id']; ?>" style="color: var(--danger);" onclick="return confirmDelete('Bu kategoriyi ve içindeki fotoğrafları silmek istediğinizden emin misiniz?')">
                <i class="fas fa-times"></i>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Fotoğraf Ekleme -->
<div class="content-card">
    <div class="content-header">
        <h2>Yeni Fotoğraf Ekle</h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label>Kategori *</label>
                <select name="category_id" class="form-control" required>
                    <option value="">Seçin...</option>
                    <?php foreach($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Başlık</label>
                <input type="text" name="title" class="form-control" placeholder="Fotoğraf başlığı">
            </div>
        </div>

        <div class="form-group">
            <label>Açıklama</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Kısa açıklama"></textarea>
        </div>

        <div class="form-group">
            <label>Fotoğraflar * (Birden fazla seçebilirsiniz)</label>
            <input type="file" name="images[]" class="form-control" accept="image/*" multiple required>
            <small style="color: #666;">Ctrl/Cmd tuşu ile birden fazla dosya seçebilirsiniz</small>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="active" value="1" checked>
                <span>Yayınla</span>
            </label>
        </div>

        <button type="submit" name="add_gallery" class="btn btn-success">
            <i class="fas fa-upload"></i> Yükle
        </button>
    </form>
</div>

<!-- Fotoğraf Listesi -->
<div class="content-card">
    <div class="content-header">
        <h2>Galeri</h2>
        <div style="display: flex; gap: 10px;">
            <a href="gallery.php" class="btn <?php echo !$filter_cat ? 'btn-primary' : 'btn-warning'; ?> btn-sm">Tümü</a>
            <?php foreach($categories as $cat): ?>
            <a href="gallery.php?category=<?php echo $cat['id']; ?>" class="btn <?php echo $filter_cat == $cat['id'] ? 'btn-primary' : 'btn-warning'; ?> btn-sm">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if(count($gallery_items) > 0): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px;">
        <?php foreach($gallery_items as $item): ?>
        <div style="position: relative; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <img src="<?php echo UPLOAD_URL . $item['image']; ?>" alt="" style="width: 100%; height: 200px; object-fit: cover;">
            <div style="padding: 15px; background: white;">
                <div style="font-weight: 600; margin-bottom: 5px; color: var(--primary);">
                    <?php echo htmlspecialchars($item['title'] ?: 'Başlıksız'); ?>
                </div>
                <div style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">
                    <?php echo htmlspecialchars($item['category_name']); ?>
                </div>
                <a href="gallery.php?delete=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm" style="width: 100%;" onclick="return confirmDelete()">
                    <i class="fas fa-trash"></i> Sil
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz fotoğraf eklenmemiş</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>

