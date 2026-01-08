<?php
require_once 'header.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM sliders WHERE id = ?");
    $stmt->execute([$id]);
    $slider = $stmt->fetch();
    if ($slider && $slider['image']) deleteFile($slider['image']);
    $pdo->prepare("DELETE FROM sliders WHERE id = ?")->execute([$id]);
    flash('success', 'Slider başarıyla silindi!');
    redirect('/admin/sliders.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $description = $_POST['description'];
    $button_text = $_POST['button_text'];
    $button_link = $_POST['button_link'];
    $active = isset($_POST['active']) ? 1 : 0;
    $sort_order = $_POST['sort_order'];
    
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = uploadFile($_FILES['image'], 'slider');
        if ($id) {
            $stmt = $pdo->prepare("SELECT image FROM sliders WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch();
            if ($old && $old['image']) deleteFile($old['image']);
        }
    }
    
    if ($id) {
        if ($image) {
            $stmt = $pdo->prepare("UPDATE sliders SET title=?, description=?, image=?, button_text=?, button_link=?, active=?, sort_order=? WHERE id=?");
            $stmt->execute([$title, $description, $image, $button_text, $button_link, $active, $sort_order, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE sliders SET title=?, description=?, button_text=?, button_link=?, active=?, sort_order=? WHERE id=?");
            $stmt->execute([$title, $description, $button_text, $button_link, $active, $sort_order, $id]);
        }
        flash('success', 'Slider başarıyla güncellendi!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO sliders (title, description, image, button_text, button_link, active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $image, $button_text, $button_link, $active, $sort_order]);
        flash('success', 'Slider başarıyla eklendi!');
    }
    redirect('/admin/sliders.php');
}

$edit_slider = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM sliders WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_slider = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM sliders ORDER BY sort_order ASC");
$sliders = $stmt->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_slider ? 'Slider Düzenle' : 'Yeni Slider Ekle'; ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <?php if($edit_slider): ?>
        <input type="hidden" name="id" value="<?php echo $edit_slider['id']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Başlık *</label>
            <input type="text" name="title" class="form-control" value="<?php echo $edit_slider ? htmlspecialchars($edit_slider['title']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Açıklama *</label>
            <textarea name="description" class="form-control" rows="3" required><?php echo $edit_slider ? htmlspecialchars($edit_slider['description']) : ''; ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Buton Yazısı</label>
                <input type="text" name="button_text" class="form-control" value="<?php echo $edit_slider ? htmlspecialchars($edit_slider['button_text']) : ''; ?>" placeholder="Örn: Detaylı Bilgi">
            </div>

            <div class="form-group">
                <label>Buton Linki</label>
                <input type="text" name="button_link" class="form-control" value="<?php echo $edit_slider ? htmlspecialchars($edit_slider['button_link']) : ''; ?>" placeholder="Örn: /about">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Görsel (1920x600 önerilir) *</label>
                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'imagePreview')" <?php echo !$edit_slider ? 'required' : ''; ?>>
                <div class="image-preview" id="imagePreview" style="width: 100%; height: 200px;">
                    <?php if($edit_slider && $edit_slider['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $edit_slider['image']; ?>" alt="">
                    <?php else: ?>
                    <i class="fas fa-image" style="font-size: 3rem; color: #ddd;"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label>Sıra No</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo $edit_slider ? $edit_slider['sort_order'] : '0'; ?>" min="0">
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="active" value="1" <?php echo (!$edit_slider || $edit_slider['active']) ? 'checked' : ''; ?>>
                        <span>Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $edit_slider ? 'Güncelle' : 'Kaydet'; ?>
            </button>
            <?php if($edit_slider): ?>
            <a href="sliders.php" class="btn btn-danger"><i class="fas fa-times"></i> İptal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Slider Listesi</h2>
    </div>

    <?php if(count($sliders) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Görsel</th>
                <th>Başlık</th>
                <th>Açıklama</th>
                <th>Sıra</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($sliders as $slider): ?>
            <tr>
                <td>
                    <?php if($slider['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $slider['image']; ?>" alt="" style="width: 100px; height: 60px;">
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($slider['title']); ?></strong></td>
                <td><?php echo htmlspecialchars(substr($slider['description'], 0, 60)) . '...'; ?></td>
                <td><?php echo $slider['sort_order']; ?></td>
                <td>
                    <span class="badge badge-<?php echo $slider['active'] ? 'success' : 'danger'; ?>">
                        <?php echo $slider['active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
                <td>
                    <a href="sliders.php?edit=<?php echo $slider['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="sliders.php?delete=<?php echo $slider['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz slider eklenmemiş</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>