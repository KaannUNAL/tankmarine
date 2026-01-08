<?php
require_once 'header.php';

// Slider verilerini çek
$stmt = $pdo->query("SELECT * FROM sliders WHERE active = 1 ORDER BY sort_order ASC");
$sliders = $stmt->fetchAll();

// Bayrakları çek
$stmt = $pdo->query("SELECT * FROM country_flags WHERE active = 1 ORDER BY sort_order ASC");
$flags = $stmt->fetchAll();

// Hakkımızda bilgisini çek
$stmt = $pdo->query("SELECT * FROM about LIMIT 1");
$about = $stmt->fetch();

// Filomuz verilerini çek
$stmt = $pdo->query("SELECT * FROM fleet WHERE active = 1 ORDER BY sort_order ASC LIMIT 6");
$fleet = $stmt->fetchAll();

// Ekibimiz verilerini çek
$stmt = $pdo->query("SELECT * FROM team WHERE active = 1 ORDER BY sort_order ASC");
$team = $stmt->fetchAll();

// İstatistikler
$stmt = $pdo->query("SELECT * FROM statistics WHERE active = 1 ORDER BY sort_order ASC");
$stats = $stmt->fetchAll();

// Hizmetler
$stmt = $pdo->query("SELECT * FROM services WHERE active = 1 ORDER BY sort_order ASC LIMIT 6");
$services = $stmt->fetchAll();

// Haberler - Ana sayfada son 3 haber
$stmt = $pdo->query("SELECT * FROM news WHERE active = 1 ORDER BY created_at DESC LIMIT 3");
$news = $stmt->fetchAll();
?>

<style>
    /* Slider Styles */
    .slider-container {
        position: relative;
        width: 100%;
        height: 600px;
        overflow: hidden;
    }

    .slider-item {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
        background-size: cover;
        background-position: center;
    }

    .slider-item.active {
        opacity: 1;
    }

    .slider-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(rgba(0, 26, 51, 0.6), rgba(0, 51, 102, 0.6));
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .slider-content {
        max-width: 800px;
        padding: 0 20px;
        color: white;
        animation: fadeInUp 1s ease;
    }

    .slider-content h1 {
        font-size: 3rem;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }

    .slider-content p {
        font-size: 1.3rem;
        margin-bottom: 30px;
    }

    .slider-btn {
        display: inline-block;
        padding: 15px 40px;
        background: var(--accent);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .slider-btn:hover {
        background: var(--secondary);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .slider-nav {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 10;
    }

    .slider-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255,255,255,0.5);
        cursor: pointer;
        transition: all 0.3s;
    }

    .slider-dot.active {
        background: white;
        width: 30px;
        border-radius: 6px;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Flag Slider */
    .flag-slider {
        background: var(--primary);
        padding: 20px 0;
        overflow: hidden;
    }

    .flag-track {
        display: flex;
        gap: 40px;
        animation: scroll 30s linear infinite;
    }

    .flag-item {
        display: flex;
        align-items: center;
        gap: 10px;
        white-space: nowrap;
        color: white;
        font-size: 1.8rem;
        filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
    }

    .flag-item img {
        width: 40px;
        height: 40px;
        object-fit: contain;
        filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
    }

    @keyframes scroll {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }

    /* Section Styles */
    .section {
        padding: 80px 20px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .section-title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 50px;
        color: var(--primary);
    }

    .section-title::after {
        content: '';
        display: block;
        width: 80px;
        height: 4px;
        background: var(--accent);
        margin: 20px auto 0;
    }

    /* About Section - YENİ TASARIM */
    .about-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        align-items: start;
        margin-bottom: 40px;
    }

    .about-image-wrapper {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }

    .about-image-wrapper img {
        width: 100%;
        height: 500px;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .about-image-wrapper:hover img {
        transform: scale(1.05);
    }

    .about-text {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .about-text h3 {
        color: var(--primary);
        font-size: 2rem;
        margin-bottom: 10px;
    }

    .about-text p {
        line-height: 1.8;
        color: #666;
        font-size: 1.05rem;
    }

    .mission-vision {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-top: 30px;
    }

    .mv-card {
        background: linear-gradient(135deg, var(--light) 0%, #fff 100%);
        padding: 35px;
        border-radius: 15px;
        border-left: 5px solid var(--accent);
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: all 0.3s;
    }

    .mv-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .mv-card h4 {
        color: var(--primary);
        font-size: 1.5rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .mv-card h4 i {
        color: var(--accent);
        font-size: 1.8rem;
    }

    .mv-card p {
        color: #666;
        line-height: 1.9;
        font-size: 1.05rem;
    }

    /* Stats */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        margin-top: 50px;
    }

    .stat-card {
        text-align: center;
        padding: 40px 20px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        border-radius: 15px;
        transition: transform 0.3s;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .stat-card:hover {
        transform: translateY(-10px);
    }

    .stat-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        opacity: 0.9;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    /* Fleet Grid */
    .fleet-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
    }

    .fleet-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }

    .fleet-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .fleet-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 5rem;
    }

    .fleet-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .fleet-info {
        padding: 25px;
    }

    .fleet-info h3 {
        color: var(--primary);
        margin-bottom: 10px;
        font-size: 1.5rem;
    }

    .fleet-info .type {
        color: var(--accent);
        font-weight: 600;
        margin-bottom: 15px;
    }

    .fleet-specs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 15px;
        font-size: 0.9rem;
    }

    .spec-item {
        display: flex;
        justify-content: space-between;
        padding: 8px;
        background: var(--light);
        border-radius: 5px;
    }

    /* Team Section */
    .team-slider {
        position: relative;
        overflow: hidden;
        padding: 20px 0;
    }

    .team-track {
        display: flex;
        gap: 30px;
        animation: slideTeam 20s linear infinite;
    }

    .team-track:hover {
        animation-play-state: paused;
    }

    @keyframes slideTeam {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }

    .team-card {
        min-width: 300px;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }

    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .team-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 5rem;
    }

    .team-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .team-info {
        padding: 25px;
        text-align: center;
    }

    .team-info h3 {
        color: var(--primary);
        margin-bottom: 5px;
        font-size: 1.3rem;
    }

    .team-info .position {
        color: var(--accent);
        font-weight: 600;
        margin-bottom: 15px;
    }

    /* Services */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
    }

    .service-card {
        background: white;
        padding: 40px 30px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
        text-align: center;
        border-top: 4px solid var(--accent);
    }

    .service-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .service-icon {
        font-size: 3rem;
        color: var(--accent);
        margin-bottom: 20px;
    }

    .service-card h3 {
        color: var(--primary);
        margin-bottom: 15px;
    }

    /* News Section - YENİ */
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 30px;
    }

    .news-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
        cursor: pointer;
    }

    .news-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .news-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 4rem;
    }

    .news-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .news-content {
        padding: 25px;
    }

    .news-meta {
        display: flex;
        gap: 20px;
        color: #999;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .news-title {
        font-size: 1.4rem;
        color: var(--primary);
        margin-bottom: 15px;
        font-weight: 600;
    }

    .news-summary {
        color: #666;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .read-more {
        color: var(--accent);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: gap 0.3s;
    }

    .read-more:hover {
        gap: 12px;
    }

    @media (max-width: 768px) {
        .slider-content h1 {
            font-size: 2rem;
        }

        .about-content {
            grid-template-columns: 1fr;
        }

        .mission-vision {
            grid-template-columns: 1fr;
        }

        .fleet-grid, .services-grid, .news-grid {
            grid-template-columns: 1fr;
        }

        .team-card {
            min-width: 250px;
        }
    }
