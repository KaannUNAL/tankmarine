<?php
/* admin/careers.php - Kariyer İlanları Yönetimi */
require_once 'header.php';

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM careers WHERE id = ?")->execute([$_GET['delete']]);
    flash('success', 'İlan başarıyla silindi!');
    redirect('/admin/careers.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = $_POST['title'];
    $department = $_POST['department'];
    $location = $_POST['location'];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];
    $deadline = $_POST['deadline'] ? $_POST['deadline'] : null;
    $active = isset($_POST['active']) ? 1 : 0;
    
    if ($id) {
        $stmt = $pdo->prepare("UPDATE careers SET title=?, department=?, location=?, type=?, description=?, requirements=?, deadline=?, active=? WHERE id=?");
        $stmt->execute([$title, $department, $location, $type, $description, $requirements, $deadline, $active, $id]);
        flash('success', 'İlan güncellendi!');
    } else {
        $stmt = $pdo->prepare("INSERT INTO careers (title, department, location, type, description, requirements, deadline, active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $department, $location, $type, $description, $requirements, $deadline, $active]);
        flash('success', 'İlan eklendi!');
    }
    redirect('/admin/careers.php');
}

$edit_career = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM careers WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_career = $stmt->fetch();
}

$stmt = $pdo->query("SELECT *, (SELECT COUNT(*) FROM career_applications WHERE career_id = careers.id) as application_count FROM careers ORDER BY created_at DESC");
$careers = $stmt->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_career ? 'İlan Düzenle' : 'Yeni İlan Ekle'; ?></h2>
    </div>

    <form method="POST">
        <?php if($edit_career): ?>
        <input type="hidden" name="id" value="<?php echo $edit_career['id']; ?>">
        <?php endif; ?>

        <div class="form-group">
            <label>İlan Başlığı *</label>
            <input type="text" name="title" class="form-control" value="<?php echo $edit_career ? htmlspecialchars($edit_career['title']) : ''; ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Departman *</label>
                <input type="text" name="department" class="form-control" value="<?php echo $edit_career ? htmlspecialchars($edit_career['department']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Lokasyon *</label>
                <input type="text" name="location" class="form-control" value="<?php echo $edit_career ? htmlspecialchars($edit_career['location']) : ''; ?>" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>İş Tipi *</label>
                <select name="type" class="form-control" required>
                    <option value="Tam Zamanlı" <?php echo ($edit_career && $edit_career['type'] == 'Tam Zamanlı') ? 'selected' : ''; ?>>Tam Zamanlı</option>
                    <option value="Yarı Zamanlı" <?php echo ($edit_career && $edit_career['type'] == 'Yarı Zamanlı') ? 'selected' : ''; ?>>Yarı Zamanlı</option>
                    <option value="Staj" <?php echo ($edit_career && $edit_career['type'] == 'Staj') ? 'selected' : ''; ?>>Staj</option>
                </select>
            </div>

            <div class="form-group">
                <label>Son Başvuru Tarihi</label>
                <input type="date" name="deadline" class="form-control" value="<?php echo $edit_career ? $edit_career['deadline'] : ''; ?>">
            </div>
        </div>

        <div class="form-group">
            <label>İş Tanımı *</label>
            <textarea name="description" class="form-control" rows="6" required><?php echo $edit_career ? htmlspecialchars($edit_career['description']) : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label>Aranan Nitelikler *</label>
            <textarea name="requirements" class="form-control" rows="6" required><?php echo $edit_career ? htmlspecialchars($edit_career['requirements']) : ''; ?></textarea>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                <input type="checkbox" name="active" value="1" <?php echo (!$edit_career || $edit_career['active']) ? 'checked' : ''; ?>>
                <span>Yayında</span>
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $edit_career ? 'Güncelle' : 'Yayınla'; ?>
            </button>
            <?php if($edit_career): ?>
            <a href="careers.php" class="btn btn-danger"><i class="fas fa-times"></i> İptal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Kariyer İlanları</h2>
    </div>

    <?php if(count($careers) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Başlık</th>
                <th>Departman</th>
                <th>Lokasyon</th>
                <th>Başvuru Sayısı</th>
                <th>Son Tarih</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($careers as $career): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($career['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($career['department']); ?></td>
                <td><?php echo htmlspecialchars($career['location']); ?></td>
                <td>
                    <a href="applications.php?career_id=<?php echo $career['id']; ?>" style="color: var(--accent); font-weight: 600;">
                        <?php echo $career['application_count']; ?> Başvuru
                    </a>
                </td>
                <td><?php echo $career['deadline'] ? date('d.m.Y', strtotime($career['deadline'])) : '-'; ?></td>
                <td>
                    <span class="badge badge-<?php echo $career['active'] ? 'success' : 'danger'; ?>">
                        <?php echo $career['active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
                <td>
                    <a href="careers.php?edit=<?php echo $career['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="careers.php?delete=<?php echo $career['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz ilan eklenmemiş</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>