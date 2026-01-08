<?php
/* ===================== admin/services.php ===================== */
require_once 'header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$_GET['delete']]);
    flash('success', 'Hizmet silindi!');
    redirect('/admin/services.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $icon = $_POST['icon'];
    $description = $_POST['description'];
    $active = isset($_POST['active']) ? 1 : 0;
    $sort_order = $_POST['sort_order'];
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE services SET title=?, icon=?, description=?, active=?, sort_order=? WHERE id=?");
        $stmt->execute([$title, $icon, $description, $active, $sort_order, $id]);
        flash('success', 'Hizmet güncellendi!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO services (title, icon, description, active, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $icon, $description, $active, $sort_order]);
        flash('success', 'Hizmet eklendi!');
    }
    redirect('/admin/services.php');
}

$edit_service = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_service = $stmt->fetch();
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC")->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_service ? 'Hizmet Düzenle' : 'Yeni Hizmet Ekle'; ?></h2>
    </div>

    <form method="POST">
        <?php if($edit_service): ?>
        <input type="hidden" name="id" value="<?php echo $edit_service['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Hizmet Başlığı *</label>
                <input type="text" name="title" class="form-control" value="<?php echo $edit_service ? htmlspecialchars($edit_service['title']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Font Awesome Icon *</label>
                <input type="text" name="icon" class="form-control" value="<?php echo $edit_service ? htmlspecialchars($edit_service['icon']) : ''; ?>" required placeholder="fa-ship">
                <small style="color: #666;">
                    Icon listesi: <a href="https://fontawesome.com/icons" target="_blank" style="color: var(--accent);">fontawesome.com/icons</a>
                </small>
            </div>
        </div>

        <div class="form-group">
            <label>Açıklama *</label>
            <textarea name="description" class="form-control" rows="4" required><?php echo $edit_service ? htmlspecialchars($edit_service['description']) : ''; ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sıra No</label>
                <input type="number" name="sort_order" class="form-control" value="<?php echo $edit_service ? $edit_service['sort_order'] : '0'; ?>" min="0">
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="active" value="1" <?php echo (!$edit_service || $edit_service['active']) ? 'checked' : ''; ?>>
                    <span>Aktif</span>
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $edit_service ? 'Güncelle' : 'Kaydet'; ?>
            </button>
            <?php if($edit_service): ?>
            <a href="services.php" class="btn btn-danger"><i class="fas fa-times"></i> İptal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Hizmetler</h2>
    </div>

    <?php if(count($services) > 0): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
        <?php foreach($services as $service): ?>
        <div style="background: var(--light); padding: 30px; border-radius: 10px; text-align: center; position: relative;">
            <div style="font-size: 3rem; color: var(--accent); margin-bottom: 15px;">
                <i class="fas <?php echo $service['icon']; ?>"></i>
            </div>
            <h3 style="color: var(--primary); margin-bottom: 10px; font-size: 1.2rem;">
                <?php echo htmlspecialchars($service['title']); ?>
            </h3>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">
                <?php echo htmlspecialchars($service['description']); ?>
            </p>
            <div style="display: flex; gap: 5px; justify-content: center;">
                <span class="badge badge-<?php echo $service['active'] ? 'success' : 'danger'; ?>">
                    <?php echo $service['active'] ? 'Aktif' : 'Pasif'; ?>
                </span>
                <span class="badge badge-info">Sıra: <?php echo $service['sort_order']; ?></span>
            </div>
            <div style="display: flex; gap: 10px; margin-top: 15px; justify-content: center;">
                <a href="services.php?edit=<?php echo $service['id']; ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Düzenle
                </a>
                <a href="services.php?delete=<?php echo $service['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                    <i class="fas fa-trash"></i> Sil
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz hizmet eklenmemiş</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>