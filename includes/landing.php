<?php
/**
 * Landing page per utenti non autenticati
 * BiancoNeriHub - Social network per tifosi della Juventus
 */
?>

<!-- Font e librerie -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>

<style>
/* Reset e variabili già definite in style.css */

/* Hero Section */
.hero-section {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('<?php echo ASSETS_URL; ?>/images/juventus-stadium.jpg') no-repeat center center;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--juventus-white);
    overflow: hidden;
}

.hero-stripe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: repeating-linear-gradient(
        45deg,
        rgba(0, 0, 0, 0.1),
        rgba(0, 0, 0, 0.1) 10px,
        rgba(255, 255, 255, 0.1) 10px,
        rgba(255, 255, 255, 0.1) 20px
    );
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 1200px;
    padding: 2rem;
}

.hero-title {
    font-size: clamp(3rem, 8vw, 6rem);
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 1.5rem;
    line-height: 1;
    position: relative;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}

.hero-subtitle {
    font-size: clamp(1.2rem, 3vw, 1.8rem);
    font-weight: 400;
    margin-bottom: 3rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    opacity: 0.9;
}

/* Social Feed Preview */
.social-preview {
    background: var(--juventus-white);
    padding: 6rem 0;
    position: relative;
    overflow: hidden;
}

.preview-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--juventus-black);
    margin-bottom: 4rem;
}

.feed-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.feed-post {
    background: var(--juventus-white);
    border-radius: 15px;
    overflow: hidden;
    box-shadow: var(--shadow-md);
    transition: transform 0.3s ease;
}

.feed-post:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
}

.post-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.post-content {
    padding: 1.5rem;
}

.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.post-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 1rem;
}

.post-user {
    font-weight: 600;
    color: var(--juventus-black);
}

.post-text {
    color: var(--juventus-gray);
    margin-bottom: 1rem;
    line-height: 1.5;
}

.post-stats {
    display: flex;
    gap: 1rem;
    color: var(--juventus-gray);
    font-size: 0.9rem;
}

/* Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 3rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 6rem 2rem;
    background: var(--juventus-light-gray);
}

.feature-card {
    background: var(--juventus-white);
    padding: 2rem;
    border-radius: 15px;
    text-align: left;
    box-shadow: var(--shadow-md);
    display: flex;
    flex-direction: column;
}

.feature-icon {
    font-size: 2.5rem;
    color: var(--juventus-gold);
    margin-bottom: 1.5rem;
}

.feature-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--juventus-black);
}

.feature-text {
    color: var(--juventus-gray);
    line-height: 1.6;
}

/* Community Stats */
.stats-section {
    background: var(--juventus-gradient);
    padding: 4rem 0;
    color: var(--juventus-white);
    text-align: center;
}

.stats-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 4rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
}

.stat-item {
    flex: 1;
    min-width: 200px;
}

