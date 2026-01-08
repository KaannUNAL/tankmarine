<?php
require_once 'header.php';

/* ======================
   KULLANICI SİL
====================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Kendi hesabını silmeyi engelle (opsiyonel ama önerilir)
    if ($id == $_SESSION['user_id']) {
        flash('error', 'Kendi hesabınızı silemezsiniz!');
        redirect('/admin/users.php');
    }

    $pdo->prepare("DELETE FROM admin_users WHERE id = ?")->execute([$id]);
    flash('success', 'Kullanıcı başarıyla silindi!');
    redirect('/admin/users.php');
}

/* ======================
   EKLE / GÜNCELLE
====================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id        = $_POST['id'] ?? null;
    $username  = trim($_POST['username']);
    $email     = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $role      = $_POST['role'];
    $active    = isset($_POST['active']) ? 1 : 0;
    $password  = $_POST['password'] ?? null;

    if ($id) {
        // GÜNCELLE
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("
                UPDATE admin_users 
                SET username=?, email=?, full_name=?, role=?, active=?, password=?, updated_at=NOW()
                WHERE id=?
            ");
            $stmt->execute([$username, $email, $full_name, $role, $active, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("
                UPDATE admin_users 
                SET username=?, email=?, full_name=?, role=?, active=?, updated_at=NOW()
                WHERE id=?
            ");
            $stmt->execute([$username, $email, $full_name, $role, $active, $id]);
        }
        flash('success', 'Kullanıcı başarıyla güncellendi!');
    } else {
        // EKLE
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("
            INSERT INTO admin_users 
            (username, password, email, full_name, role, active, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$username, $hash, $email, $full_name, $role, $active]);
        flash('success', 'Kullanıcı başarıyla eklendi!');
    }

    redirect('/admin/users.php');
}

/* ======================
   DÜZENLE
====================== */
$edit_user = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_user = $stmt->fetch();
}

/* ======================
   LİSTE
====================== */
$stmt = $pdo->query("SELECT * FROM admin_users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<?php if (flash('success')): ?>
<div class="alert alert-success"><?php echo flash('success'); ?></div>
<?php endif; ?>

<?php if (flash('error')): ?>
<div class="alert alert-danger"><?php echo flash('error'); ?></div>
<?php endif; ?>

<!-- ======================
     FORM
====================== -->
<div class="content-card">
    <div class="content-header">
        <h2><?php echo $edit_user ? 'Kullanıcı Düzenle' : 'Yeni Kullanıcı Ekle'; ?></h2>
    </div>

    <form method="POST">
        <?php if ($edit_user): ?>
        <input type="hidden" name="id" value="<?php echo $edit_user['id']; ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Kullanıcı Adı *</label>
                <input type="text" name="username" class="form-control"
                       value="<?php echo $edit_user['username'] ?? ''; ?>" required>
            </div>

            <div class="form-group">
                <label>E-posta *</label>
                <input type="email" name="email" class="form-control"
                       value="<?php echo $edit_user['email'] ?? ''; ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label>Ad Soyad *</label>
            <input type="text" name="full_name" class="form-control"
                   value="<?php echo $edit_user['full_name'] ?? ''; ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Şifre <?php echo $edit_user ? '(Boş bırakılırsa değişmez)' : '*'; ?></label>
                <input type="password" name="password" class="form-control"
                       <?php echo $edit_user ? '' : 'required'; ?>>
            </div>

            <div class="form-group">
                <label>Rol</label>
                <select name="role" class="form-control">
                    <option value="admin" <?php echo ($edit_user['role'] ?? '') == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    <option value="editor" <?php echo ($edit_user['role'] ?? '') == 'editor' ? 'selected' : ''; ?>>Editor</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label style="display:flex;gap:10px;align-items:center;">
                <input type="checkbox" name="active" value="1"
                    <?php echo (!$edit_user || $edit_user['active']) ? 'checked' : ''; ?>>
                Aktif
            </label>
        </div>

        <button type="submit" class="btn btn-success">
            <?php echo $edit_user ? 'Güncelle' : 'Kaydet'; ?>
        </button>

        <?php if ($edit_user): ?>
        <a href="users.php" class="btn btn-danger">İptal</a>
        <?php endif; ?>
    </form>
</div>

<!-- ======================
     LİSTE
====================== -->
<div class="content-card">
    <div class="content-header">
        <h2>Kullanıcılar</h2>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Kullanıcı Adı</th>
                <th>Ad Soyad</th>
                <th>E-posta</th>
                <th>Rol</th>
                <th>Durum</th>
                <th>Son Giriş</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo ucfirst($user['role']); ?></td>
                <td>
                    <span class="badge badge-<?php echo $user['active'] ? 'success' : 'danger'; ?>">
                        <?php echo $user['active'] ? 'Aktif' : 'Pasif'; ?>
                    </span>
                </td>
                <td><?php echo $user['last_login'] ?? '-'; ?></td>
                <td>
                    <a href="users.php?edit=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="users.php?delete=<?php echo $user['id']; ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirmDelete()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'footer.php'; ?>
