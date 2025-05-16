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
/* Reset stili per full screen */
body, html {
    margin: 0;
    padding: 0;
    overflow-x: hidden;
    background: var(--juventus-black);
    color: var(--juventus-white);
}

/* Hero Section */
.hero-section {
    position: relative;
    min-height: 100vh;
    width: 100vw;
    background: linear-gradient(rgba(0, 0, 0, 0.85), rgba(0, 0, 0, 0.85)),
                url('<?php echo ASSETS_URL; ?>/images/juventus-stadium.jpg') no-repeat center center;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0;
    padding: 0;
}

.hero-stripe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: repeating-linear-gradient(
        45deg,
        rgba(255, 255, 255, 0.03) 0px,
        rgba(255, 255, 255, 0.03) 1px,
        transparent 1px,
        transparent 10px
    );
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 1200px;
    padding: 0 2rem;
}

.hero-title {
    font-size: clamp(4rem, 10vw, 8rem);
    font-weight: 900;
    text-transform: uppercase;
    margin-bottom: 1.5rem;
    line-height: 1;
    background: linear-gradient(135deg, var(--juventus-white) 0%, var(--juventus-gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 30px rgba(197, 164, 126, 0.3);
}

.hero-subtitle {
    font-size: clamp(1.2rem, 3vw, 1.8rem);
    font-weight: 400;
    margin-bottom: 3rem;
    max-width: 800px;
    margin-left: auto;
    margin-right: auto;
    color: var(--juventus-white);
    opacity: 0.9;
}

/* Social Feed Preview */
.social-preview {
    background: var(--juventus-black);
    padding: 6rem 0;
    position: relative;
    width: 100vw;
}

.preview-title {
    text-align: center;
    font-size: 3rem;
    font-weight: 800;
    color: var(--juventus-white);
    margin-bottom: 4rem;
    text-transform: uppercase;
}

.feed-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

.feed-post {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    overflow: hidden;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.feed-post:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(197, 164, 126, 0.2);
    border-color: var(--juventus-gold);
}

.post-content {
    padding: 1.5rem;
}

.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.post-text {
    color: var(--juventus-white);
    opacity: 0.9;
}

/* Features Grid */
.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 3rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 6rem 2rem;
    background: var(--juventus-black);
}

.feature-card {
    background: rgba(255, 255, 255, 0.05);
    padding: 2rem;
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    text-align: left;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.feature-card:hover {
    border-color: var(--juventus-gold);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(197, 164, 126, 0.2);
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
    color: var(--juventus-white);
}

.feature-text {
    color: var(--juventus-white);
    opacity: 0.8;
    line-height: 1.6;
}

/* Stats Section */
.stats-section {
    background: linear-gradient(45deg, rgba(0,0,0,0.95), rgba(0,0,0,0.98)),
                url('<?php echo ASSETS_URL; ?>/images/juventus-stadium.jpg') no-repeat center center;
    background-size: cover;
    padding: 6rem 0;
    width: 100vw;
}

.stats-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 4rem;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2rem;
}

.stat-number {
    font-size: 4rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--juventus-white) 0%, var(--juventus-gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: 0 0 30px rgba(197, 164, 126, 0.3);
}

/* CTA Section */
.cta-section {
    background: var(--juventus-black);
    padding: 6rem 0;
    width: 100vw;
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 2rem;
}

.cta-title {
    font-size: 3rem;
    font-weight: 800;
    color: var(--juventus-white);
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
    padding: 1.2rem 3.5rem;
    font-size: 1.2rem;
    font-weight: 600;
    background: linear-gradient(135deg, var(--juventus-gold) 0%, darken(var(--juventus-gold), 20%) 100%);
    color: var(--juventus-black);
    border: none;
    border-radius: 50px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-primary-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px rgba(197, 164, 126, 0.3);
    color: var(--juventus-black);
}

.btn-secondary-large {
    padding: 1.2rem 3.5rem;
    font-size: 1.2rem;
    font-weight: 600;
    background: transparent;
    color: var(--juventus-gold);
    border: 2px solid var(--juventus-gold);
    border-radius: 50px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.btn-secondary-large:hover {
    background: var(--juventus-gold);
    color: var(--juventus-black);
    box-shadow: 0 10px 30px rgba(197, 164, 126, 0.3);
}

/* Effetti di scroll */
.scroll-reveal {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease;
}

.scroll-reveal.visible {
    opacity: 1;
    transform: translateY(0);
}

@media (max-width: 768px) {
    .hero-content {
        padding: 0 1rem;
    }
    
    .preview-title,
    .cta-title {
        font-size: 2rem;
    }
    
    .btn-primary-large,
    .btn-secondary-large {
        width: 100%;
        margin: 0.5rem 0;
    }
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-stripe"></div>
    <div class="hero-content scroll-reveal">
        <h1 class="hero-title">BiancoNeriHub</h1>
        <p class="hero-subtitle">Il social network dedicato ai veri tifosi della Juventus. 
        Unisciti alla community più appassionata d'Italia e condividi la tua fede bianconera.</p>
        <div class="cta-buttons">
            <a href="register.php" class="btn-primary-large">
                <i class="fas fa-user-plus"></i> Unisciti Ora
            </a>
            <a href="#features" class="btn-secondary-large">
                <i class="fas fa-chevron-down"></i> Scopri di Più
            </a>
        </div>
    </div>
    <div class="scroll-indicator">
        <span class="mouse"></span>
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

document.addEventListener('DOMContentLoaded', function() {
    // Animazione elementi allo scroll
    const scrollElements = document.querySelectorAll('.scroll-reveal');
    
    const elementInView = (el, offset = 0) => {
        const elementTop = el.getBoundingClientRect().top;
        return (elementTop <= (window.innerHeight || document.documentElement.clientHeight) * (1 - offset));
    };
    
    const displayScrollElement = (element) => {
        element.classList.add('visible');
    };
    
    const handleScrollAnimation = () => {
        scrollElements.forEach((el) => {
            if (elementInView(el, 0.25)) {
                displayScrollElement(el);
            }
        });
    }
    
    window.addEventListener('scroll', () => {
        handleScrollAnimation();
    });
    
    // Trigger iniziale
    handleScrollAnimation();
});
</script>
