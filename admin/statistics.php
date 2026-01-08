<?php
require_once 'header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM statistics WHERE id = ?")->execute([$_GET['delete']]);
    flash('success', 'İstatistik silindi!');
    redirect('/admin/statistics.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $label = $_POST['label'];
    $value = $_POST['value'];
    $icon = $_POST['icon'];
    $active = isset($_POST['active']) ? 1 : 0;
    $sort_order = $_POST['sort_order'];
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE statistics SET label=?, value=?, icon=?, active=?, sort_order=? WHERE id=?");
        $stmt->execute([$label, $value, $icon, $active, $sort_order, $id]);
        flash('success', 'İstatistik güncellendi!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO statistics (label, value, icon, active, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$label, $value, $icon, $active, $sort_order]);
        flash('success', 'İstatistik eklendi!');
    }
    redirect('/admin/statistics.php');
}

$edit_stat = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM statistics WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_stat = $stmt->fetch();
}

$statistics = $pdo->query("SELECT * FROM statistics ORDER BY sort_order ASC")->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong>Ana Sayfada Gösterilen İstatistikler</strong><br>
        Bu istatistikler ana sayfada "Hakkımızda" bölümünün altında gösterilir. Örnek: "15+ Yıllık Deneyim", "20+ Filo Sayısı", "500K+ Deniz Mili"
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_stat ? 'İstatistik Düzenle' : 'Yeni İstatistik Ekle'; ?></h2>
    </div>

    <form method="POST">
        <?php if($edit_stat): ?>
        <input type="hidden" name="id" value="<?php echo $edit_stat['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Etiket (Başlık) *</label>
                <input type="text" name="label" class="form-control" value="<?php echo $edit_stat ? htmlspecialchars($edit_stat['label']) : ''; ?>" required placeholder="Örn: Yıllık Deneyim">
            </div>

            <div class="form-group">
                <label>Değer *</label>
                <input type="text" name="value" class="form-control" value="<?php echo $edit_stat ? htmlspecialchars($edit_stat['value']) : ''; ?>" required placeholder="Örn: 15+">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Font Awesome Icon *</label>
                <input type="text" name="icon" class="form-control" value="<?php echo $edit_stat ? htmlspecialchars($edit_stat['icon']) : ''; ?>" required placeholder="fa-calendar">
                <small style="color: #666;">
                    Icon listesi: <a href="https://fontawesome.com/icons" target="_blank" style="color: var(--accent);">fontawesome.com/icons</a>
                </small>
            </div>

            <div class="form-group">
                <label>Sıra No</label>
                <input type="number" name="sort_order" class="form-control" value="<?php echo $edit_stat ? $edit_stat['sort_order'] : '0'; ?>" min="0">
            </div>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="active" value="1" <?php echo (!$edit_stat || $edit_stat['active']) ? 'checked' : ''; ?>>
                <span>Aktif</span>
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $edit_stat ? 'Güncelle' : 'Kaydet'; ?>
            </button>
            <?php if($edit_stat): ?>
            <a href="statistics.php" class="btn btn-danger"><i class="fas fa-times"></i> İptal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>İstatistik Listesi</h2>
    </div>

    <?php if(count($statistics) > 0): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
        <?php foreach($statistics as $stat): ?>
        <div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); padding: 40px; border-radius: 15px; text-align: center; color: white; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div style="font-size: 3rem; margin-bottom: 20px; opacity: 0.9;">
                <i class="fas <?php echo $stat['icon']; ?>"></i>
            </div>
            <div style="font-size: 3rem; font-weight: bold; margin-bottom: 10px;">
                <?php echo htmlspecialchars($stat['value']); ?>
            </div>
            <div style="font-size: 1.1rem; margin-bottom: 20px;">
                <?php echo htmlspecialchars($stat['label']); ?>
            </div>
            <div style="display: flex; gap: 5px; justify-content: center; margin-bottom: 15px;">
                <span style="padding: 5px 12px; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 0.85rem;">
                    <?php echo $stat['active'] ? '✓ Aktif' : '✗ Pasif'; ?>
                </span>
                <span style="padding: 5px 12px; background: rgba(255,255,255,0.2); border-radius: 20px; font-size: 0.85rem;">
                    Sıra: <?php echo $stat['sort_order']; ?>
                </span>
            </div>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="statistics.php?edit=<?php echo $stat['id']; ?>" class="btn btn-sm" style="background: white; color: var(--primary);">
                    <i class="fas fa-edit"></i> Düzenle
                </a>
                <a href="statistics.php?delete=<?php echo $stat['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                    <i class="fas fa-trash"></i> Sil
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz istatistik eklenmemiş</p>
    <?php endif; ?>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Popüler İkonlar</h2>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 15px;">
        <?php
        $popular_icons = [
            ['icon' => 'fa-calendar', 'label' => 'Takvim'],
            ['icon' => 'fa-ship', 'label' => 'Gemi'],
            ['icon' => 'fa-anchor', 'label' => 'Çıpa'],
            ['icon' => 'fa-users', 'label' => 'Kullanıcılar'],
            ['icon' => 'fa-globe', 'label' => 'Dünya'],
            ['icon' => 'fa-trophy', 'label' => 'Kupa'],
            ['icon' => 'fa-star', 'label' => 'Yıldız'],
            ['icon' => 'fa-chart-line', 'label' => 'Grafik'],
            ['icon' => 'fa-briefcase', 'label' => 'Çanta'],
            ['icon' => 'fa-award', 'label' => 'Ödül'],
            ['icon' => 'fa-medal', 'label' => 'Madalya'],
            ['icon' => 'fa-flag', 'label' => 'Bayrak'],
        ];
        
        foreach($popular_icons as $pi):
        ?>
        <div style="padding: 20px; background: white; border-radius: 8px; text-align: center; cursor: pointer; transition: all 0.3s; border: 2px solid var(--light);" 
             onclick="document.querySelector('[name=icon]').value='<?php echo $pi['icon']; ?>'; window.scrollTo({top: 0, behavior: 'smooth'});"
             onmouseover="this.style.borderColor='var(--accent)'; this.style.transform='translateY(-5px)';"
             onmouseout="this.style.borderColor='var(--light)'; this.style.transform='translateY(0)';">
            <div style="font-size: 2.5rem; color: var(--accent); margin-bottom: 10px;">
                <i class="fas <?php echo $pi['icon']; ?>"></i>
            </div>
            <div style="font-size: 0.8rem; color: #666;">
                <?php echo $pi['label']; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>