<?php
require_once 'header.php';

// Filo detayı gösterilecek mi?
$fleet_id = $_GET['id'] ?? null;

if ($fleet_id) {
    // Tek gemi detayı
    $stmt = $pdo->prepare("SELECT * FROM fleet WHERE id = ? AND active = 1");
    $stmt->execute([$fleet_id]);
    $ship = $stmt->fetch();
    
    if (!$ship) {
        redirect('/fleet.php');
    }
    
    // Gemi galerisi
    $stmt = $pdo->prepare("SELECT * FROM fleet_gallery WHERE fleet_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$fleet_id]);
    $gallery = $stmt->fetchAll();
} else {
    // Tüm filo listesi
    $stmt = $pdo->query("SELECT * FROM fleet WHERE active = 1 ORDER BY sort_order ASC");
    $fleet = $stmt->fetchAll();
}
?>

<style>
    .page-header {
        background: linear-gradient(rgba(0, 26, 51, 0.8), rgba(0, 51, 102, 0.8)), 
                    url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 300"><rect fill="%23003366" width="1200" height="300"/><path fill="%23004080" d="M0 150 Q300 100 600 150 T1200 150 V300 H0 Z"/></svg>');
        background-size: cover;
        background-position: center;
        color: white;
        padding: 100px 20px 60px;
        text-align: center;
    }

    .page-header h1 {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .breadcrumb {
        display: flex;
        justify-content: center;
        gap: 10px;
        align-items: center;
        color: #ddd;
    }

    .breadcrumb a {
        color: white;
        text-decoration: none;
    }

    .breadcrumb a:hover {
        color: var(--accent);
    }

    .fleet-section {
        padding: 80px 20px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .fleet-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .fleet-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: all 0.3s;
        cursor: pointer;
    }

    .fleet-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.2);
    }

    .fleet-image {
        width: 100%;
        height: 250px;
        overflow: hidden;
        position: relative;
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
        transition: transform 0.3s;
    }

    .fleet-card:hover .fleet-image img {
        transform: scale(1.1);
    }

    .fleet-info {
        padding: 25px;
    }

    .fleet-info h3 {
        color: var(--primary);
        margin-bottom: 10px;
        font-size: 1.5rem;
    }

    .fleet-type {
        color: var(--accent);
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    .fleet-specs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-top: 15px;
    }

    .spec-item {
        padding: 10px;
        background: var(--light);
        border-radius: 5px;
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
    }

    .view-details {
        display: block;
        margin-top: 20px;
        padding: 10px;
        background: var(--accent);
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 600;
        transition: background 0.3s;
    }

    .view-details:hover {
        background: var(--secondary);
    }

    /* Detail Page */
    .ship-detail {
        padding: 80px 20px;
        background: var(--light);
    }

    .ship-header {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }

    .ship-main-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 30px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 8rem;
    }

    .ship-main-image img {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 10px;
    }

    .ship-title {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 20px;
    }

    .ship-title h1 {
        color: var(--primary);
        font-size: 2.5rem;
    }

    .ship-title .type {
        background: var(--accent);
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: 600;
    }

    .ship-specs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .spec-box {
        background: var(--light);
        padding: 20px;
        border-radius: 10px;
        border-left: 4px solid var(--accent);
    }

    .spec-box .label {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
    }

    .spec-box .value {
        color: var(--primary);
        font-size: 1.3rem;
        font-weight: 600;
    }

    .ship-description {
        background: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        line-height: 1.8;
        color: #666;
    }

    .ship-gallery {
        margin-top: 40px;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        cursor: pointer;
        height: 200px;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .gallery-item:hover img {
        transform: scale(1.1);
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 30px;
        background: var(--dark);
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin-bottom: 30px;
        transition: background 0.3s;
    }

    .back-button:hover {
        background: var(--primary);
    }

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2rem;
        }

        .fleet-grid {
            grid-template-columns: 1fr;
        }

        .ship-title {
            flex-direction: column;
            gap: 15px;
        }

        .ship-specs-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if ($fleet_id && $ship): ?>
    <!-- Ship Detail Page -->
    <div class="page-header">
        <h1><?php echo htmlspecialchars($ship['name']); ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Anasayfa</a>
            <i class="fas fa-angle-right"></i>
            <a href="fleet.php">Filomuz</a>
            <i class="fas fa-angle-right"></i>
            <span><?php echo htmlspecialchars($ship['name']); ?></span>
        </div>
    </div>

    <div class="ship-detail">
        <div class="container">
            <a href="fleet.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Filomuz'a Dön
            </a>

            <div class="ship-header">
                <div class="ship-main-image">
                    <?php if($ship['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $ship['image']; ?>" alt="<?php echo htmlspecialchars($ship['name']); ?>">
                    <?php else: ?>
                    <i class="fas fa-ship"></i>
                    <?php endif; ?>
                </div>

                <div class="ship-title">
                    <div>
                        <h1><?php echo htmlspecialchars($ship['name']); ?></h1>
                    </div>
                    <div class="type"><?php echo htmlspecialchars($ship['type']); ?></div>
                </div>

                <div class="ship-specs-grid">
                    <div class="spec-box">
                        <div class="label">DWT (Deadweight Tonnage)</div>
                        <div class="value"><?php echo htmlspecialchars($ship['dwt']); ?></div>
                    </div>
                    <div class="spec-box">
                        <div class="label">İnşa Yılı</div>
                        <div class="value"><?php echo htmlspecialchars($ship['built_year']); ?></div>
                    </div>
                    <div class="spec-box">
                        <div class="label">Bayrak</div>
                        <div class="value"><?php echo htmlspecialchars($ship['flag']); ?></div>
                    </div>
                    <?php if($ship['length']): ?>
                    <div class="spec-box">
                        <div class="label">Uzunluk</div>
                        <div class="value"><?php echo htmlspecialchars($ship['length']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($ship['beam']): ?>
                    <div class="spec-box">
                        <div class="label">Genişlik</div>
                        <div class="value"><?php echo htmlspecialchars($ship['beam']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($ship['draft']): ?>
                    <div class="spec-box">
                        <div class="label">Draft</div>
                        <div class="value"><?php echo htmlspecialchars($ship['draft']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($ship['speed']): ?>
                    <div class="spec-box">
                        <div class="label">Hız</div>
                        <div class="value"><?php echo htmlspecialchars($ship['speed']); ?></div>
                    </div>
                    <?php endif; ?>
                    <?php if($ship['engine']): ?>
                    <div class="spec-box">
                        <div class="label">Motor</div>
                        <div class="value"><?php echo htmlspecialchars($ship['engine']); ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($ship['description']): ?>
            <div class="ship-description">
                <h2 style="color: var(--primary); margin-bottom: 20px;">Detaylı Bilgi</h2>
                <?php echo nl2br(htmlspecialchars($ship['description'])); ?>
            </div>
            <?php endif; ?>

            <?php if(count($gallery) > 0): ?>
            <div class="ship-gallery">
                <h2 style="color: var(--primary); margin-bottom: 30px; text-align: center;">Fotoğraf Galerisi</h2>
                <div class="gallery-grid">
                    <?php foreach($gallery as $photo): ?>
                    <div class="gallery-item">
                        <img src="<?php echo UPLOAD_URL . $photo['image']; ?>" 
                             alt="<?php echo htmlspecialchars($photo['title'] ?? $ship['name']); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>
    <!-- Fleet List Page -->
    <div class="page-header">
        <h1>Filomuz</h1>
        <div class="breadcrumb">
            <a href="index.php">Anasayfa</a>
            <i class="fas fa-angle-right"></i>
            <span>Filomuz</span>
        </div>
    </div>

    <div class="fleet-section">
        <div class="container">
            <div class="fleet-grid">
                <?php foreach($fleet as $ship): ?>
                <div class="fleet-card" onclick="window.location.href='fleet.php?id=<?php echo $ship['id']; ?>'">
                    <div class="fleet-image">
                        <?php if($ship['image']): ?>
                        <img src="<?php echo UPLOAD_URL . $ship['image']; ?>" alt="<?php echo htmlspecialchars($ship['name']); ?>">
                        <?php else: ?>
                        <i class="fas fa-ship"></i>
                        <?php endif; ?>
                    </div>
                    <div class="fleet-info">
                        <h3><?php echo htmlspecialchars($ship['name']); ?></h3>
                        <div class="fleet-type"><?php echo htmlspecialchars($ship['type']); ?></div>
                        
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

                        <a href="fleet.php?id=<?php echo $ship['id']; ?>" class="view-details" onclick="event.stopPropagation()">
                            Detaylı Bilgi <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>