</style>

<!-- Slider -->
<div class="slider-container">
    <?php foreach($sliders as $index => $slider): ?>
    <div class="slider-item <?php echo $index === 0 ? 'active' : ''; ?>" 
         style="background-image: url('<?php echo UPLOAD_URL . $slider['image']; ?>');">
        <div class="slider-overlay">
            <div class="slider-content">
                <?php if($slider['button_text'] && $slider['button_link']): ?>
                <a href="<?php echo $slider['button_link']; ?>" class="slider-btn">
                    <?php echo htmlspecialchars($slider['button_text']); ?>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <div class="slider-nav">
        <?php foreach($sliders as $index => $slider): ?>
        <div class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
             onclick="goToSlide(<?php echo $index; ?>)"></div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Flag Slider -->
<?php if(count($flags) > 0): ?>
<div class="flag-slider">
    <div class="flag-track">
        <?php 
        // İki kez göster (seamless loop için)
        $flagsDouble = array_merge($flags, $flags);
        foreach($flagsDouble as $flag): 
        ?>
        <div class="flag-item">
            <?php if($flag['flag_image']): ?>
                <img src="<?php echo UPLOAD_URL . $flag['flag_image']; ?>" alt="<?php echo htmlspecialchars($flag['country_name']); ?>">
            <?php else: ?>
                <?php echo $flag['flag_emoji']; ?>
            <?php endif; ?>
            <?php echo htmlspecialchars($flag['country_name']); ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- About Section -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Hakkımızda</h2>
        
        <!-- Resim ve Açıklama Yan Yana -->
        <div class="about-wrapper">
            <div class="about-image-wrapper">
                <?php if($about['image']): ?>
                <img src="<?php echo UPLOAD_URL . $about['image']; ?>" alt="Hakkımızda">
                <?php else: ?>
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 600'%3E%3Crect fill='%23003366' width='400' height='600'/%3E%3Cpath fill='%230066cc' d='M0 300 Q100 250 200 300 T400 300 V600 H0 Z'/%3E%3Ccircle fill='%2300a8e8' cx='200' cy='150' r='50'/%3E%3C/svg%3E" alt="Hakkımızda">
                <?php endif; ?>
            </div>
        <!-- Misyon ve Vizyon - Tam Genişlik -->
        <div class="mission-vision-wrapper">
            <div class="mission-vision-grid">
                <div class="mv-card">
                    <h4>
                        <i class="fas fa-bullseye"></i>
                        Misyonumuz
                    </h4>
                    <h3><?php echo htmlspecialchars($about['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($about['content'])); ?></p>
                </div>
                <div class="mv-card">
                    <h4>
                        <i class="fas fa-bullseye"></i>
                        Misyonumuz
                    </h4>
                    <p><?php echo nl2br(htmlspecialchars($about['mission'])); ?></p>
                </div>
                
                <div class="mv-card">
                    <h4>
                        <i class="fas fa-eye"></i>
                        Vizyonumuz
                    </h4>
                    <p><?php echo nl2br(htmlspecialchars($about['vision'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <?php if(count($stats) > 0): ?>
        <div class="stats-grid">
            <?php foreach($stats as $stat): ?>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas <?php echo $stat['icon']; ?>"></i>
                </div>
                <div class="stat-number"><?php echo htmlspecialchars($stat['value']); ?></div>
                <div><?php echo htmlspecialchars($stat['label']); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Fleet Section -->
<?php if(count($fleet) > 0): ?>
<section class="section" style="background: var(--light);">
    <div class="container">
        <h2 class="section-title">Filomuz</h2>
        <div class="fleet-grid">
            <?php foreach($fleet as $ship): ?>
            <div class="fleet-card">
                <div class="fleet-image">
                    <?php if($ship['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $ship['image']; ?>" alt="<?php echo htmlspecialchars($ship['name']); ?>">
                    <?php else: ?>
                    <i class="fas fa-ship"></i>
                    <?php endif; ?>
                </div>
                <div class="fleet-info">
                    <h3><?php echo htmlspecialchars($ship['name']); ?></h3>
                    <div class="type"><?php echo htmlspecialchars($ship['type']); ?></div>
                    <div class="fleet-specs">
                        <div class="spec-item">
                            <strong>DWT:</strong>
                            <span><?php echo htmlspecialchars($ship['dwt']); ?></span>
                        </div>
                        <div class="spec-item">
                            <strong>Yıl:</strong>
                            <span><?php echo htmlspecialchars($ship['built_year']); ?></span>
                        </div>
                        <div class="spec-item" style="grid-column: 1 / -1;">
                            <strong>Bayrak:</strong>
                            <span><?php echo htmlspecialchars($ship['flag']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="fleet.php" class="slider-btn">Tüm Filomuz</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Team Section -->
<?php if(count($team) > 0): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">Ekibimiz</h2>
        <div class="team-slider">
            <div class="team-track">
                <?php 
                $teamDouble = array_merge($team, $team);
                foreach($teamDouble as $member): 
                ?>
                <div class="team-card">
                    <div class="team-image">
                        <?php if($member['image']): ?>
                        <img src="<?php echo UPLOAD_URL . $member['image']; ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                        <?php else: ?>
                        <i class="fas fa-user"></i>
                        <?php endif; ?>
                    </div>
                    <div class="team-info">
                        <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                        <div class="position"><?php echo htmlspecialchars($member['position']); ?></div>
                        <?php if($member['description']): ?>
                        <p style="color: #666; font-size: 0.9rem;">
                            <?php echo htmlspecialchars(substr($member['description'], 0, 100)) . '...'; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- News Section - YENİ -->
<?php if(count($news) > 0): ?>
<section class="section" style="background: var(--light);">
    <div class="container">
        <h2 class="section-title">Haberler</h2>
        <div class="news-grid">
            <?php foreach($news as $item): ?>
            <div class="news-card" onclick="window.location.href='news.php?id=<?php echo $item['id']; ?>'">
                <div class="news-image">
                    <?php if($item['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $item['image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                    <?php else: ?>
                    <i class="fas fa-newspaper"></i>
                    <?php endif; ?>
                </div>
                <div class="news-content">
                    <div class="news-meta">
                        <span><i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($item['created_at'])); ?></span>
                        <span><i class="fas fa-eye"></i> <?php echo $item['views']; ?></span>
                    </div>
                    <h3 class="news-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p class="news-summary"><?php echo htmlspecialchars(substr($item['summary'], 0, 150)) . '...'; ?></p>
                    <a href="news.php?id=<?php echo $item['id']; ?>" class="read-more" onclick="event.stopPropagation()">
                        Devamını Oku <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: center; margin-top: 40px;">
            <a href="news.php" class="slider-btn">Tüm Haberler</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Services Section -->
<?php if(count($services) > 0): ?>
<section class="section">
    <div class="container">
        <h2 class="section-title">Hizmetlerimiz</h2>
        <div class="services-grid">
            <?php foreach($services as $service): ?>
            <div class="service-card">
                <div class="service-icon">
                    <i class="fas <?php echo $service['icon']; ?>"></i>
                </div>
                <h3><?php echo htmlspecialchars($service['title']); ?></h3>
                <p><?php echo htmlspecialchars($service['description']); ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
    // Slider functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slider-item');
    const dots = document.querySelectorAll('.slider-dot');
    
    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            dots[i].classList.remove('active');
        });
        
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }
    
    function goToSlide(index) {
        currentSlide = index;
        showSlide(currentSlide);
    }
    
    // Auto slide
    setInterval(nextSlide, 5000);
</script>

<?php require_once 'footer.php'; ?>