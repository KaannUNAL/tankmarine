<?php
require_once 'header.php';

// Form alanlarını çek
$stmt = $pdo->query("SELECT * FROM contact_form_fields WHERE is_active = 1 ORDER BY sort_order ASC");
$form_fields = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    $name = '';
    $email = '';
    $phone = '';
    $subject = '';
    $message = '';
    
    // Dinamik alanları topla
    foreach($form_fields as $field) {
        $field_name = $field['field_name'];
        $value = $_POST[$field_name] ?? '';
        
        // Ana değişkenlere ata
        if($field_name == 'name') $name = $value;
        if($field_name == 'email') $email = $value;
        if($field_name == 'phone') $phone = $value;
        if($field_name == 'subject') $subject = $value;
        if($field_name == 'message') $message = $value;
    }
    
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $phone, $subject, $message])) {
        flash('success', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
        redirect('/contact.php');
    }
}

$settings = getSettings();
?>

<style>
    .page-header {
        background: linear-gradient(rgba(0, 26, 51, 0.8), rgba(0, 51, 102, 0.8)), 
                    url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 300"><rect fill="%23003366" width="1200" height="300"/></svg>');
        padding: 100px 20px 60px;
        text-align: center;
        color: white;
    }

    .page-header h1 {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .contact-section {
        padding: 80px 20px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
    }

    .contact-form {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--primary);
        font-weight: 600;
    }

    .form-group label .required {
        color: var(--danger);
        margin-left: 3px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 1rem;
        transition: border 0.3s;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--accent);
    }

    .submit-btn {
        width: 100%;
        padding: 15px;
        background: var(--accent);
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .submit-btn:hover {
        background: var(--secondary);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .contact-info-box {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .info-card {
        background: white;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        display: flex;
        gap: 20px;
        align-items: start;
        transition: transform 0.3s;
    }

    .info-card:hover {
        transform: translateX(10px);
    }

    .info-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, var(--primary), var(--accent));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .info-content h3 {
        color: var(--primary);
        margin-bottom: 10px;
    }

    .info-content p {
        color: #666;
        line-height: 1.6;
    }

    .info-content a {
        color: var(--accent);
        text-decoration: none;
    }

    .info-content a:hover {
        text-decoration: underline;
    }

    .map-container {
        margin-top: 50px;
        height: 450px;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .alert {
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2rem;
        }

        .contact-grid {
            grid-template-columns: 1fr;
        }

        .contact-form {
            padding: 25px;
        }
    }
</style>

<div class="page-header">
    <h1>İletişim</h1>
    <div style="display: flex; justify-content: center; gap: 10px; align-items: center; color: #ddd; margin-top: 15px;">
        <a href="index.php" style="color: white; text-decoration: none;">Anasayfa</a>
        <i class="fas fa-angle-right"></i>
        <span>İletişim</span>
    </div>
</div>

<div class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Form -->
            <div>
                <div class="contact-form">
                    <h2 style="color: var(--primary); margin-bottom: 10px;">Bize Ulaşın</h2>
                    <p style="color: #666; margin-bottom: 30px;">Formu doldurarak bizimle iletişime geçebilirsiniz.</p>

                    <?php if(flash('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo flash('success'); ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <?php foreach($form_fields as $field): ?>
                        <div class="form-group">
                            <label>
                                <?php echo htmlspecialchars($field['field_label']); ?>
                                <?php if($field['is_required']): ?>
                                <span class="required">*</span>
                                <?php endif; ?>
                            </label>
                            
                            <?php if($field['field_type'] == 'textarea'): ?>
                            <textarea 
                                name="<?php echo $field['field_name']; ?>" 
                                rows="5" 
                                placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>"
                                <?php echo $field['is_required'] ? 'required' : ''; ?>></textarea>
                            <?php else: ?>
                            <input 
                                type="<?php echo $field['field_type']; ?>" 
                                name="<?php echo $field['field_name']; ?>" 
                                placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>"
                                <?php echo $field['is_required'] ? 'required' : ''; ?>>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>

                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Mesajı Gönder
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="contact-info-box">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h3>Adres</h3>
                        <p><?php echo nl2br(htmlspecialchars($settings['address'])); ?></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h3>Telefon</h3>
                        <p><a href="tel:<?php echo $settings['phone']; ?>"><?php echo htmlspecialchars($settings['phone']); ?></a></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h3>E-posta</h3>
                        <p><a href="mailto:<?php echo $settings['email']; ?>"><?php echo htmlspecialchars($settings['email']); ?></a></p>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-content">
                        <h3>Çalışma Saatleri</h3>
                        <p>
                            Pazartesi - Cuma: 09:00 - 18:00<br>
                            Cumartesi: 09:00 - 14:00<br>
                            Pazar: Kapalı
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map -->
        <?php if($settings['map_embed']): ?>
        <div class="map-container">
            <iframe 
                src="<?php echo htmlspecialchars($settings['map_embed']); ?>" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>