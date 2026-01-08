<?php
require_once 'header.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();
    if ($news && $news['image']) deleteFile($news['image']);
    $pdo->prepare("DELETE FROM news WHERE id = ?")->execute([$id]);
    flash('success', 'Haber başarıyla silindi!');
    redirect('/admin/news.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $slug = slugify(trToEn($title));
    $summary = $_POST['summary'];
    $content = $_POST['content'];
    $active = isset($_POST['active']) ? 1 : 0;
    
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = uploadFile($_FILES['image'], 'news');
        if ($id) {
            $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch();
            if ($old && $old['image']) deleteFile($old['image']);
        }
    }
    
    if ($id) {
        if ($image) {
            $stmt = $pdo->prepare("UPDATE news SET title=?, slug=?, summary=?, content=?, image=?, active=? WHERE id=?");
            $stmt->execute([$title, $slug, $summary, $content, $image, $active, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE news SET title=?, slug=?, summary=?, content=?, active=? WHERE id=?");
            $stmt->execute([$title, $slug, $summary, $content, $active, $id]);
        }
        flash('success', 'Haber başarıyla güncellendi!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO news (title, slug, summary, content, image, active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $slug, $summary, $content, $image, $active]);
        flash('success', 'Haber başarıyla eklendi!');
    }
    redirect('/admin/news.php');
}

$edit_news = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_news = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
$news_list = $stmt->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_news ? 'Haber Düzenle' : 'Yeni Haber Ekle'; ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <?php if($edit_news): ?>
        <input type="hidden" name="id" value="<?php echo $edit_news['id']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Başlık *</label>
            <input type="text" name="title" class="form-control" value="<?php echo $edit_news ? htmlspecialchars($edit_news['title']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>Özet *</label>
            <textarea name="summary" class="form-control" rows="3" required><?php echo $edit_news ? htmlspecialchars($edit_news['summary']) : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label>İçerik *</label>
            <textarea name="content" class="form-control" rows="10" required><?php echo $edit_news ? htmlspecialchars($edit_news['content']) : ''; ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Görsel</label>
                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                <div class="image-preview" id="imagePreview">
                    <?php if($edit_news && $edit_news['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $edit_news['image']; ?>" alt="">
                    <?php else: ?>
                    <i class="fas fa-image" style="font-size: 3rem; color: #ddd;"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="active" value="1" <?php echo (!$edit_news || $edit_news['active']) ? 'checked' : ''; ?>>
                        <span>Yayınla</span>
                    </label>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $edit_news ? 'Güncelle' : 'Yayınla'; ?>
            </button>
            <?php if($edit_news): ?>
            <a href="news.php" class="btn btn-danger"><i class="fas fa-times"></i> İptal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Haber Listesi</h2>
    </div>

    <?php if(count($news_list) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Görsel</th>
                <th>Başlık</th>
                <th>Görüntülenme</th>
                <th>Tarih</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($news_list as $news): ?>
            <tr>
                <td>
                    <?php if($news['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $news['image']; ?>" alt="">
                    <?php else: ?>
                    <div style="width: 60px; height: 60px; background: var(--light); border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-newspaper" style="font-size: 1.5rem; color: #999;"></i>
                    </div>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($news['title']); ?></strong></td>
                <td><?php echo $news['views']; ?></td>
                <td><?php echo date('d.m.Y', strtotime($news['created_at'])); ?></td>
                <td>
                    <span class="badge badge-<?php echo $news['active'] ? 'success' : 'danger'; ?>">
                        <?php echo $news['active'] ? 'Yayında' : 'Taslak'; ?>
                    </span>
                </td>
                <td>
                    <a href="news.php?edit=<?php echo $news['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="news.php?delete=<?php echo $news['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz haber eklenmemiş</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>