.stat-number {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    background: linear-gradient(135deg, var(--juventus-white) 0%, var(--juventus-gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-label {
    font-size: 1.2rem;
    opacity: 0.9;
}

/* CTA Section */
.cta-section {
    padding: 6rem 0;
    background: var(--juventus-white);
    text-align: center;
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
}

.cta-title {
    font-size: 3rem;
    font-weight: 800;
    color: var(--juventus-black);
    margin-bottom: 1.5rem;
}

.cta-text {
    font-size: 1.2rem;
    color: var(--juventus-gray);
    margin-bottom: 2rem;
    line-height: 1.6;
}

.cta-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-primary-large {
    padding: 1rem 3rem;
    font-size: 1.2rem;
    font-weight: 600;
    background: var(--juventus-gradient);
    color: var(--juventus-white);
    border: none;
    border-radius: 50px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-primary-large:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--juventus-white);
}

.btn-secondary-large {
    padding: 1rem 3rem;
    font-size: 1.2rem;
    font-weight: 600;
    background: transparent;
    color: var(--juventus-black);
    border: 2px solid var(--juventus-black);
    border-radius: 50px;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-secondary-large:hover {
    background: var(--juventus-black);
    color: var(--juventus-white);
}

@media (max-width: 768px) {
    .hero-content {
        padding: 1rem;
    }
    
    .feed-container {
        grid-template-columns: 1fr;
    }
    
    .stats-container {
        gap: 2rem;
    }
    
    .stat-item {
        flex: 0 0 100%;
    }
    
    .cta-buttons {
        flex-direction: column;
    }
    
    .btn-primary-large,
    .btn-secondary-large {
        width: 100%;
    }
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-stripe"></div>
    <div class="hero-content">
        <h1 class="hero-title">BiancoNeriHub</h1>
        <p class="hero-subtitle">Il social network dedicato ai veri tifosi della Juventus. 
        Unisciti alla community più appassionata d'Italia e condividi la tua fede bianconera.</p>
        <div class="cta-buttons">
            <a href="register.php" class="btn-primary-large">Unisciti alla Community</a>
            <a href="#features" class="btn-secondary-large">Scopri di Più</a>
        </div>
    </div>
</section>

<!-- Social Feed Preview -->
<section class="social-preview">
    <h2 class="preview-title">Scopri cosa condividono i tifosi</h2>
    <div class="feed-container">
        <!-- Post 1 -->
        <div class="feed-post">
            <img src="<?php echo ASSETS_URL; ?>/images/post-1.jpg" alt="Post sulla Juventus" class="post-image">
            <div class="post-content">
                <div class="post-header">
                    <img src="<?php echo ASSETS_URL; ?>/images/avatar-1.jpg" alt="Avatar" class="post-avatar">
                    <div class="post-user">Marco B.</div>
                </div>
                <p class="post-text">Che vittoria incredibile ieri sera! Questa squadra ha un cuore immenso 🖤⚪️ #ForzaJuve</p>
                <div class="post-stats">
                    <span><i class="fas fa-heart"></i> 234</span>
                    <span><i class="fas fa-comment"></i> 45</span>
                </div>
            </div>
        </div>
        
        <!-- Post 2 -->
        <div class="feed-post">
            <img src="<?php echo ASSETS_URL; ?>/images/post-2.jpg" alt="Post sulla Juventus" class="post-image">
            <div class="post-content">
                <div class="post-header">
                    <img src="<?php echo ASSETS_URL; ?>/images/avatar-2.jpg" alt="Avatar" class="post-avatar">
                    <div class="post-user">Laura M.</div>
                </div>
                <p class="post-text">Primo match all'Allianz Stadium! Un'emozione unica vivere la partita con altri tifosi 🏟️ #JuventusStadium</p>
                <div class="post-stats">
                    <span><i class="fas fa-heart"></i> 156</span>
                    <span><i class="fas fa-comment"></i> 28</span>
                </div>
            </div>
        </div>
        
        <!-- Post 3 -->
        <div class="feed-post">
            <img src="<?php echo ASSETS_URL; ?>/images/post-3.jpg" alt="Post sulla Juventus" class="post-image">
            <div class="post-content">
                <div class="post-header">
                    <img src="<?php echo ASSETS_URL; ?>/images/avatar-3.jpg" alt="Avatar" class="post-avatar">
                    <div class="post-user">Alessandro R.</div>
                </div>
                <p class="post-text">33 scudetti sul campo, una storia che parla da sola. Orgoglioso di essere juventino! ⭐️⭐️</p>
                <div class="post-stats">
                    <span><i class="fas fa-heart"></i> 312</span>
                    <span><i class="fas fa-comment"></i> 67</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-grid">
    <div class="feature-card">
        <i class="fas fa-users feature-icon"></i>
        <h3 class="feature-title">Community Esclusiva</h3>
        <p class="feature-text">Connettiti con migliaia di tifosi juventini, condividi la tua passione e resta aggiornato sulle ultime novità del mondo bianconero.</p>
    </div>
    
    <div class="feature-card">
        <i class="fas fa-calendar-alt feature-icon"></i>
        <h3 class="feature-title">Eventi dal Vivo</h3>
        <p class="feature-text">Organizza e partecipa a eventi per guardare le partite insieme ad altri tifosi nella tua città.</p>
    </div>
    
    <div class="feature-card">
        <i class="fas fa-camera-retro feature-icon"></i>
        <h3 class="feature-title">Contenuti Esclusivi</h3>
        <p class="feature-text">Condividi foto, video e momenti speciali della tua passione bianconera con una community che capisce il tuo amore per la Juve.</p>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="stats-container">
        <div class="stat-item">
            <div class="stat-number" data-count="50000">50.000+</div>
            <div class="stat-label">Tifosi Iscritti</div>
        </div>
        <div class="stat-item">
            <div class="stat-number" data-count="100000">100.000+</div>
            <div class="stat-label">Post Condivisi</div>
        </div>
        <div class="stat-item">
            <div class="stat-number" data-count="5000">5.000+</div>
            <div class="stat-label">Eventi Organizzati</div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-content">
        <h2 class="cta-title">Entra nella Community Bianconera</h2>
        <p class="cta-text">Unisciti a migliaia di tifosi che condividono la tua stessa passione. BiancoNeriHub è più di un social network: è la tua casa bianconera digitale.</p>
        <div class="cta-buttons">
            <a href="register.php" class="btn-primary-large">Registrati Ora</a>
            <a href="about.php" class="btn-secondary-large">Scopri di Più</a>
        </div>
    </div>
</section>

<script>
// Animazioni al caricamento
document.addEventListener('DOMContentLoaded', function() {
    // Animazione numeri statistiche
    const stats = document.querySelectorAll('.stat-number');
    stats.forEach(stat => {
        const target = parseInt(stat.getAttribute('data-count'));
        let current = 0;
        const increment = target / 100;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                clearInterval(timer);
                current = target;
            }
            stat.textContent = Math.floor(current).toLocaleString() + '+';
        }, 20);
    });
    
    // Parallax effect sulla hero section
    document.addEventListener('mousemove', (e) => {
        const moveX = (e.clientX * -0.01);
        const moveY = (e.clientY * -0.01);
        document.querySelector('.hero-section').style.backgroundPosition = `calc(50% + ${moveX}px) calc(50% + ${moveY}px)`;
    });
});
</script>
