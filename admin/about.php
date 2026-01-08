<?php
// ===================== admin/about.php =====================
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $mission = $_POST['mission'];
    $vision = $_POST['vision'];
    $video_url = $_POST['video_url'];
    
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = uploadFile($_FILES['image'], 'about');
        $stmt = $pdo->prepare("SELECT image FROM about WHERE id = 1");
        $stmt->execute();
        $old = $stmt->fetch();
        if ($old && $old['image']) deleteFile($old['image']);
    }
    
    if ($image) {
        $stmt = $pdo->prepare("UPDATE about SET title=?, content=?, mission=?, vision=?, video_url=?, image=? WHERE id=1");
        $stmt->execute([$title, $content, $mission, $vision, $video_url, $image]);
    } else {
        $stmt = $pdo->prepare("UPDATE about SET title=?, content=?, mission=?, vision=?, video_url=? WHERE id=1");
        $stmt->execute([$title, $content, $mission, $vision, $video_url]);
    }
    
    flash('success', 'Hakkımızda bilgileri güncellendi!');
    redirect('/admin/about.php');
}

$stmt = $pdo->query("SELECT * FROM about WHERE id = 1");
$about = $stmt->fetch();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2>Hakkımızda Bilgileri</h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Başlık *</label>
            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($about['title']); ?>" required>
        </div>

        <div class="form-group">
            <label>İçerik *</label>
            <textarea name="content" class="form-control" rows="6" required><?php echo htmlspecialchars($about['content']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Misyon *</label>
            <textarea name="mission" class="form-control" rows="4" required><?php echo htmlspecialchars($about['mission']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Vizyon *</label>
            <textarea name="vision" class="form-control" rows="4" required><?php echo htmlspecialchars($about['vision']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Video URL (YouTube, Vimeo vb.)</label>
            <input type="url" name="video_url" class="form-control" value="<?php echo htmlspecialchars($about['video_url']); ?>" placeholder="https://www.youtube.com/watch?v=...">
        </div>

        <div class="form-group">
            <label>Görsel</label>
            <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'imagePreview')">
            <div class="image-preview" id="imagePreview">
                <?php if($about['image']): ?>
                <img src="<?php echo UPLOAD_URL . $about['image']; ?>" alt="">
                <?php else: ?>
                <i class="fas fa-image" style="font-size: 3rem; color: #ddd;"></i>
                <?php endif; ?>
            </div>
        </div>

        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Kaydet
        </button>
    </form>
</div>

<!-- İstatistikler Yönetimi -->
<div class="content-card">
    <div class="content-header">
        <h2>İstatistikler</h2>
    </div>

    <?php
    $stmt = $pdo->query("SELECT * FROM statistics ORDER BY sort_order ASC");
    $stats = $stmt->fetchAll();
    ?>

    <p style="color: #666; margin-bottom: 20px;">Ana sayfada gösterilen istatistikler. <a href="statistics.php" style="color: var(--accent);">Düzenlemek için tıklayın</a></p>

    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <?php foreach($stats as $stat): ?>
        <div style="background: var(--light); padding: 20px; border-radius: 10px; text-align: center;">
            <i class="fas <?php echo $stat['icon']; ?>" style="font-size: 2rem; color: var(--accent); margin-bottom: 10px;"></i>
            <h3 style="font-size: 2rem; color: var(--primary); margin-bottom: 5px;">
                <?php echo htmlspecialchars($stat['value']); ?>
            </h3>
            <p style="color: #666;"><?php echo htmlspecialchars($stat['label']); ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>