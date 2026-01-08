<?php
require_once 'header.php';

if (isset($_POST['update_field'])) {
    $id = $_POST['id'];
    $field_label = $_POST['field_label'];
    $placeholder = $_POST['placeholder'];
    $is_required = isset($_POST['is_required']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $stmt = $pdo->prepare("UPDATE contact_form_fields SET field_label=?, placeholder=?, is_required=?, is_active=? WHERE id=?");
    $stmt->execute([$field_label, $placeholder, $is_required, $is_active, $id]);
    
    flash('success', 'Alan güncellendi!');
    redirect('/admin/contact-fields.php');
}

$fields = $pdo->query("SELECT * FROM contact_form_fields ORDER BY sort_order ASC")->fetchAll();
?>

<?php if(flash('success')): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo flash('success'); ?></div>
<?php endif; ?>

<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong>İletişim Formu Ayarları</strong><br>
        Form alanlarını özelleştirebilir, zorunlu olup olmayacağını belirleyebilir ve aktif/pasif yapabilirsiniz.
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Form Alanları</h2>
    </div>

    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php foreach($fields as $field): ?>
        <div style="background: var(--light); padding: 25px; border-radius: 10px; border-left: 4px solid <?php echo $field['is_active'] ? 'var(--accent)' : '#ccc'; ?>;">
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $field['id']; ?>">
                
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <strong style="color: var(--primary);">Alan Adı:</strong>
                        <div style="margin-top: 5px; padding: 10px; background: white; border-radius: 5px; font-family: monospace;">
                            <?php echo htmlspecialchars($field['field_name']); ?>
                        </div>
                    </div>
                    
                    <div>
                        <strong style="color: var(--primary);">Alan Tipi:</strong>
                        <div style="margin-top: 5px; padding: 10px; background: white; border-radius: 5px;">
                            <?php 
                            $types = [
                                'text' => 'Metin',
                                'email' => 'E-posta',
                                'tel' => 'Telefon',
                                'textarea' => 'Çok Satırlı Metin'
                            ];
                            echo $types[$field['field_type']] ?? $field['field_type'];
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Etiket (Label) *</label>
                        <input type="text" name="field_label" class="form-control" value="<?php echo htmlspecialchars($field['field_label']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Placeholder (İpucu Metni)</label>
                        <input type="text" name="placeholder" class="form-control" value="<?php echo htmlspecialchars($field['placeholder']); ?>">
                    </div>
                </div>

                <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 20px;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_required" value="1" <?php echo $field['is_required'] ? 'checked' : ''; ?>>
                        <span><i class="fas fa-asterisk"></i> Zorunlu Alan</span>
                    </label>

                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" <?php echo $field['is_active'] ? 'checked' : ''; ?>>
                        <span><i class="fas fa-eye"></i> Aktif</span>
                    </label>
                </div>

                <button type="submit" name="update_field" class="btn btn-success btn-sm">
                    <i class="fas fa-save"></i> Kaydet
                </button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="content-card">
    <div class="content-header">
        <h2>Önizleme</h2>
    </div>

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h3 style="color: var(--primary); margin-bottom: 20px;">İletişim Formu Önizleme</h3>
        
        <form>
            <?php 
            $active_fields = array_filter($fields, function($f) { return $f['is_active']; });
            foreach($active_fields as $field): 
            ?>
            <div class="form-group">
                <label style="color: var(--primary); font-weight: 600;">
                    <?php echo htmlspecialchars($field['field_label']); ?>
                    <?php if($field['is_required']): ?>
                    <span style="color: var(--danger);">*</span>
                    <?php endif; ?>
                </label>
                
                <?php if($field['field_type'] == 'textarea'): ?>
                <textarea 
                    class="form-control" 
                    rows="5" 
                    placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>"
                    <?php echo $field['is_required'] ? 'required' : ''; ?>
                    disabled></textarea>
                <?php else: ?>
                <input 
                    type="<?php echo $field['field_type']; ?>" 
                    class="form-control" 
                    placeholder="<?php echo htmlspecialchars($field['placeholder']); ?>"
                    <?php echo $field['is_required'] ? 'required' : ''; ?>
                    disabled>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            
            <button type="button" class="btn btn-success" style="width: 100%; opacity: 0.7;" disabled>
                <i class="fas fa-paper-plane"></i> Mesajı Gönder
            </button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>