

<?php
require_once 'header.php';

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM team WHERE id = ?");
    $stmt->execute([$id]);
    $team = $stmt->fetch();
    if ($team && $team['image']) deleteFile($team['image']);
    $pdo->prepare("DELETE FROM team WHERE id = ?")->execute([$id]);
    flash('success', 'Ekip üyesi başarıyla silindi!');
    redirect('/admin/team.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $position = $_POST['position'];
    $description = $_POST['description'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $linkedin = $_POST['linkedin'];
    $active = isset($_POST['active']) ? 1 : 0;
    $sort_order = $_POST['sort_order'];
    
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = uploadFile($_FILES['image'], 'team');
        if ($id) {
            $stmt = $pdo->prepare("SELECT image FROM team WHERE id = ?");
            $stmt->execute([$id]);
            $old = $stmt->fetch();
            if ($old && $old['image']) deleteFile($old['image']);
        }
    }
    
    if ($id) {
        if ($image) {
            $stmt = $pdo->prepare("UPDATE team SET name=?, position=?, description=?, email=?, phone=?, linkedin=?, image=?, active=?, sort_order=? WHERE id=?");
            $stmt->execute([$name, $position, $description, $email, $phone, $linkedin, $image, $active, $sort_order, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE team SET name=?, position=?, description=?, email=?, phone=?, linkedin=?, active=?, sort_order=? WHERE id=?");
            $stmt->execute([$name, $position, $description, $email, $phone, $linkedin, $active, $sort_order, $id]);
        }
        flash('success', 'Ekip üyesi başarıyla güncellendi!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO team (name, position, description, email, phone, linkedin, image, active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $position, $description, $email, $phone, $linkedin, $image, $active, $sort_order]);
        flash('success', 'Ekip üyesi başarıyla eklendi!');
    }
    redirect('/admin/team.php');
}

$edit_team = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM team WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_team = $stmt->fetch();
}

$stmt = $pdo->query("SELECT * FROM team ORDER BY sort_order ASC");
$team_list = $stmt->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_team ? 'Ekip Üyesi Düzenle' : 'Yeni Ekip Üyesi Ekle'; ?></h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <?php if($edit_team): ?>
        <input type="hidden" name="id" value="<?php echo $edit_team['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Ad Soyad *</label>
                <input type="text" name="name" class="form-control" value="<?php echo $edit_team ? htmlspecialchars($edit_team['name']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Pozisyon *</label>
                <input type="text" name="position" class="form-control" value="<?php echo $edit_team ? htmlspecialchars($edit_team['position']) : ''; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Açıklama</label>
            <textarea name="description" class="form-control" rows="4"><?php echo $edit_team ? htmlspecialchars($edit_team['description']) : ''; ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>E-posta</label>
                <input type="email" name="email" class="form-control" value="<?php echo $edit_team ? htmlspecialchars($edit_team['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label>Telefon</label>
                <input type="text" name="phone" class="form-control" value="<?php echo $edit_team ? htmlspecialchars($edit_team['phone']) : ''; ?>">
            </div>
        </div>

        <div class="form-group">
            <label>LinkedIn Profili</label>
            <input type="url" name="linkedin" class="form-control" value="<?php echo $edit_team ? htmlspecialchars($edit_team['linkedin']) : ''; ?>">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Fotoğraf</label>
                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                <div class="image-preview" id="imagePreview">
                    <?php if($edit_team && $edit_team['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $edit_team['image']; ?>" alt="">
                    <?php else: ?>
                    <i class="fas fa-user" style="font-size: 3rem; color: #ddd;"></i>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <div class="form-group">
                    <label>Sıra No</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo $edit_team ? $edit_team['sort_order'] : '0'; ?>" min="0">
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="active" value="1" <?php echo (!$edit_team || $edit_team['active']) ? 'checked' : ''; ?>>
                        <span>Aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $edit_team ? 'Güncelle' : 'Kaydet'; ?>
            </button>
            <?php if($edit_team): ?>
            <a href="team.php" class="btn btn-danger"><i class="fas fa-times"></i> İptal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Ekip Listesi</h2>
    </div>

    <?php if(count($team_list) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Fotoğraf</th>
                <th>Ad Soyad</th>
                <th>Pozisyon</th>
                <th>İletişim</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($team_list as $member): ?>
            <tr>
                <td>
                    <?php if($member['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $member['image']; ?>" alt="" style="border-radius: 50%;">
                    <?php else: ?>
                    <div style="width: 60px; height: 60px; background: var(--light); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user" style="font-size: 1.5rem; color: #999;"></i>
                    </div>
                    <?php endif; ?>
                </td>
                <td><strong><?php echo htmlspecialchars($member['name']); ?></strong></td>
                <td><?php echo htmlspecialchars($member['position']); ?></td>
                <td>
                    <?php if($member['email']): ?>
                    <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($member['email']); ?></div>
                    <?php endif; ?>
                    <?php if($member['phone']): ?>
                    <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($member['phone']); ?></div>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge badge-<?php echo $member['active'] ? 'success' : 'danger'; ?>">
                        <?php echo $member['active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
                <td>
                    <a href="team.php?edit=<?php echo $member['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="team.php?delete=<?php echo $member['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz ekip üyesi eklenmemiş</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
