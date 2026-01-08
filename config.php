<?php
// Veritabanı bağlantı ayarları
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'tankmarine');

// Site ayarları
define('SITE_URL', 'http://localhost/tankmarine');
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('UPLOAD_URL', SITE_URL . '/uploads/');

// Output buffering başlat (header hatalarını önler)
ob_start();

// Oturum başlat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Veritabanı bağlantısı
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

// Yardımcı fonksiyonlar
function redirect($url) {
    // Output buffer'ı temizle
    if (ob_get_length()) {
        ob_end_clean();
    }
    
    // Header gönder
    header("Location: " . SITE_URL . $url);
    exit;
}

function flash($key, $value = null) {
    if ($value === null) {
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $value;
        }
        return null;
    }
    $_SESSION['flash'][$key] = $value;
}

function old($key, $default = '') {
    return $_SESSION['old'][$key] ?? $default;
}

function setOld($data) {
    $_SESSION['old'] = $data;
}

function clearOld() {
    unset($_SESSION['old']);
}

function getSettings() {
    global $pdo;
    static $settings = null;
    
    if ($settings === null) {
        $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
        $settings = $stmt->fetch();
    }
    
    return $settings;
}

function uploadFile($file, $directory = '') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $uploadDir = UPLOAD_PATH . $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $directory . '/' . $filename;
    }
    
    return false;
}

function deleteFile($filepath) {
    $fullPath = UPLOAD_PATH . $filepath;
    if (file_exists($fullPath)) {
        unlink($fullPath);
    }
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('/admin/login.php');
    }
}

function getAdmin() {
    global $pdo;
    if (!isAdmin()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}

// Türkçe karakter dönüşümü
function trToEn($text) {
    $tr = array('ş','Ş','ı','I','İ','ğ','Ğ','ü','Ü','ö','Ö','Ç','ç');
    $en = array('s','s','i','i','i','g','g','u','u','o','o','c','c');
    return str_replace($tr, $en, $text);
}
?>