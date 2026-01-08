<?php
require_once 'header.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT flag_image FROM country_flags WHERE id = ?");
    $stmt->execute([$id]);
    $flag = $stmt->fetch();
    if ($flag && $flag['flag_image']) deleteFile($flag['flag_image']);
    $pdo->prepare("DELETE FROM country_flags WHERE id = ?")->execute([$id]);
    flash('success', 'Bayrak silindi!');
    redirect('/admin/flags.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $country_name = $_POST['country_name'];
    $flag_emoji = $_POST['flag_emoji'];
    $active = isset($_POST['active']) ? 1 : 0;
    $sort_order = $_POST['sort_order'];
    
    // Resim yükleme
    $flag_image = null;
    if (isset($_FILES['flag_image']) && $_FILES['flag_image']['error'] === UPLOAD_ERR_OK) {
        $flag_image = uploadFile($_FILES['flag_image'], 'flags');
        
        // Eski resmi sil
        if ($id) {
            $stmt = $pdo->prepare("SELECT flag_image FROM country_flags WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch();
            if ($old && $old['flag_image']) {
                deleteFile($old['flag_image']);
            }
        }
    }
    
    if ($id) {
        if ($flag_image) {
            $stmt = $pdo->prepare("UPDATE country_flags SET country_name=?, flag_emoji=?, flag_image=?, active=?, sort_order=? WHERE id=?");
            $stmt->execute([$country_name, $flag_emoji, $flag_image, $active, $sort_order, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE country_flags SET country_name=?, flag_emoji=?, active=?, sort_order=? WHERE id=?");
            $stmt->execute([$country_name, $flag_emoji, $active, $sort_order, $id]);
        }
        flash('success', 'Bayrak güncellendi!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO country_flags (country_name, flag_emoji, flag_image, active, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$country_name, $flag_emoji, $flag_image, $active, $sort_order]);
        flash('success', 'Bayrak eklendi!');
    }
    redirect('/admin/flags.php');
}

$edit_flag = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM country_flags WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_flag = $stmt->fetch();
}

$flags = $pdo->query("SELECT * FROM country_flags ORDER BY sort_order ASC")->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_flag ? 'Bayrak Düzenle' : 'Yeni Bayrak Ekle'; ?></h2>
    </div>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Bayrak Ekleme Yöntemleri:</strong><br>
            <strong>1. Emoji kullan:</strong> <a href="https://emojipedia.org/flags/" target="_blank" style="color: var(--accent);">Emojipedia Flags</a> sayfasından emoji kopyala<br>
            <strong>2. Resim yükle:</strong> Bayrak resmini bilgisayarınızdan yükleyin (PNG/JPG - Önerilen: 64x64px)
        </div>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <?php if($edit_flag): ?>
        <input type="hidden" name="id" value="<?php echo $edit_flag['id']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>Ülke Adı *</label>
            <input type="text" name="country_name" class="form-control" value="<?php echo $edit_flag ? htmlspecialchars($edit_flag['country_name']) : ''; ?>" required placeholder="Örn: Türkiye">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Bayrak Emoji</label>
                <input type="text" name="flag_emoji" class="form-control" value="<?php echo $edit_flag ? htmlspecialchars($edit_flag['flag_emoji']) : ''; ?>" placeholder="🇹🇷" style="font-size: 2rem;">
                <small style="color: #666;">İsteğe bağlı - Resim yoksa emoji gösterilir</small>
            </div>

            <div class="form-group">
                <label>Bayrak Resmi (PNG/JPG)</label>
                <input type="file" name="flag_image" class="form-control" accept="image/*" onchange="previewImage(this, 'flagPreview')">
                <small style="color: #666;">Önerilen boyut: 64x64px veya 128x128px</small>
                <div class="image-preview" id="flagPreview" style="width: 100px; height: 100px; margin-top: 10px;">
                    <?php if($edit_flag && $edit_flag['flag_image']): ?>
                    <img src="<?php echo UPLOAD_URL . $edit_flag['flag_image']; ?>" alt="" style="object-fit: contain;">
                    <?php elseif($edit_flag && $edit_flag['flag_emoji']): ?>
                    <div style="font-size: 4rem;"><?php echo $edit_flag['flag_emoji']; ?></div>
                    <?php else: ?>
                    <i class="fas fa-flag" style="font-size: 3rem; color: #ddd;"></i>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Sıra No</label>
                <input type="number" name="sort_order" class="form-control" value="<?php echo $edit_flag ? $edit_flag['sort_order'] : '0'; ?>" min="0">
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="active" value="1" <?php echo (!$edit_flag || $edit_flag['active']) ? 'checked' : ''; ?>>
                    <span>Aktif</span>
                </label>
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $edit_flag ? 'Güncelle' : 'Kaydet'; ?>
            </button>
            <?php if($edit_flag): ?>
            <a href="flags.php" class="btn btn-danger"><i class="fas fa-times"></i> İptal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Bayrak Listesi</h2>
    </div>

    <?php if(count($flags) > 0): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
        <?php foreach($flags as $flag): ?>
        <div style="background: var(--light); padding: 25px; border-radius: 10px; text-align: center; position: relative; transition: all 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="font-size: 4rem; margin-bottom: 15px; height: 80px; display: flex; align-items: center; justify-content: center;">
                <?php if($flag['flag_image']): ?>
                    <img src="<?php echo UPLOAD_URL . $flag['flag_image']; ?>" alt="<?php echo htmlspecialchars($flag['country_name']); ?>" style="max-width: 80px; max-height: 80px; object-fit: contain;">
                <?php else: ?>
                    <?php echo $flag['flag_emoji']; ?>
                <?php endif; ?>
            </div>
            <h3 style="color: var(--primary); margin-bottom: 10px; font-size: 1.2rem;">
                <?php echo htmlspecialchars($flag['country_name']); ?>
            </h3>
            <div style="display: flex; gap: 5px; justify-content: center; margin-bottom: 15px;">
                <span class="badge badge-<?php echo $flag['active'] ? 'success' : 'danger'; ?>">
                    <?php echo $flag['active'] ? 'Aktif' : 'Pasif'; ?>
                </span>
                <span class="badge badge-info">Sıra: <?php echo $flag['sort_order']; ?></span>
                <?php if($flag['flag_image']): ?>
                <span class="badge" style="background: #17a2b8; color: white;">
                    <i class="fas fa-image"></i> Resim
                </span>
                <?php else: ?>
                <span class="badge" style="background: #ffc107; color: #333;">
                    <i class="fas fa-smile"></i> Emoji
                </span>
                <?php endif; ?>
            </div>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <a href="flags.php?edit=<?php echo $flag['id']; ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="flags.php?delete=<?php echo $flag['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                    <i class="fas fa-trash"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz bayrak eklenmemiş</p>
    <?php endif; ?>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Popüler Bayraklar (Hızlı Ekle - Emoji)</h2>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 15px;">
        <?php
        $popular_flags = [
            ['name' => 'Türkiye', 'emoji' => '🇹🇷'],
            ['name' => 'ABD', 'emoji' => '🇺🇸'],
            ['name' => 'İngiltere', 'emoji' => '🇬🇧'],
            ['name' => 'Almanya', 'emoji' => '🇩🇪'],
            ['name' => 'Fransa', 'emoji' => '🇫🇷'],
            ['name' => 'İtalya', 'emoji' => '🇮🇹'],
            ['name' => 'İspanya', 'emoji' => '🇪🇸'],
            ['name' => 'Hollanda', 'emoji' => '🇳🇱'],
            ['name' => 'Belçika', 'emoji' => '🇧🇪'],
            ['name' => 'Yunanistan', 'emoji' => '🇬🇷'],
            ['name' => 'Portekiz', 'emoji' => '🇵🇹'],
            ['name' => 'Rusya', 'emoji' => '🇷🇺'],
            ['name' => 'Çin', 'emoji' => '🇨🇳'],
            ['name' => 'Japonya', 'emoji' => '🇯🇵'],
            ['name' => 'Hindistan', 'emoji' => '🇮🇳'],
            ['name' => 'Kanada', 'emoji' => '🇨🇦'],
        ];
        
        foreach($popular_flags as $pf):
        ?>
        <div style="padding: 15px; background: white; border-radius: 8px; text-align: center; cursor: pointer; transition: all 0.3s; border: 2px solid var(--light);" 
             onclick="document.querySelector('[name=country_name]').value='<?php echo $pf['name']; ?>'; document.querySelector('[name=flag_emoji]').value='<?php echo $pf['emoji']; ?>'; window.scrollTo({top: 0, behavior: 'smooth'});"
             onmouseover="this.style.borderColor='var(--accent)'; this.style.transform='scale(1.05)';"
             onmouseout="this.style.borderColor='var(--light)'; this.style.transform='scale(1)';">
            <div style="font-size: 2.5rem; margin-bottom: 8px;">
                <?php echo $pf['emoji']; ?>
            </div>
            <div style="font-size: 0.85rem; color: #666;">
                <?php echo $pf['name']; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>