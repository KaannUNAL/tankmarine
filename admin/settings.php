<?php
require_once 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_name = $_POST['site_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $linkedin = $_POST['linkedin'];
    $instagram = $_POST['instagram'];
    $youtube = $_POST['youtube'];
    $map_embed = $_POST['map_embed'];
    $meta_title = $_POST['meta_title'];
    $meta_description = $_POST['meta_description'];
    $meta_keywords = $_POST['meta_keywords'];
    $google_analytics = $_POST['google_analytics'];
    
    // Logo yükleme
    $site_logo = null;
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
        $site_logo = uploadFile($_FILES['site_logo'], 'logo');
        $stmt = $pdo->prepare("SELECT site_logo FROM settings WHERE id = 1");
        $stmt->execute();
        $old = $stmt->fetch();
        if ($old && $old['site_logo']) deleteFile($old['site_logo']);
    }
    
    // Favicon yükleme
    $site_favicon = null;
    if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
        $site_favicon = uploadFile($_FILES['site_favicon'], 'logo');
        $stmt = $pdo->prepare("SELECT site_favicon FROM settings WHERE id = 1");
        $stmt->execute();
        $old = $stmt->fetch();
        if ($old && $old['site_favicon']) deleteFile($old['site_favicon']);
    }
    
    if ($site_logo && $site_favicon) {
        $stmt = $pdo->prepare("UPDATE settings SET 
            site_name=?, site_logo=?, site_favicon=?, phone=?, email=?, address=?,
            facebook=?, twitter=?, linkedin=?, instagram=?, youtube=?, map_embed=?,
            meta_title=?, meta_description=?, meta_keywords=?, google_analytics=?
            WHERE id=1");
        $stmt->execute([$site_name, $site_logo, $site_favicon, $phone, $email, $address, $facebook, $twitter, $linkedin, $instagram, $youtube, $map_embed, $meta_title, $meta_description, $meta_keywords, $google_analytics]);
    } elseif ($site_logo) {
        $stmt = $pdo->prepare("UPDATE settings SET 
            site_name=?, site_logo=?, phone=?, email=?, address=?,
            facebook=?, twitter=?, linkedin=?, instagram=?, youtube=?, map_embed=?,
            meta_title=?, meta_description=?, meta_keywords=?, google_analytics=?
            WHERE id=1");
        $stmt->execute([$site_name, $site_logo, $phone, $email, $address, $facebook, $twitter, $linkedin, $instagram, $youtube, $map_embed, $meta_title, $meta_description, $meta_keywords, $google_analytics]);
    } elseif ($site_favicon) {
        $stmt = $pdo->prepare("UPDATE settings SET 
            site_name=?, site_favicon=?, phone=?, email=?, address=?,
            facebook=?, twitter=?, linkedin=?, instagram=?, youtube=?, map_embed=?,
            meta_title=?, meta_description=?, meta_keywords=?, google_analytics=?
            WHERE id=1");
        $stmt->execute([$site_name, $site_favicon, $phone, $email, $address, $facebook, $twitter, $linkedin, $instagram, $youtube, $map_embed, $meta_title, $meta_description, $meta_keywords, $google_analytics]);
    } else {
        $stmt = $pdo->prepare("UPDATE settings SET 
            site_name=?, phone=?, email=?, address=?,
            facebook=?, twitter=?, linkedin=?, instagram=?, youtube=?, map_embed=?,
            meta_title=?, meta_description=?, meta_keywords=?, google_analytics=?
            WHERE id=1");
        $stmt->execute([$site_name, $phone, $email, $address, $facebook, $twitter, $linkedin, $instagram, $youtube, $map_embed, $meta_title, $meta_description, $meta_keywords, $google_analytics]);
    }
    
    flash('success', 'Ayarlar başarıyla güncellendi!');
    redirect('/admin/settings.php');
}

$settings = getSettings();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success">
    <i class="fas fa-check-circle"></i> <?php echo flash('success'); ?>
</div>
<?php endif; ?>

<div class="content-card">
    <div class="content-header">
        <h2>Site Ayarları</h2>
    </div>

    <form method="POST" enctype="multipart/form-data">
        
        <!-- Logo ve Branding -->
        <h3 style="color: var(--primary); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid var(--light);">
            <i class="fas fa-image"></i> Logo ve Marka
        </h3>

        <div class="form-group">
            <label>Site Adı *</label>
            <input type="text" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Site Logosu (PNG önerilir - Şeffaf arkaplan)</label>
                <input type="file" name="site_logo" class="form-control" accept="image/*" onchange="previewImage(this, 'logoPreview')">
                <small style="color: #666;">Önerilen boyut: 200x60px veya oransal</small>
                <div class="image-preview" id="logoPreview" style="width: 250px; height: 80px; margin-top: 10px; background: #f8f9fa;">
                    <?php if($settings['site_logo']): ?>
                    <img src="<?php echo UPLOAD_URL . $settings['site_logo']; ?>" alt="Logo" style="object-fit: contain; padding: 10px;">
                    <?php else: ?>
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;">
                        <i class="fas fa-image" style="font-size: 2rem;"></i>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label>Favicon (ICO veya PNG)</label>
                <input type="file" name="site_favicon" class="form-control" accept="image/*" onchange="previewImage(this, 'faviconPreview')">
                <small style="color: #666;">Önerilen boyut: 32x32px veya 64x64px</small>
                <div class="image-preview" id="faviconPreview" style="width: 64px; height: 64px; margin-top: 10px; background: #f8f9fa;">
                    <?php if($settings['site_favicon']): ?>
                    <img src="<?php echo UPLOAD_URL . $settings['site_favicon']; ?>" alt="Favicon" style="object-fit: contain; padding: 5px;">
                    <?php else: ?>
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;">
                        <i class="fas fa-star" style="font-size: 1.5rem;"></i>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <hr style="margin: 40px 0; border: none; border-top: 2px solid var(--light);">
        
        <!-- İletişim Bilgileri -->
        <h3 style="color: var(--primary); margin-bottom: 20px;">
            <i class="fas fa-address-book"></i> İletişim Bilgileri
        </h3>

        <div class="form-row">
            <div class="form-group">
                <label>Telefon</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($settings['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label>E-posta</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($settings['email']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Adres</label>
            <textarea name="address" class="form-control" rows="3" required><?php echo htmlspecialchars($settings['address']); ?></textarea>
        </div>

        <hr style="margin: 40px 0; border: none; border-top: 2px solid var(--light);">
        
        <!-- Sosyal Medya -->
        <h3 style="color: var(--primary); margin-bottom: 20px;">
            <i class="fas fa-share-alt"></i> Sosyal Medya Linkleri
        </h3>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fab fa-facebook"></i> Facebook</label>
                <input type="url" name="facebook" class="form-control" value="<?php echo htmlspecialchars($settings['facebook']); ?>">
            </div>

            <div class="form-group">
                <label><i class="fab fa-twitter"></i> Twitter</label>
                <input type="url" name="twitter" class="form-control" value="<?php echo htmlspecialchars($settings['twitter']); ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fab fa-linkedin"></i> LinkedIn</label>
                <input type="url" name="linkedin" class="form-control" value="<?php echo htmlspecialchars($settings['linkedin']); ?>">
            </div>

            <div class="form-group">
                <label><i class="fab fa-instagram"></i> Instagram</label>
                <input type="url" name="instagram" class="form-control" value="<?php echo htmlspecialchars($settings['instagram']); ?>">
            </div>
        </div>

        <div class="form-group">
            <label><i class="fab fa-youtube"></i> YouTube</label>
            <input type="url" name="youtube" class="form-control" value="<?php echo htmlspecialchars($settings['youtube']); ?>">
        </div>

        <hr style="margin: 40px 0; border: none; border-top: 2px solid var(--light);">
        
        <!-- Harita -->
        <h3 style="color: var(--primary); margin-bottom: 20px;">
            <i class="fas fa-map-marked-alt"></i> Google Maps Harita
        </h3>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Harita Embed Kodu Nasıl Alınır?</strong><br>
                1. <a href="https://maps.google.com" target="_blank" style="color: var(--accent);">Google Maps</a>'e gidin<br>
                2. Lokasyonunuzu bulun ve "Paylaş" butonuna tıklayın<br>
                3. "Haritayı göm" sekmesini seçin<br>
                4. "HTML'yi kopyala" butonuna tıklayın<br>
                5. Aşağıdaki alana yapıştırın (Sadece src="..." içindeki URL'i)
            </div>
        </div>

        <div class="form-group">
            <label>Harita Embed URL</label>
            <textarea name="map_embed" class="form-control" rows="3" placeholder="https://www.google.com/maps/embed?pb=..."><?php echo htmlspecialchars($settings['map_embed']); ?></textarea>
        </div>

        <?php if($settings['map_embed']): ?>
        <div style="margin-top: 15px; border-radius: 10px; overflow: hidden;">
            <iframe src="<?php echo htmlspecialchars($settings['map_embed']); ?>" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
        <?php endif; ?>

        <hr style="margin: 40px 0; border: none; border-top: 2px solid var(--light);">
        
        <!-- SEO Ayarları -->
        <h3 style="color: var(--primary); margin-bottom: 20px;">
            <i class="fas fa-search"></i> SEO Ayarları
        </h3>

        <div class="form-group">
            <label>Meta Title (Sayfa Başlığı)</label>
            <input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($settings['meta_title']); ?>" placeholder="Tankmarine Ship Management - Tanker İşletmeciliği">
            <small style="color: #666;">Google'da görünecek başlık (50-60 karakter önerilir)</small>
        </div>

        <div class="form-group">
            <label>Meta Description (Sayfa Açıklaması)</label>
            <textarea name="meta_description" class="form-control" rows="3" placeholder="Profesyonel tanker işletmeciliği ve denizcilik hizmetleri..."><?php echo htmlspecialchars($settings['meta_description']); ?></textarea>
            <small style="color: #666;">Google'da görünecek açıklama (150-160 karakter önerilir)</small>
        </div>

        <div class="form-group">
            <label>Meta Keywords (Anahtar Kelimeler)</label>
            <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($settings['meta_keywords']); ?>" placeholder="tanker, gemi işletmeciliği, denizcilik, tankmarine">
            <small style="color: #666;">Virgülle ayırarak yazın</small>
        </div>

        <hr style="margin: 40px 0; border: none; border-top: 2px solid var(--light);">
        
        <!-- Google Analytics -->
        <h3 style="color: var(--primary); margin-bottom: 20px;">
            <i class="fas fa-chart-line"></i> Google Analytics
        </h3>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Google Analytics Tracking Kodu</strong><br>
                Google Analytics hesabınızdan aldığınız tracking kodunu (Measurement ID: G-XXXXXXXXXX) buraya yapıştırın.
            </div>
        </div>

        <div class="form-group">
            <label>Google Analytics Measurement ID</label>
            <input type="text" name="google_analytics" class="form-control" value="<?php echo htmlspecialchars($settings['google_analytics']); ?>" placeholder="G-XXXXXXXXXX">
        </div>

        <div style="margin-top: 40px;">
            <button type="submit" class="btn btn-success" style="padding: 15px 40px; font-size: 1.1rem;">
                <i class="fas fa-save"></i> Tüm Ayarları Kaydet
            </button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>