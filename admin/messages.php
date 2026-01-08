<?php
require_once 'header.php';

// Mesaj detayı
$view_message = null;
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->execute([$_GET['view']]);
    $view_message = $stmt->fetch();
    
    // Okundu olarak işaretle
    if ($view_message && $view_message['status'] == 'new') {
        $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?")->execute([$_GET['view']]);
    }
}

// Silme
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$_GET['delete']]);
    flash('success', 'Mesaj başarıyla silindi!');
    redirect('/admin/messages.php');
}

// Durum güncelleme
if (isset($_POST['update_status'])) {
    $id = $_POST['message_id'];
    $status = $_POST['status'];
    $pdo->prepare("UPDATE contact_messages SET status = ? WHERE id = ?")->execute([$status, $id]);
    flash('success', 'Durum güncellendi!');
    redirect('/admin/messages.php?view=' . $id);
}

// Tüm mesajlar
$filter = $_GET['filter'] ?? 'all';
if ($filter == 'new') {
    $stmt = $pdo->query("SELECT * FROM contact_messages WHERE status = 'new' ORDER BY created_at DESC");
} elseif ($filter == 'read') {
    $stmt = $pdo->query("SELECT * FROM contact_messages WHERE status = 'read' ORDER BY created_at DESC");
} elseif ($filter == 'replied') {
    $stmt = $pdo->query("SELECT * FROM contact_messages WHERE status = 'replied' ORDER BY created_at DESC");
} else {
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
}
$messages = $stmt->fetchAll();

// İstatistikler
$new_count = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
$read_count = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'read'")->fetchColumn();
$replied_count = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'replied'")->fetchColumn();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<?php if($view_message): ?>
<!-- Mesaj Detay -->
<div class="content-card">
    <div class="content-header">
        <h2>Mesaj Detayı</h2>
        <a href="messages.php" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>

    <div style="background: var(--light); padding: 25px; border-radius: 10px; margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <strong style="color: var(--primary);">Gönderen:</strong>
                <p style="margin-top: 5px; font-size: 1.1rem;"><?php echo htmlspecialchars($view_message['name']); ?></p>
            </div>
            <div>
                <strong style="color: var(--primary);">Tarih:</strong>
                <p style="margin-top: 5px;"><?php echo date('d.m.Y H:i', strtotime($view_message['created_at'])); ?></p>
            </div>
            <div>
                <strong style="color: var(--primary);">E-posta:</strong>
                <p style="margin-top: 5px;">
                    <a href="mailto:<?php echo $view_message['email']; ?>" style="color: var(--accent);">
                        <?php echo htmlspecialchars($view_message['email']); ?>
                    </a>
                </p>
            </div>
            <div>
                <strong style="color: var(--primary);">Telefon:</strong>
                <p style="margin-top: 5px;">
                    <a href="tel:<?php echo $view_message['phone']; ?>" style="color: var(--accent);">
                        <?php echo htmlspecialchars($view_message['phone']); ?>
                    </a>
                </p>
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <strong style="color: var(--primary);">Konu:</strong>
            <p style="margin-top: 5px; font-size: 1.1rem; font-weight: 600;">
                <?php echo htmlspecialchars($view_message['subject']); ?>
            </p>
        </div>

        <div>
            <strong style="color: var(--primary);">Mesaj:</strong>
            <p style="margin-top: 10px; line-height: 1.8; white-space: pre-wrap;">
                <?php echo htmlspecialchars($view_message['message']); ?>
            </p>
        </div>
    </div>

    <form method="POST" style="display: flex; gap: 10px; align-items: center;">
        <input type="hidden" name="message_id" value="<?php echo $view_message['id']; ?>">
        <select name="status" class="form-control" style="width: auto;">
            <option value="new" <?php echo $view_message['status'] == 'new' ? 'selected' : ''; ?>>Yeni</option>
            <option value="read" <?php echo $view_message['status'] == 'read' ? 'selected' : ''; ?>>Okundu</option>
            <option value="replied" <?php echo $view_message['status'] == 'replied' ? 'selected' : ''; ?>>Cevaplandı</option>
        </select>
        <button type="submit" name="update_status" class="btn btn-success">
            <i class="fas fa-save"></i> Durumu Güncelle
        </button>
        <a href="mailto:<?php echo $view_message['email']; ?>?subject=Re: <?php echo urlencode($view_message['subject']); ?>" class="btn btn-primary">
            <i class="fas fa-reply"></i> E-posta ile Cevapla
        </a>
        <a href="messages.php?delete=<?php echo $view_message['id']; ?>" class="btn btn-danger" onclick="return confirmDelete()">
            <i class="fas fa-trash"></i> Sil
        </a>
    </form>
</div>

<?php else: ?>
<!-- Mesaj Listesi -->
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $new_count; ?></h3>
            <p>Yeni Mesaj</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-envelope-open"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $read_count; ?></h3>
            <p>Okundu</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $replied_count; ?></h3>
            <p>Cevaplandı</p>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Mesajlar</h2>
        <div style="display: flex; gap: 10px;">
            <a href="messages.php?filter=all" class="btn <?php echo $filter == 'all' ? 'btn-primary' : 'btn-warning'; ?> btn-sm">Tümü</a>
            <a href="messages.php?filter=new" class="btn <?php echo $filter == 'new' ? 'btn-primary' : 'btn-warning'; ?> btn-sm">Yeni</a>
            <a href="messages.php?filter=read" class="btn <?php echo $filter == 'read' ? 'btn-primary' : 'btn-warning'; ?> btn-sm">Okundu</a>
            <a href="messages.php?filter=replied" class="btn <?php echo $filter == 'replied' ? 'btn-primary' : 'btn-warning'; ?> btn-sm">Cevaplandı</a>
        </div>
    </div>

    <?php if(count($messages) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Gönderen</th>
                <th>Konu</th>
                <th>Tarih</th>
                <th>Durum</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($messages as $msg): ?>
            <tr style="<?php echo $msg['status'] == 'new' ? 'background: #fff3cd;' : ''; ?>">
                <td>
                    <strong><?php echo htmlspecialchars($msg['name']); ?></strong><br>
                    <small style="color: #666;"><?php echo htmlspecialchars($msg['email']); ?></small>
                </td>
                <td><?php echo htmlspecialchars(substr($msg['subject'], 0, 50)) . '...'; ?></td>
                <td><?php echo date('d.m.Y H:i', strtotime($msg['created_at'])); ?></td>
                <td>
                    <?php
                    $status_badge = [
                        'new' => 'success',
                        'read' => 'warning',
                        'replied' => 'primary'
                    ];
                    $status_text = [
                        'new' => 'Yeni',
                        'read' => 'Okundu',
                        'replied' => 'Cevaplandı'
                    ];
                    ?>
                    <span class="badge badge-<?php echo $status_badge[$msg['status']]; ?>">
                        <?php echo $status_text[$msg['status']]; ?>
                    </span>
                </td>
                <td>
                    <a href="messages.php?view=<?php echo $msg['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="messages.php?delete=<?php echo $msg['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirmDelete()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 40px;">
        <?php 
        if ($filter != 'all') {
            echo 'Bu kategoride mesaj yok';
        } else {
            echo 'Henüz mesaj yok';
        }
        ?>
    </p>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>