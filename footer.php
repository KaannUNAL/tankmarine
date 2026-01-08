<?php
$settings = getSettings();
?>
    <footer style="background: var(--dark); color: white; padding: 50px 0 0;">
        <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px; margin-bottom: 40px;">
                
                <!-- Hakkımızda -->
                <div>
                    <h3 style="margin-bottom: 20px; color: var(--accent);">
                        <i class="fas fa-anchor"></i> Tankmarine
                    </h3>
                    <p style="line-height: 1.8; color: #ddd;">
                        Profesyonel tanker işletmeciliği ve denizcilik hizmetleri alanında güvenilir çözüm ortağınız.
                    </p>
                    <div style="margin-top: 20px; display: flex; gap: 15px;">
                        <?php if($settings['facebook']): ?>
                        <a href="<?php echo $settings['facebook']; ?>" target="_blank" 
                           style="color: white; font-size: 1.2rem; transition: color 0.3s;"
                           onmouseover="this.style.color='var(--accent)'" 
                           onmouseout="this.style.color='white'">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if($settings['twitter']): ?>
                        <a href="<?php echo $settings['twitter']; ?>" target="_blank" 
                           style="color: white; font-size: 1.2rem; transition: color 0.3s;"
                           onmouseover="this.style.color='var(--accent)'" 
                           onmouseout="this.style.color='white'">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if($settings['linkedin']): ?>
                        <a href="<?php echo $settings['linkedin']; ?>" target="_blank" 
                           style="color: white; font-size: 1.2rem; transition: color 0.3s;"
                           onmouseover="this.style.color='var(--accent)'" 
                           onmouseout="this.style.color='white'">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php if($settings['instagram']): ?>
                        <a href="<?php echo $settings['instagram']; ?>" target="_blank" 
                           style="color: white; font-size: 1.2rem; transition: color 0.3s;"
                           onmouseover="this.style.color='var(--accent)'" 
                           onmouseout="this.style.color='white'">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div>
                    <h3 style="margin-bottom: 20px; color: var(--accent);">Hızlı Linkler</h3>
                    <ul style="list-style: none;">
                        <li style="margin-bottom: 10px;">
                            <a href="index.php" style="color: #ddd; text-decoration: none; transition: color 0.3s;"
                               onmouseover="this.style.color='var(--accent)'" 
                               onmouseout="this.style.color='#ddd'">
                                <i class="fas fa-angle-right"></i> Anasayfa
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="fleet.php" style="color: #ddd; text-decoration: none; transition: color 0.3s;"
                               onmouseover="this.style.color='var(--accent)'" 
                               onmouseout="this.style.color='#ddd'">
                                <i class="fas fa-angle-right"></i> Filomuz
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="gallery.php" style="color: #ddd; text-decoration: none; transition: color 0.3s;"
                               onmouseover="this.style.color='var(--accent)'" 
                               onmouseout="this.style.color='#ddd'">
                                <i class="fas fa-angle-right"></i> Galeri
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="news.php" style="color: #ddd; text-decoration: none; transition: color 0.3s;"
                               onmouseover="this.style.color='var(--accent)'" 
                               onmouseout="this.style.color='#ddd'">
                                <i class="fas fa-angle-right"></i> Haberler
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="career.php" style="color: #ddd; text-decoration: none; transition: color 0.3s;"
                               onmouseover="this.style.color='var(--accent)'" 
                               onmouseout="this.style.color='#ddd'">
                                <i class="fas fa-angle-right"></i> Kariyer
                            </a>
                        </li>
                        <li style="margin-bottom: 10px;">
                            <a href="contact.php" style="color: #ddd; text-decoration: none; transition: color 0.3s;"
                               onmouseover="this.style.color='var(--accent)'" 
                               onmouseout="this.style.color='#ddd'">
                                <i class="fas fa-angle-right"></i> İletişim
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- İletişim Bilgileri -->
                <div>
                    <h3 style="margin-bottom: 20px; color: var(--accent);">İletişim</h3>
                    <div style="margin-bottom: 15px; display: flex; gap: 10px; color: #ddd;">
                        <i class="fas fa-map-marker-alt" style="color: var(--accent); margin-top: 5px;"></i>
                        <span><?php echo nl2br($settings['address']); ?></span>
                    </div>
                    <div style="margin-bottom: 15px; display: flex; gap: 10px; color: #ddd;">
                        <i class="fas fa-phone" style="color: var(--accent);"></i>
                        <a href="tel:<?php echo $settings['phone']; ?>" 
                           style="color: #ddd; text-decoration: none;"
                           onmouseover="this.style.color='var(--accent)'" 
                           onmouseout="this.style.color='#ddd'">
                            <?php echo $settings['phone']; ?>
                        </a>
                    </div>
                    <div style="margin-bottom: 15px; display: flex; gap: 10px; color: #ddd;">
                        <i class="fas fa-envelope" style="color: var(--accent);"></i>
                        <a href="mailto:<?php echo $settings['email']; ?>" 
                           style="color: #ddd; text-decoration: none;"
                           onmouseover="this.style.color='var(--accent)'" 
                           onmouseout="this.style.color='#ddd'">
                            <?php echo $settings['email']; ?>
                        </a>
                    </div>
                </div>

                <!-- Harita -->
                <div>
                    <h3 style="margin-bottom: 20px; color: var(--accent);">Konum</h3>
                    <?php if($settings['map_embed']): ?>
                    <div style="border-radius: 10px; overflow: hidden; height: 200px;">
                        <iframe 
                            src="<?php echo htmlspecialchars($settings['map_embed']); ?>" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                    <?php else: ?>
                    <div style="height: 200px; background: rgba(255,255,255,0.1); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #ddd;">
                        <i class="fas fa-map-marker-alt" style="font-size: 3rem;"></i>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div style="border-top: 1px solid rgba(255,255,255,0.1); padding: 20px 0; text-align: center; color: #ddd;">
            <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $settings['site_name']; ?>. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTop" 
            style="position: fixed; bottom: 30px; right: 30px; 
                   background: var(--accent); color: white; border: none; 
                   width: 50px; height: 50px; border-radius: 50%; 
                   cursor: pointer; display: none; z-index: 999;
                   box-shadow: 0 4px 10px rgba(0,0,0,0.3);
                   transition: all 0.3s;"
            onclick="window.scrollTo({top: 0, behavior: 'smooth'});"
            onmouseover="this.style.transform='scale(1.1)'"
            onmouseout="this.style.transform='scale(1)'">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // Scroll to top button
        window.addEventListener('scroll', function() {
            const scrollTop = document.getElementById('scrollTop');
            if (window.pageYOffset > 300) {
                scrollTop.style.display = 'block';
            } else {
                scrollTop.style.display = 'none';
            }
        });
    </script>
</body>
</html>