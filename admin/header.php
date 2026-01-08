<?php
// UYARI: Bu dosyanın başında ve sonunda BOŞLUK OLMAMALI!
require_once '../config.php';
requireAdmin();

$admin = getAdmin();
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Tankmarine</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f6fa;
            color: #333;
        }

        :root {
            --primary: #003366;
            --secondary: #0066cc;
            --accent: #00a8e8;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --dark: #001a33;
            --light: #f8f9fa;
            --sidebar-width: 260px;
        }

        /* Top Header */
        .top-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: 70px;
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
        }

        .header-left h2 {
            color: var(--primary);
            font-size: 1.5rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 15px;
            background: var(--light);
            border-radius: 50px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .logout-btn {
            padding: 10px 20px;
            background: var(--danger);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--dark) 0%, var(--primary) 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-logo {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: var(--accent);
        }

        .sidebar-logo h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .sidebar-logo p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-item {
            display: block;
            padding: 15px 25px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid transparent;
        }

        .menu-item i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: var(--accent);
        }

        .menu-item.active {
            background: rgba(255,255,255,0.15);
            border-left-color: var(--accent);
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: 70px;
            padding: 30px;
            min-height: calc(100vh - 70px);
        }

        /* Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
        }

        .stat-icon.blue { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-icon.green { background: linear-gradient(135deg, #28a745, #20c997); }
        .stat-icon.orange { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-icon.purple { background: linear-gradient(135deg, #4facfe, #00f2fe); }

        .stat-info h3 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #666;
            font-size: 0.95rem;
        }

        /* Content Card */
        .content-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--light);
        }

        .content-header h2 {
            color: var(--primary);
            font-size: 1.8rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-warning {
            background: var(--warning);
            color: #333;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        /* Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: var(--light);
        }

        .data-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--primary);
            border-bottom: 2px solid #ddd;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .data-table tbody tr:hover {
            background: var(--light);
        }

        .data-table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Alerts */
        .alert {
            padding: 15px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-success {
            background: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .top-header {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }

        /* Image Preview */
        .image-preview {
            width: 200px;
            height: 200px;
            border: 2px dashed #ddd;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-anchor"></i>
            <h1>TANKMARINE</h1>
            <p>Yönetim Paneli</p>
        </div>

        <nav class="sidebar-menu">
            <a href="index.php" class="menu-item <?php echo $current_page == 'index' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>

            <a href="sliders.php" class="menu-item <?php echo $current_page == 'sliders' ? 'active' : ''; ?>">
                <i class="fas fa-images"></i>
                <span>Slider Yönetimi</span>
            </a>

            <a href="flags.php" class="menu-item <?php echo $current_page == 'flags' ? 'active' : ''; ?>">
                <i class="fas fa-flag"></i>
                <span>Bayrak Yönetimi</span>
            </a>

            <a href="about.php" class="menu-item <?php echo $current_page == 'about' ? 'active' : ''; ?>">
                <i class="fas fa-info-circle"></i>
                <span>Hakkımızda</span>
            </a>

            <a href="statistics.php" class="menu-item <?php echo $current_page == 'statistics' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>İstatistikler</span>
            </a>

            <a href="fleet.php" class="menu-item <?php echo $current_page == 'fleet' ? 'active' : ''; ?>">
                <i class="fas fa-ship"></i>
                <span>Filo Yönetimi</span>
            </a>

            <a href="team.php" class="menu-item <?php echo $current_page == 'team' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Ekip Yönetimi</span>
            </a>

            <a href="services.php" class="menu-item <?php echo $current_page == 'services' ? 'active' : ''; ?>">
                <i class="fas fa-concierge-bell"></i>
                <span>Hizmetler</span>
            </a>

            <a href="gallery.php" class="menu-item <?php echo $current_page == 'gallery' ? 'active' : ''; ?>">
                <i class="fas fa-camera"></i>
                <span>Galeri</span>
            </a>

            <a href="news.php" class="menu-item <?php echo $current_page == 'news' ? 'active' : ''; ?>">
                <i class="fas fa-newspaper"></i>
                <span>Haberler</span>
            </a>

            <a href="careers.php" class="menu-item <?php echo $current_page == 'careers' ? 'active' : ''; ?>">
                <i class="fas fa-briefcase"></i>
                <span>Kariyer İlanları</span>
            </a>

            <a href="applications.php" class="menu-item <?php echo $current_page == 'applications' ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i>
                <span>Kariyer Başvuruları</span>
            </a>

            <a href="messages.php" class="menu-item <?php echo $current_page == 'messages' ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                <span>İletişim Mesajları</span>
            </a>

            <a href="settings.php" class="menu-item <?php echo $current_page == 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Site Ayarları</span>
            </a>

            <a href="contact-fields.php" class="menu-item <?php echo $current_page == 'contact-fields' ? 'active' : ''; ?>">
                <i class="fas fa-wpforms"></i>
                <span>İletişim Formu</span>
                            <a href="users.php" class="menu-item <?php echo $current_page == 'admin_users   ' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Kullanıcı Yönetimi</span>
            </a>
            </a>
        </nav>
    </div>

    <!-- Top Header -->
    <div class="top-header">
        <div class="header-left">
            <h2>Hoş Geldiniz, <?php echo htmlspecialchars($admin['full_name']); ?></h2>
        </div>
        <div class="header-right">
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($admin['full_name'], 0, 1)); ?>
                </div>
                <div>
                    <div style="font-weight: 600; font-size: 0.95rem;">
                        <?php echo htmlspecialchars($admin['full_name']); ?>
                    </div>
                    <div style="font-size: 0.8rem; color: #999;">
                        <?php echo htmlspecialchars($admin['role']); ?>
                    </div>
                </div>
            </div>
            <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-primary btn-sm">
                <i class="fas fa-globe"></i> Siteyi Görüntüle
            </a>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Çıkış
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">