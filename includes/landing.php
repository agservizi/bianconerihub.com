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
/* Reset CSS totale */
*, *::before, *::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    margin: 0;
    padding: 0;
    width: 100%;
    height: 100%;
    overflow-x: hidden;
    background: var(--juventus-black);
}

/* Rimozione margini dal container principale */
.main-container {
    margin: 0;
    padding: 0;
    width: 100vw;
    min-height: 100vh;
    overflow: hidden;
}

/* Hero Section */
.hero-section {
    position: relative;
    width: 100vw;
    height: 100vh;
    margin: 0;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('<?php echo ASSETS_URL; ?>/images/juventus-stadium.jpg') no-repeat center center;
    background-size: cover;
    text-align: center;
}

.hero-content {
    width: 100%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 0;
    margin: 0;
}

.hero-title {
    font-size: clamp(4rem, 12vw, 8rem);
    font-weight: 900;
    line-height: 1;
    margin: 0;
    padding: 0;
    text-transform: uppercase;
    color: var(--juventus-white);
    letter-spacing: -2px;
    font-family: var(--font-heading);
}

.hero-subtitle {
    font-size: clamp(1.2rem, 3vw, 1.8rem);
    margin: 2vh 0;
    padding: 0 1rem;
    color: var(--juventus-white);
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
}

.cta-buttons {
    margin: 4vh 0 0 0;
    padding: 0;
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.btn-primary-large,
.btn-secondary-large {
    padding: 1rem 2.5rem;
    font-size: clamp(1rem, 2.5vw, 1.2rem);
    font-weight: 600;
    text-transform: uppercase;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary-large {
    background: var(--juventus-gold);
    color: var(--juventus-black);
}

.btn-secondary-large {
    background: transparent;
    border: 2px solid var(--juventus-gold);
    color: var(--juventus-gold);
}

/* Rimozione ogni spazio vuoto tra le sezioni */
section {
    width: 100vw;
    margin: 0;
    padding: 0;
}

/* Fix per il navbar */
.navbar {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    padding: 1rem 2rem;
    background: transparent;
    z-index: 1000;
}

/* Social Preview Section */
.social-preview {
    width: 100%;
    margin: 0;
    padding: 0;
    background: var(--juventus-black);
}

.preview-title {
    width: 100%;
    text-align: center;
    margin: 0;
    padding: 2vh 0;
    color: var(--juventus-white);
}

.feed-container {
    width: 100%;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 0;
}

.feed-post {
    margin: 0;
    padding: 0;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Features Section */
.features-grid {
    width: 100%;
    margin: 0;
    padding: 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 0;
    background: var(--juventus-black);
}

.feature-card {
    margin: 0;
    padding: 2vh;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Stats Section */
.stats-section {
    width: 100%;
    margin: 0;
    padding: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.95), rgba(0,0,0,0.98));
}

.stats-container {
    width: 100%;
    margin: 0;
    padding: 2vh 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}

.stat-item {
    text-align: center;
    margin: 0;
    padding: 2vh 0;
}

/* CTA Section */
.cta-section {
    width: 100%;
    margin: 0;
    padding: 0;
    background: var(--juventus-black);
}

.cta-content {
    width: 100%;
    margin: 0;
    padding: 2vh 0;
    text-align: center;
}

/* Media Queries */
@media (max-width: 768px) {
    .hero-content {
        width: 100%;
    }

    .cta-buttons {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        padding: 0 1rem;
    }

    .btn-primary-large,
    .btn-secondary-large {
        width: 100%;
        justify-content: center;
    }
}
</style>

<nav class="navbar">
    <a class="navbar-brand" href="index.php">
        <img src="<?php echo ASSETS_URL; ?>/images/logo.png" alt="BiancoNeriHub" height="40">
    </a>
    <div class="nav-buttons">
        <a href="login.php" class="btn btn-link text-white">Accedi</a>
        <a href="register.php" class="btn btn-outline-light">Registrati</a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">BIANCONERIHUB</h1>
        <p class="hero-subtitle">Il social network dedicato ai veri tifosi della Juventus. 
        Unisciti alla community più appassionata d'Italia e condividi la tua fede bianconera.</p>
        <div class="cta-buttons">
            <a href="register.php" class="btn-primary-large">
                <i class="fas fa-user-plus"></i> UNISCITI ORA
            </a>
            <a href="#features" class="btn-secondary-large">
                <i class="fas fa-chevron-down"></i> SCOPRI DI PIÙ
            </a>
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
