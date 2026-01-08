<?php
require_once 'header.php';

// İstatistikleri çek
$fleet_count = $pdo->query("SELECT COUNT(*) FROM fleet WHERE active = 1")->fetchColumn();
$news_count = $pdo->query("SELECT COUNT(*) FROM news WHERE active = 1")->fetchColumn();
$gallery_count = $pdo->query("SELECT COUNT(*) FROM gallery WHERE active = 1")->fetchColumn();
$messages_count = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
$applications_count = $pdo->query("SELECT COUNT(*) FROM career_applications WHERE status = 'pending'")->fetchColumn();

// Son mesajlar
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recent_messages = $stmt->fetchAll();

// Son başvurular
$stmt = $pdo->query("SELECT ca.*, c.title as career_title FROM career_applications ca 
                     LEFT JOIN careers c ON ca.career_id = c.id 
                     ORDER BY ca.created_at DESC LIMIT 5");
$recent_applications = $stmt->fetchAll();

// Son haberler
$stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
$recent_news = $stmt->fetchAll();
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-ship"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $fleet_count; ?></h3>
            <p>Aktif Gemi</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $news_count; ?></h3>
            <p>Yayınlanan Haber</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-images"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $gallery_count; ?></h3>
            <p>Galeri Fotoğrafı</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $messages_count; ?></h3>
            <p>Yeni Mesaj</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
    <div class="content-card">
        <div class="content-header">
            <h2>Son Mesajlar</h2>
            <a href="messages.php" class="btn btn-primary btn-sm">
                Tümünü Gör
            </a>
        </div>

        <?php if(count($recent_messages) > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach($recent_messages as $message): ?>
            <div style="padding: 15px; background: var(--light); border-radius: 8px; border-left: 4px solid <?php echo $message['status'] == 'new' ? 'var(--accent)' : '#ddd'; ?>;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <strong style="color: var(--primary);"><?php echo htmlspecialchars($message['name']); ?></strong>
                    <span class="badge badge-<?php echo $message['status'] == 'new' ? 'success' : 'warning'; ?>">
                        <?php echo $message['status'] == 'new' ? 'Yeni' : 'Okundu'; ?>
                    </span>
                </div>
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">
                    <strong>Konu:</strong> <?php echo htmlspecialchars($message['subject']); ?>
                </div>
                <div style="font-size: 0.85rem; color: #999;">
                    <i class="fas fa-clock"></i> <?php echo date('d.m.Y H:i', strtotime($message['created_at'])); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="text-align: center; color: #999; padding: 20px;">Henüz mesaj yok</p>
        <?php endif; ?>
    </div>

    <div class="content-card">
        <div class="content-header">
            <h2>Son Başvurular</h2>
            <a href="applications.php" class="btn btn-primary btn-sm">
                Tümünü Gör
            </a>
        </div>

        <?php if(count($recent_applications) > 0): ?>
        <div style="display: flex; flex-direction: column; gap: 15px;">
            <?php foreach($recent_applications as $app): ?>
            <div style="padding: 15px; background: var(--light); border-radius: 8px; border-left: 4px solid <?php echo $app['status'] == 'pending' ? 'var(--warning)' : '#ddd'; ?>;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <strong style="color: var(--primary);"><?php echo htmlspecialchars($app['name']); ?></strong>
                    <span class="badge badge-<?php echo $app['status'] == 'pending' ? 'warning' : 'success'; ?>">
                        <?php 
                        $status_labels = [
                            'pending' => 'Bekliyor',
                            'reviewed' => 'İncelendi',
                            'accepted' => 'Kabul',
                            'rejected' => 'Red'
                        ];
                        echo $status_labels[$app['status']];
                        ?>
                    </span>
                </div>
                <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;">
                    <strong>Pozisyon:</strong> <?php echo htmlspecialchars($app['career_title']); ?>
                </div>
                <div style="font-size: 0.85rem; color: #999;">
                    <i class="fas fa-clock"></i> <?php echo date('d.m.Y H:i', strtotime($app['created_at'])); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="text-align: center; color: #999; padding: 20px;">Henüz başvuru yok</p>
        <?php endif; ?>
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Son Haberler</h2>
        <a href="news.php" class="btn btn-primary btn-sm">
            Tümünü Gör
        </a>
    </div>

    <?php if(count($recent_news) > 0): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>Görsel</th>
                <th>Başlık</th>
                <th>Görüntülenme</th>
                <th>Tarih</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($recent_news as $news): ?>
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
                <td><?php echo htmlspecialchars($news['title']); ?></td>
                <td><?php echo $news['views']; ?></td>
                <td><?php echo date('d.m.Y', strtotime($news['created_at'])); ?></td>
                <td>
                    <span class="badge badge-<?php echo $news['active'] ? 'success' : 'danger'; ?>">
                        <?php echo $news['active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p style="text-align: center; color: #999; padding: 20px;">Henüz haber yok</p>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>