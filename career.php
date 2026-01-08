<?php
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $career_id = $_POST['career_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $cover_letter = $_POST['cover_letter'];
    
    // CV dosyası yükle
    $cv_file = uploadFile($_FILES['cv_file'], 'cv');
    
    if ($cv_file) {
        $stmt = $pdo->prepare("INSERT INTO career_applications (career_id, name, email, phone, cv_file, cover_letter) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$career_id, $name, $email, $phone, $cv_file, $cover_letter]);
        
        flash('success', 'Başvurunuz başarıyla alındı!');
        redirect('/career.php');
    } else {
        flash('error', 'CV dosyası yüklenirken hata oluştu.');
    }
}

$stmt = $pdo->query("SELECT * FROM careers WHERE active = 1 AND (deadline IS NULL OR deadline >= CURDATE()) ORDER BY created_at DESC");
$careers = $stmt->fetchAll();
?>

<style>
    .page-header { background: linear-gradient(rgba(0, 26, 51, 0.8), rgba(0, 51, 102, 0.8)), url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 300"><rect fill="%23003366" width="1200" height="300"/></svg>'); padding: 100px 20px 60px; text-align: center; color: white; }
    .career-list { padding: 80px 20px; max-width: 1000px; margin: 0 auto; }
    .career-card { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 25px; border-left: 4px solid var(--accent); }
    .career-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px; }
    .career-title { font-size: 1.5rem; color: var(--primary); margin-bottom: 10px; }
    .career-meta { display: flex; gap: 20px; color: #666; font-size: 0.9rem; flex-wrap: wrap; }
    .apply-btn { padding: 10px 30px; background: var(--accent); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600; transition: background 0.3s; }
    .apply-btn:hover { background: var(--secondary); }
    .modal { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; align-items: center; justify-content: center; padding: 20px; }
    .modal.active { display: flex; }
    .modal-content { background: white; padding: 40px; border-radius: 10px; max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; color: var(--primary); font-weight: 600; }
    .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 5px; font-size: 1rem; }
    .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent); }
    .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    @media (max-width: 768px) {
        .career-header { flex-direction: column; gap: 15px; }
        .career-meta { flex-direction: column; gap: 10px; }
    }
</style>

<div class="page-header">
    <h1>Kariyer</h1>
    <div style="display: flex; justify-content: center; gap: 10px; align-items: center; color: #ddd; margin-top: 15px;">
        <a href="index.php" style="color: white; text-decoration: none;">Anasayfa</a>
        <i class="fas fa-angle-right"></i>
        <span>Kariyer</span>
    </div>
</div>

<div class="career-list">
    <?php if(flash('success')): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?php echo flash('success'); ?>
    </div>
    <?php endif; ?>

    <?php if(count($careers) > 0): ?>
        <?php foreach($careers as $career): ?>
        <div class="career-card">
            <div class="career-header">
                <div>
                    <h3 class="career-title"><?php echo htmlspecialchars($career['title']); ?></h3>
                    <div class="career-meta">
                        <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($career['department']); ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($career['location']); ?></span>
                        <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($career['type']); ?></span>
                        <?php if($career['deadline']): ?>
                        <span><i class="fas fa-calendar"></i> Son Başvuru: <?php echo date('d.m.Y', strtotime($career['deadline'])); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <button class="apply-btn" onclick="openApplication(<?php echo $career['id']; ?>, '<?php echo htmlspecialchars($career['title']); ?>')">
                    <i class="fas fa-paper-plane"></i> Başvur
                </button>
            </div>
            <div style="color: #666; line-height: 1.8;">
                <h4 style="color: var(--primary); margin: 20px 0 10px;">İş Tanımı:</h4>
                <?php echo nl2br(htmlspecialchars($career['description'])); ?>
                
                <h4 style="color: var(--primary); margin: 20px 0 10px;">Aranan Nitelikler:</h4>
                <?php echo nl2br(htmlspecialchars($career['requirements'])); ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #666;">
            <i class="fas fa-briefcase" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <p>Şu anda açık pozisyon bulunmamaktadır.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Başvuru Formu Modal -->
<div class="modal" id="applicationModal">
    <div class="modal-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h2 style="color: var(--primary);" id="modalTitle">Başvuru Formu</h2>
            <button onclick="closeApplication()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: #999;">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="career_id" id="careerId">
            
            <div class="form-group">
                <label>Ad Soyad *</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label>E-posta *</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Telefon *</label>
                <input type="tel" name="phone" required>
            </div>

            <div class="form-group">
                <label>CV Dosyası (PDF) *</label>
                <input type="file" name="cv_file" accept=".pdf" required>
            </div>

            <div class="form-group">
                <label>Ön Yazı</label>
                <textarea name="cover_letter" rows="5" placeholder="Kendinizi tanıtın..."></textarea>
            </div>

            <button type="submit" class="apply-btn" style="width: 100%;">
                <i class="fas fa-paper-plane"></i> Başvuruyu Gönder
            </button>
        </form>
    </div>
</div>

<script>
    function openApplication(careerId, careerTitle) {
        document.getElementById('careerId').value = careerId;
        document.getElementById('modalTitle').textContent = careerTitle + ' - Başvuru Formu';
        document.getElementById('applicationModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeApplication() {
        document.getElementById('applicationModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    document.getElementById('applicationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeApplication();
        }
    });
</script>

<?php require_once 'footer.php'; ?>