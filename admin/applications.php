<?php
/* admin/applications.php - Kariyer Başvuruları */
require_once 'header.php';

// Durum güncelleme
if (isset($_POST['update_status'])) {
    $id = $_POST['application_id'];
    $status = $_POST['status'];
    $pdo->prepare("UPDATE career_applications SET status = ? WHERE id = ?")->execute([$status, $id]);
    flash('success', 'Durum güncellendi!');
    redirect('/admin/applications.php?view=' . $id);
}

// Silme
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT cv_file FROM career_applications WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $app = $stmt->fetch();
    if ($app && $app['cv_file']) deleteFile($app['cv_file']);
    $pdo->prepare("DELETE FROM career_applications WHERE id = ?")->execute([$_GET['delete']]);
    flash('success', 'Başvuru silindi!');
    redirect('/admin/applications.php');
}

// Detay
$view_app = null;
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare("SELECT ca.*, c.title as career_title FROM career_applications ca 
                          LEFT JOIN careers c ON ca.career_id = c.id WHERE ca.id = ?");
    $stmt->execute([$_GET['view']]);
    $view_app = $stmt->fetch();
}

// Liste
$filter = $_GET['career_id'] ?? null;
if ($filter) {
    $stmt = $pdo->prepare("SELECT ca.*, c.title as career_title FROM career_applications ca 
                          LEFT JOIN careers c ON ca.career_id = c.id WHERE ca.career_id = ? ORDER BY ca.created_at DESC");
    $stmt->execute([$filter]);
} else {
    $stmt = $pdo->query("SELECT ca.*, c.title as career_title FROM career_applications ca 
                        LEFT JOIN careers c ON ca.career_id = c.id ORDER BY ca.created_at DESC");
}
$applications = $stmt->fetchAll();

$stats = [
    'pending' => $pdo->query("SELECT COUNT(*) FROM career_applications WHERE status = 'pending'")->fetchColumn(),
    'reviewed' => $pdo->query("SELECT COUNT(*) FROM career_applications WHERE status = 'reviewed'")->fetchColumn(),
    'accepted' => $pdo->query("SELECT COUNT(*) FROM career_applications WHERE status = 'accepted'")->fetchColumn(),
    'rejected' => $pdo->query("SELECT COUNT(*) FROM career_applications WHERE status = 'rejected'")->fetchColumn(),
];
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<?php if($view_app): ?>
<!-- Başvuru Detay -->
<div class="content-card">
    <div class="content-header">
        <h2>Başvuru Detayı</h2>
        <a href="applications.php" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    <div style="background: var(--light); padding: 25px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <strong style="color: var(--primary);">Başvuran:</strong>
                <p style="margin-top: 5px; font-size: 1.1rem;"><?php echo htmlspecialchars($view_app['name']); ?></p>
            </div>
            <div>
                <strong style="color: var(--primary);">Pozisyon:</strong>
                <p style="margin-top: 5px; font-size: 1.1rem;"><?php echo htmlspecialchars($view_app['career_title']); ?></p>
            </div>
            <div>
                <strong style="color: var(--primary);">E-posta:</strong>
                <p style="margin-top: 5px;">
                    <a href="mailto:<?php echo $view_app['email']; ?>" style="color: var(--accent);">
                        <?php echo htmlspecialchars($view_app['email']); ?>
                    </a>
                </p>
            </div>
            <div>
                <strong style="color: var(--primary);">Telefon:</strong>
                <p style="margin-top: 5px;">
                    <a href="tel:<?php echo $view_app['phone']; ?>" style="color: var(--accent);">
                        <?php echo htmlspecialchars($view_app['phone']); ?>
                    </a>
                </p>
            </div>
            <div>
                <strong style="color: var(--primary);">CV:</strong>
                <p style="margin-top: 5px;">
                    <a href="<?php echo UPLOAD_URL . $view_app['cv_file']; ?>" target="_blank" class="btn btn-primary btn-sm">
                        <i class="fas fa-download"></i> CV'yi İndir
                    </a>
                </p>
            </div>
            <div>
                <strong style="color: var(--primary);">Başvuru Tarihi:</strong>
                <p style="margin-top: 5px;"><?php echo date('d.m.Y H:i', strtotime($view_app['created_at'])); ?></p>
            </div>
        </div>

        <?php if($view_app['cover_letter']): ?>
        <div>
            <strong style="color: var(--primary);">Ön Yazı:</strong>
            <p style="margin-top: 10px; line-height: 1.8; white-space: pre-wrap;">
                <?php echo htmlspecialchars($view_app['cover_letter']); ?>
            </p>
        </div>
        <?php endif; ?>
    </div>

    <form method="POST" style="display: flex; gap: 10px; align-items: center;">
        <input type="hidden" name="application_id" value="<?php echo $view_app['id']; ?>">
        <select name="status" class="form-control" style="width: auto;">
            <option value="pending" <?php echo $view_app['status'] == 'pending' ? 'selected' : ''; ?>>Bekliyor</option>
            <option value="reviewed" <?php echo $view_app['status'] == 'reviewed' ? 'selected' : ''; ?>>İncelendi</option>
            <option value="accepted" <?php echo $view_app['status'] == 'accepted' ? 'selected' : ''; ?>>Kabul Edildi</option>
            <option value="rejected" <?php echo $view_app['status'] == 'rejected' ? 'selected' : ''; ?>>Reddedildi</option>
        </select>
        <button type="submit" name="update_status" class="btn btn-success">
            <i class="fas fa-save"></i> Durumu Güncelle
        </button>
        <a href="applications.php?delete=<?php echo $view_app['id']; ?>" class="btn btn-danger" onclick="return confirmDelete()">
            <i class="fas fa-trash"></i> Sil
        </a>
    </form>
</div>

<?php else: ?>
<!-- Başvuru Listesi -->
<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['pending']; ?></h3>
            <p>Bekliyor</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-eye"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['reviewed']; ?></h3>
            <p>İncelendi</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['accepted']; ?></h3>
            <p>Kabul</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #dc3545, #c82333);"><i class="fas fa-times"></i></div>
        <div class="stat-info">
            <h3><?php echo $stats['rejected']; ?></h3>
            <p>Red</p>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Başvurular</h2>
    </div>

    <?php if(count($applications) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Başvuran</th>
                <th>Pozisyon</th>
                <th>Tarih</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($applications as $app): ?>
            <tr>
                <td>
                    <strong><?php echo htmlspecialchars($app['name']); ?></strong><br>
                    <small style="color: #666;"><?php echo htmlspecialchars($app['email']); ?></small>
                </td>
                <td><?php echo htmlspecialchars($app['career_title']); ?></td>
                <td><?php echo date('d.m.Y', strtotime($app['created_at'])); ?></td>
                <td>
                    <?php
                    $status_class = ['pending'=>'warning', 'reviewed'=>'info', 'accepted'=>'success', 'rejected'=>'danger'];
                    $status_text = ['pending'=>'Bekliyor', 'reviewed'=>'İncelendi', 'accepted'=>'Kabul', 'rejected'=>'Red'];
                    ?>
                    <span class="badge badge-<?php echo $status_class[$app['status']]; ?>">
                        <?php echo $status_text[$app['status']]; ?>
                    </span>
                </td>
                <td>
                    <a href="applications.php?view=<?php echo $app['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="<?php echo UPLOAD_URL . $app['cv_file']; ?>" target="_blank" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="applications.php?delete=<?php echo $app['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">Henüz başvuru yok</p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>