<?php
require_once 'header.php';

// Kategorileri çek
$stmt = $pdo->query("SELECT * FROM gallery_categories ORDER BY sort_order ASC");
$categories = $stmt->fetchAll();

// Seçili kategori
$category_id = $_GET['category'] ?? null;

// Galeri öğelerini çek
if ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM gallery WHERE category_id = ? AND active = 1 ORDER BY sort_order ASC");
    $stmt->execute([$category_id]);
} else {
    $stmt = $pdo->query("SELECT * FROM gallery WHERE active = 1 ORDER BY sort_order ASC");
}
$gallery_items = $stmt->fetchAll();
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

    .gallery-section {
        padding: 80px 20px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .category-filter {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 50px;
    }

    .category-btn {
        padding: 12px 30px;
        background: white;
        border: 2px solid var(--primary);
        color: var(--primary);
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .category-btn:hover,
    .category-btn.active {
        background: var(--primary);
        color: white;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 25px;
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        cursor: pointer;
        height: 300px;
        background: var(--light);
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

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.8));
        padding: 20px;
        color: white;
        transform: translateY(100%);
        transition: transform 0.3s;
    }

    .gallery-item:hover .gallery-overlay {
        transform: translateY(0);
    }

    .gallery-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 5px;
    }

    /* Lightbox */
    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.95);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .lightbox.active {
        display: flex;
    }

    .lightbox-content {
        max-width: 90%;
        max-height: 90%;
        position: relative;
    }

    .lightbox-content img {
        max-width: 100%;
        max-height: 90vh;
        border-radius: 10px;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: white;
        color: var(--dark);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.5rem;
        transition: all 0.3s;
    }

    .lightbox-close:hover {
        background: var(--accent);
        color: white;
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.9);
        color: var(--dark);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        cursor: pointer;
        font-size: 1.5rem;
        transition: all 0.3s;
    }

    .lightbox-nav:hover {
        background: white;
    }

    .lightbox-prev {
        left: 20px;
    }

    .lightbox-next {
        right: 20px;
    }

    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2rem;
        }

        .gallery-grid {
            grid-template-columns: 1fr;
        }

        .category-filter {
            flex-direction: column;
        }
    }
</style>

<div class="page-header">
    <h1>Galeri</h1>
    <div class="breadcrumb">
        <a href="index.php">Anasayfa</a>
        <i class="fas fa-angle-right"></i>
        <span>Galeri</span>
    </div>
</div>

<div class="gallery-section">
    <div class="container">
        <?php if(count($categories) > 0): ?>
        <div class="category-filter">
            <a href="gallery.php" class="category-btn <?php echo !$category_id ? 'active' : ''; ?>">
                Tümü
            </a>
            <?php foreach($categories as $category): ?>
            <a href="gallery.php?category=<?php echo $category['id']; ?>" 
               class="category-btn <?php echo $category_id == $category['id'] ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($category['name']); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if(count($gallery_items) > 0): ?>
        <div class="gallery-grid">
            <?php foreach($gallery_items as $item): ?>
            <div class="gallery-item" onclick="openLightbox(<?php echo $item['id']; ?>)">
                <img src="<?php echo UPLOAD_URL . $item['image']; ?>" 
                     alt="<?php echo htmlspecialchars($item['title']); ?>"
                     data-id="<?php echo $item['id']; ?>">
                <?php if($item['title']): ?>
                <div class="gallery-overlay">
                    <div class="gallery-title"><?php echo htmlspecialchars($item['title']); ?></div>
                    <?php if($item['description']): ?>
                    <div style="font-size: 0.9rem; opacity: 0.9;">
                        <?php echo htmlspecialchars(substr($item['description'], 0, 80)) . '...'; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #666;">
            <i class="fas fa-images" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <p>Bu kategoride henüz fotoğraf bulunmamaktadır.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
    <button class="lightbox-close" onclick="closeLightbox(event)">
        <i class="fas fa-times"></i>
    </button>
    <button class="lightbox-nav lightbox-prev" onclick="prevImage(event)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="lightbox-nav lightbox-next" onclick="nextImage(event)">
        <i class="fas fa-chevron-right"></i>
    </button>
    <div class="lightbox-content">
        <img src="" alt="" id="lightboxImage">
    </div>
</div>

<script>
    const galleryImages = <?php echo json_encode(array_map(function($item) {
        return [
            'id' => $item['id'],
            'image' => UPLOAD_URL . $item['image'],
            'title' => $item['title']
        ];
    }, $gallery_items)); ?>;

    let currentImageIndex = 0;

    function openLightbox(imageId) {
        currentImageIndex = galleryImages.findIndex(img => img.id === imageId);
        showLightboxImage();
        document.getElementById('lightbox').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(event) {
        if (event.target === document.getElementById('lightbox') || 
            event.target.closest('.lightbox-close')) {
            document.getElementById('lightbox').classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function showLightboxImage() {
        const img = galleryImages[currentImageIndex];
        document.getElementById('lightboxImage').src = img.image;
        document.getElementById('lightboxImage').alt = img.title;
    }

    function nextImage(event) {
        event.stopPropagation();
        currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
        showLightboxImage();
    }

    function prevImage(event) {
        event.stopPropagation();
        currentImageIndex = (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
        showLightboxImage();
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('lightbox').classList.contains('active')) {
            if (e.key === 'Escape') closeLightbox({target: document.getElementById('lightbox')});
            if (e.key === 'ArrowRight') nextImage({stopPropagation: () => {}});
            if (e.key === 'ArrowLeft') prevImage({stopPropagation: () => {}});
        }
    });
</script>

<?php require_once 'footer.php'; ?>