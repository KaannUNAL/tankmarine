<?php
require_once 'config.php';
$settings = getSettings();
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Sayfa başlıkları
$page_titles = [
    'index' => 'Ana Sayfa',
    'fleet' => 'Filomuz',
    'gallery' => 'Galeri',
    'news' => 'Haberler',
    'career' => 'Kariyer',
    'contact' => 'İletişim'
];

$page_title = $page_titles[$current_page] ?? 'Sayfa';
$full_title = $settings['meta_title'] ? $settings['meta_title'] : $settings['site_name'] . ' - ' . $page_title;
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($full_title); ?></title>
    
    <!-- SEO Meta Tags -->
    <?php if($settings['meta_description']): ?>
    <meta name="description" content="<?php echo htmlspecialchars($settings['meta_description']); ?>">
    <?php endif; ?>
    
    <?php if($settings['meta_keywords']): ?>
    <meta name="keywords" content="<?php echo htmlspecialchars($settings['meta_keywords']); ?>">
    <?php endif; ?>
    
    <meta name="author" content="<?php echo htmlspecialchars($settings['site_name']); ?>">
    
    <!-- Favicon -->
    <?php if($settings['site_favicon']): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo UPLOAD_URL . $settings['site_favicon']; ?>">
    <link rel="shortcut icon" href="<?php echo UPLOAD_URL . $settings['site_favicon']; ?>">
    <?php endif; ?>
    
    <!-- Google Analytics -->
    <?php if($settings['google_analytics']): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($settings['google_analytics']); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo htmlspecialchars($settings['google_analytics']); ?>');
    </script>
    <?php endif; ?>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        :root {
            --primary: #003366;
            --secondary: #0066cc;
            --accent: #00a8e8;
            --dark: #001a33;
            --light: #f8f9fa;
        }

        /* Top Header */
        .top-header {
            background: var(--dark);
            color: white;
            padding: 10px 0;
            font-size: 0.9rem;
        }

        .top-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .contact-info {
            display: flex;
            gap: 30px;
        }

        .contact-info a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: color 0.3s;
        }

        .contact-info a:hover {
            color: var(--accent);
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            color: white;
            font-size: 1.1rem;
            transition: all 0.3s;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
        }

        .social-links a:hover {
            background: var(--accent);
            transform: translateY(-3px);
        }

        /* Main Header */
        .main-header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .main-header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 80px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary);
            text-decoration: none;
        }

        .logo img {
            max-height: 50px;
            max-width: 200px;
            object-fit: contain;
        }

        .logo i {
            font-size: 2rem;
            color: var(--accent);
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .logo-text .main {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .logo-text .sub {
            font-size: 0.75rem;
            color: #666;
            font-weight: 400;
        }

        .main-nav {
            display: flex;
            list-style: none;
            gap: 0;
        }

        .main-nav li a {
            display: block;
            padding: 30px 20px;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            position: relative;
        }

        .main-nav li a:hover,
        .main-nav li a.active {
            color: var(--accent);
            background: var(--light);
        }

        .main-nav li a.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent);
        }

        /* Mobile Menu */
        .mobile-toggle {
            display: none;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
            background: none;
            border: none;
        }

        @media (max-width: 768px) {
            .contact-info {
                flex-direction: column;
                gap: 10px;
            }

            .top-header .container {
                flex-direction: column;
                text-align: center;
            }

            .main-header .container {
                height: 70px;
            }

            .mobile-toggle {
                display: block;
            }

            .main-nav {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                box-shadow: 0 5px 10px rgba(0,0,0,0.1);
                display: none;
            }

            .main-nav.active {
                display: flex;
            }

            .main-nav li a {
                padding: 15px 20px;
            }

            .logo {
                font-size: 1.2rem;
            }

            .logo i {
                font-size: 1.5rem;
            }
        }

        /* Flag Animation */
        .flag-slider {
            background: var(--primary);
            padding: 15px 0;
            overflow: hidden;
            position: relative;
        }

        .flag-track {
            display: flex;
            gap: 30px;
            animation: scroll 30s linear infinite;
        }

        .flag-item {
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
            color: white;
            font-size: 1.5rem;
        }

        @keyframes scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <div class="top-header">
        <div class="container">
            <div class="contact-info">
                <a href="tel:<?php echo $settings['phone']; ?>">
                    <i class="fas fa-phone"></i>
                    <?php echo $settings['phone']; ?>
                </a>
                <a href="mailto:<?php echo $settings['email']; ?>">
                    <i class="fas fa-envelope"></i>
                    <?php echo $settings['email']; ?>
                </a>
            </div>
            <div class="social-links">
                <?php if($settings['facebook']): ?>
                <a href="<?php echo $settings['facebook']; ?>" target="_blank" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <?php endif; ?>
                
                <?php if($settings['twitter']): ?>
                <a href="<?php echo $settings['twitter']; ?>" target="_blank" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <?php endif; ?>
                
                <?php if($settings['linkedin']): ?>
                <a href="<?php echo $settings['linkedin']; ?>" target="_blank" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <?php endif; ?>
                
                <?php if($settings['instagram']): ?>
                <a href="<?php echo $settings['instagram']; ?>" target="_blank" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <?php endif; ?>
                
                <?php if($settings['youtube']): ?>
                <a href="<?php echo $settings['youtube']; ?>" target="_blank" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <a href="index.php" class="logo">
                <?php if($settings['site_logo']): ?>
                    <img src="<?php echo UPLOAD_URL . $settings['site_logo']; ?>" alt="<?php echo htmlspecialchars($settings['site_name']); ?>">
                <?php else: ?>
                    <i class="fas fa-anchor"></i>
                    <div class="logo-text">
                        <span class="main">TANKMARINE</span>
                        <span class="sub">Ship Management</span>
                    </div>
                <?php endif; ?>
            </a>
            
            <button class="mobile-toggle" onclick="toggleMenu()">
                <i class="fas fa-bars"></i>
            </button>
            
            <nav>
                <ul class="main-nav" id="mainNav">
                    <li><a href="index.php" class="<?php echo $current_page == 'index' ? 'active' : ''; ?>">Anasayfa</a></li>
                    <li><a href="fleet.php" class="<?php echo $current_page == 'fleet' ? 'active' : ''; ?>">Filomuz</a></li>
                    <li><a href="gallery.php" class="<?php echo $current_page == 'gallery' ? 'active' : ''; ?>">Galeri</a></li>
                    <li><a href="news.php" class="<?php echo $current_page == 'news' ? 'active' : ''; ?>">Haberler</a></li>
                    <li><a href="career.php" class="<?php echo $current_page == 'career' ? 'active' : ''; ?>">Kariyer</a></li>
                    <li><a href="contact.php" class="<?php echo $current_page == 'contact' ? 'active' : ''; ?>">İletişim</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <script>
        function toggleMenu() {
            document.getElementById('mainNav').classList.toggle('active');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('mainNav');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (!nav.contains(event.target) && !toggle.contains(event.target)) {
                nav.classList.remove('active');
            }
        });
    </script>