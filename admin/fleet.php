<?php
require_once 'header.php';

// Silme işlemi
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM fleet WHERE id = ?");
    $stmt->execute([$id]);
    $fleet = $stmt->fetch();
    
    if ($fleet && $fleet['image']) {
        deleteFile($fleet['image']);
    }
    
    $pdo->prepare("DELETE FROM fleet WHERE id = ?")->execute([$id]);
    flash('success', 'Gemi başarıyla silindi!');
    redirect('/admin/fleet.php');
}

// Ekleme/Güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $type = $_POST['type'];
    $dwt = $_POST['dwt'];
    $built_year = $_POST['built_year'];
    $flag = $_POST['flag'];
    $length = $_POST['length'];
    $beam = $_POST['beam'];
    $draft = $_POST['draft'];
    $engine = $_POST['engine'];
    $speed = $_POST['speed'];
    $description = $_POST['description'];
    $active = isset($_POST['active']) ? 1 : 0;
    $sort_order = $_POST['sort_order'];
    
    // Resim yükleme
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = uploadFile($_FILES['image'], 'fleet');
        
        // Eski resmi sil
        if ($id) {
            $stmt = $pdo->prepare("SELECT image FROM fleet WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch();
            if ($old && $old['image']) {
                deleteFile($old['image']);
            }
        }
    }
    
    if ($id) {
        // Güncelleme
        if ($image) {
            $stmt = $pdo->prepare("UPDATE fleet SET name=?, type=?, dwt=?, built_year=?, flag=?, length=?, beam=?, draft=?, engine=?, speed=?, description=?, image=?, active=?, sort_order=? WHERE id=?");
            $stmt->execute([$name, $type, $dwt, $built_year, $flag, $length, $beam, $draft, $engine, $speed, $description, $image, $active, $sort_order, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE fleet SET name=?, type=?, dwt=?, built_year=?, flag=?, length=?, beam=?, draft=?, engine=?, speed=?, description=?, active=?, sort_order=? WHERE id=?");
            $stmt->execute([$name, $type, $dwt, $built_year, $flag, $length, $beam, $draft, $engine, $speed, $description, $active, $sort_order, $id]);
        }
        flash('success', 'Gemi başarıyla güncellendi!');
    } else {
        // Ekleme
        $stmt = $pdo->prepare("INSERT INTO fleet (name, type, dwt, built_year, flag, length, beam, draft, engine, speed, description, image, active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $type, $dwt, $built_year, $flag, $length, $beam, $draft, $engine, $speed, $description, $image, $active, $sort_order]);
        flash('success', 'Gemi başarıyla eklendi!');
    }
    
    redirect('/admin/fleet.php');
}

// Düzenleme için veri çek
$edit_fleet = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM fleet WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_fleet = $stmt->fetch();
}

// Listeyi çek
$stmt = $pdo->query("SELECT * FROM fleet ORDER BY sort_order ASC, created_at DESC");
$fleet_list = $stmt->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo flash('success'); ?>
</div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_fleet ? 'Gemi Düzenle' : 'Yeni Gemi Ekle'; ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <?php if($edit_fleet): ?>
        <input type="hidden" name="id" value="<?php echo $edit_fleet['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Gemi Adı *</label>
                <input type="text" name="name" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['name']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Tip *</label>
                <input type="text" name="type" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['type']) : ''; ?>" required placeholder="Örn: Kimyasal Tanker">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>DWT *</label>
                <input type="text" name="dwt" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['dwt']) : ''; ?>" required placeholder="Örn: 12,000 DWT">
            </div>

            <div class="form-group">
                <label>İnşa Yılı *</label>
                <input type="text" name="built_year" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['built_year']) : ''; ?>" required placeholder="Örn: 2020">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Bayrak *</label>
                <input type="text" name="flag" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['flag']) : ''; ?>" required placeholder="Örn: Türk Bayrağı">
            </div>

            <div class="form-group">
                <label>Uzunluk</label>
                <input type="text" name="length" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['length']) : ''; ?>" placeholder="Örn: 120m">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Genişlik</label>
                <input type="text" name="beam" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['beam']) : ''; ?>" placeholder="Örn: 20m">
            </div>

            <div class="form-group">
                <label>Draft</label>
                <input type="text" name="draft" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['draft']) : ''; ?>" placeholder="Örn: 8.5m">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Motor</label>
                <input type="text" name="engine" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['engine']) : ''; ?>" placeholder="Örn: MAN B&W">
            </div>

            <div class="form-group">
                <label>Hız</label>
                <input type="text" name="speed" class="form-control" value="<?php echo $edit_fleet ? htmlspecialchars($edit_fleet['speed']) : ''; ?>" placeholder="Örn: 14 knot">
            </div>
        </div>

        <div class="form-group">
            <label>Açıklama</label>
            <textarea name="description" class="form-control" rows="4"><?php echo $edit_fleet ? htmlspecialchars($edit_fleet['description']) : ''; ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Görsel</label>
                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                <div class="image-preview" id="imagePreview">
                    <?php if($edit_fleet && $edit_fleet['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $edit_fleet['image']; ?>" alt="">
                    <?php else: ?>
                    <i class="fas fa-image" style="font-size: 3rem; color: #ddd;"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label>Sıra No</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo $edit_fleet ? $edit_fleet['sort_order'] : '0'; ?>" min="0">
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="active" value="1" <?php echo (!$edit_fleet || $edit_fleet['active']) ? 'checked' : ''; ?>>
                        <span>Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $edit_fleet ? 'Güncelle' : 'Kaydet'; ?>
            </button>
            <?php if($edit_fleet): ?>
            <a href="fleet.php" class="btn btn-danger">
                <i class="fas fa-times"></i> İptal
            </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Filo Listesi</h2>
    </div>

    <?php if(count($fleet_list) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Görsel</th>
                <th>Gemi Adı</th>
                <th>Tip</th>
                <th>DWT</th>
                <th>Yıl</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($fleet_list as $ship): ?>
            <tr>
                <td>
                    <?php if($ship['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $ship['image']; ?>" alt="">
                    <?php else: ?>
                    <div style="width: 60px; height: 60px; background: var(--light); border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-ship" style="font-size: 1.5rem; color: #999;"></i>
                    </div>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($ship['name']); ?></strong></td>
                <td><?php echo htmlspecialchars($ship['type']); ?></td>
                <td><?php echo htmlspecialchars($ship['dwt']); ?></td>
                <td><?php echo htmlspecialchars($ship['built_year']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $ship['active'] ? 'success' : 'danger'; ?>">
                        <?php echo $ship['active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
                <td>
                    <a href="fleet.php?edit=<?php echo $ship['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="fleet.php?delete=<?php echo $ship['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz gemi eklenmemiş</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>