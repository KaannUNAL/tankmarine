<?php
require_once 'header.php';

$news_id = $_GET['id'] ?? null;

if ($news_id) {
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id = ? AND active = 1");
    $stmt->execute([$news_id]);
    $news_item = $stmt->fetch();
    
    if ($news_item) {
        // Görüntülenme sayısını artır
        $pdo->prepare("UPDATE news SET views = views + 1 WHERE id = ?")->execute([$news_id]);
    }
    
    // Diğer haberler
    $stmt = $pdo->prepare("SELECT * FROM news WHERE id != ? AND active = 1 ORDER BY created_at DESC LIMIT 3");
    $stmt->execute([$news_id]);
    $other_news = $stmt->fetchAll();
} else {
    $stmt = $pdo->query("SELECT * FROM news WHERE active = 1 ORDER BY created_at DESC");
    $all_news = $stmt->fetchAll();
}
?>

<style>
    .page-header {
        background: linear-gradient(rgba(0, 26, 51, 0.8), rgba(0, 51, 102, 0.8)), 
                    url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 300"><rect fill="%23003366" width="1200" height="300"/></svg>');
        padding: 100px 20px 60px;
        text-align: center;
        color: white;
    }
    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        padding: 80px 20px;
    }
    .news-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s;
        cursor: pointer;
    }
    .news-card:hover {
        transform: translateY(-10px);
    }
    .news-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
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
        font-size: 1.5rem;
        color: var(--primary);
        margin-bottom: 15px;
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
    }
    .news-detail {
        padding: 80px 20px;
        max-width: 900px;
        margin: 0 auto;
    }
    .detail-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    .detail-content {
        line-height: 1.8;
        color: #444;
        font-size: 1.1rem;
    }
    .other-news {
        margin-top: 60px;
        border-top: 2px solid var(--light);
        padding-top: 40px;
    }
    @media (max-width: 768px) {
        .news-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if ($news_id && $news_item): ?>
    <div class="page-header">
        <h1><?php echo htmlspecialchars($news_item['title']); ?></h1>
    </div>
    <div class="news-detail">
        <div class="news-meta" style="justify-content: center; margin-bottom: 30px;">
            <span><i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($news_item['created_at'])); ?></span>
            <span><i class="fas fa-eye"></i> <?php echo $news_item['views']; ?> görüntülenme</span>
        </div>
        <?php if($news_item['image']): ?>
        <img src="<?php echo UPLOAD_URL . $news_item['image']; ?>" alt="<?php echo htmlspecialchars($news_item['title']); ?>" class="detail-image">
        <?php endif; ?>
        <div class="detail-content">
            <?php echo nl2br(htmlspecialchars($news_item['content'])); ?>
        </div>
        <?php if(count($other_news) > 0): ?>
        <div class="other-news">
            <h3 style="color: var(--primary); margin-bottom: 30px;">Diğer Haberler</h3>
            <div style="display: grid; gap: 20px;">
                <?php foreach($other_news as $item): ?>
                <a href="news.php?id=<?php echo $item['id']; ?>" style="display: flex; gap: 20px; text-decoration: none; padding: 15px; background: var(--light); border-radius: 10px;">
                    <?php if($item['image']): ?>
                    <img src="<?php echo UPLOAD_URL . $item['image']; ?>" style="width: 120px; height: 80px; object-fit: cover; border-radius: 5px;">
                    <?php endif; ?>
                    <div>
                        <h4 style="color: var(--primary); margin-bottom: 5px;"><?php echo htmlspecialchars($item['title']); ?></h4>
                        <p style="color: #666; font-size: 0.9rem;"><?php echo htmlspecialchars(substr($item['summary'], 0, 100)) . '...'; ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        <div style="text-align: center; margin-top: 40px;">
            <a href="news.php" style="display: inline-block; padding: 12px 30px; background: var(--dark); color: white; text-decoration: none; border-radius: 5px;">
                <i class="fas fa-arrow-left"></i> Tüm Haberler
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="page-header">
        <h1>Haberler</h1>
    </div>
    <div class="news-grid" style="max-width: 1200px; margin: 0 auto;">
        <?php foreach($all_news as $item): ?>
        <div class="news-card" onclick="window.location.href='news.php?id=<?php echo $item['id']; ?>'">
            <?php if($item['image']): ?>
            <img src="<?php echo UPLOAD_URL . $item['image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="news-image">
            <?php else: ?>
            <div class="news-image" style="display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                <i class="fas fa-newspaper"></i>
            </div>
            <?php endif; ?>
            <div class="news-content">
                <div class="news-meta">
                    <span><i class="fas fa-calendar"></i> <?php echo date('d.m.Y', strtotime($item['created_at'])); ?></span>
                    <span><i class="fas fa-eye"></i> <?php echo $item['views']; ?></span>
                </div>
                <h3 class="news-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                <p class="news-summary"><?php echo htmlspecialchars(substr($item['summary'], 0, 150)) . '...'; ?></p>
                <a href="news.php?id=<?php echo $item['id']; ?>" class="read-more">Devamını Oku <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>