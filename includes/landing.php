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

/* Navbar moderna e trasparente */
.navbar {
    width: 100vw;
    position: fixed;
    top: 0;
    left: 0;
    padding: clamp(1rem, 2vh, 1.5rem) clamp(2rem, 5vw, 4rem);
    margin: 0;
    background: rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    z-index: 1000;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.navbar.scrolled {
    background: var(--juventus-black);
    padding: 0.8rem clamp(2rem, 5vw, 4rem);
}

.navbar-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.navbar-brand img {
    height: 40px;
    transition: all 0.3s ease;
}

.nav-buttons {
    display: flex;
    gap: clamp(1rem, 2vw, 2rem);
    align-items: center;
}

.nav-link {
    color: var(--juventus-white);
    font-weight: 500;
    opacity: 0.9;
    transition: all 0.3s ease;
    text-decoration: none;
    padding: 0.5rem 1rem;
}

.nav-link:hover {
    opacity: 1;
    color: var(--juventus-gold);
}

.btn-navbar {
    font-size: clamp(0.9rem, 1.5vw, 1rem);
    font-weight: 600;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-navbar.login {
    color: var(--juventus-white);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-navbar.login:hover {
    border-color: var(--juventus-gold);
    color: var(--juventus-gold);
}

.btn-navbar.register {
    background: var(--juventus-gold);
    color: var(--juventus-black);
    border: 1px solid var(--juventus-gold);
}

.btn-navbar.register:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(197, 164, 126, 0.3);
}

/* Rimozione margini dal container principale */
.main-container {
    margin: 0;
    padding: 0;
    width: 100vw;
    min-height: 100vh;
    overflow: hidden;
}

/* Reset totale e fix per Bootstrap */
.container, 
.container-fluid,
.container-lg,
.container-md,
.container-sm,
.container-xl,
.container-xxl {
    padding-right: 0 !important;
    padding-left: 0 !important;
    margin-right: 0 !important;
    margin-left: 0 !important;
    max-width: none !important;
    width: 100vw !important;
}

.row {
    margin-right: 0 !important;
    margin-left: 0 !important;
}

.col,
[class*="col-"] {
    padding-right: 0 !important;
    padding-left: 0 !important;
}

/* Hero Section */
.hero-section {
    width: 100vw;
    height: 100vh;
    margin: 0;
    padding: 0;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw !important;
    margin-right: -50vw !important;
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)),
                url('<?php echo ASSETS_URL; ?>/images/juventus-stadium.jpg') no-repeat center center;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.hero-content {
    width: 100vw;
    text-align: center;
    position: absolute;
    left: 0;
    right: 0;
    padding: 0;
    margin: 0;
}

.hero-title {
    font-size: clamp(4rem, 15vw, 10rem);
    line-height: 1;
    font-weight: 900;
    text-transform: uppercase;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, var(--juventus-white) 0%, var(--juventus-gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    width: 100vw;
}

.hero-subtitle {
    width: 100%;
    font-size: clamp(1.2rem, 3vw, 1.8rem);
    margin: 2vh 0;
    padding: 0;
    color: var(--juventus-white);
}

.cta-buttons {
    width: 100%;
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin: 0;
    padding: 0;
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

/* Stili pulsanti migliorati */
.btn-primary-large {
    background: linear-gradient(135deg, var(--juventus-gold) 0%, darken(var(--juventus-gold), 15%) 100%);
    color: var(--juventus-black);
    border: none;
    font-size: clamp(0.9rem, 2vw, 1.1rem);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: clamp(0.8rem, 2vw, 1.2rem) clamp(1.5rem, 4vw, 3rem);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-width: clamp(200px, 30vw, 300px);
}

.btn-primary-large:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(197, 164, 126, 0.3);
    background: var(--juventus-gold);
}

.btn-secondary-large {
    background: transparent;
    color: var(--juventus-gold);
    border: 2px solid var(--juventus-gold);
    font-size: clamp(0.9rem, 2vw, 1.1rem);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: clamp(0.8rem, 2vw, 1.2rem) clamp(1.5rem, 4vw, 3rem);
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    min-width: clamp(200px, 30vw, 300px);
}

.btn-secondary-large:hover {
    background: var(--juventus-gold);
    color: var(--juventus-black);
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(197, 164, 126, 0.3);
}

/* Miglioramenti responsive per il titolo e sottotitolo */
.hero-title {
    font-size: clamp(3rem, 15vw, 8rem);
    letter-spacing: -2px;
    text-shadow: 0 0 30px rgba(0,0,0,0.5);
    margin-bottom: clamp(1rem, 3vh, 2rem);
}

.hero-subtitle {
    font-size: clamp(1rem, 2.5vw, 1.5rem);
    max-width: min(90%, 800px);
    margin: 0 auto;
    margin-bottom: clamp(2rem, 5vh, 4rem);
    line-height: 1.5;
    opacity: 0.9;
}

/* Responsive per i bottoni */
.cta-buttons {
    display: flex;
    gap: clamp(1rem, 2vw, 2rem);
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    width: min(90%, 800px);
    margin: 0 auto;
}

/* Navbar responsive */
.navbar {
    padding: clamp(0.5rem, 2vh, 1.5rem) clamp(1rem, 5vw, 3rem);
}

.nav-buttons {
    display: flex;
    gap: clamp(0.5rem, 2vw, 1rem);
}

.nav-buttons a {
    font-size: clamp(0.8rem, 1.5vw, 1rem);
}

/* Media queries ottimizzate */
@media (max-width: 768px) {
    .hero-content {
        padding: 0 clamp(1rem, 5vw, 2rem);
    }

    .cta-buttons {
        flex-direction: column;
        width: 100%;
        padding: 0 clamp(1rem, 5vw, 2rem);
    }

    .btn-primary-large,
    .btn-secondary-large {
        width: 100%;
        min-width: unset;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: clamp(2.5rem, 12vw, 4rem);
    }

    .hero-subtitle {
        font-size: clamp(0.9rem, 4vw, 1.2rem);
    }

    .nav-buttons {
        font-size: 0.9rem;
    }
}

/* Animazioni smooth */
.hero-content {
    animation: fadeIn 1s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<nav class="navbar">
    <div class="navbar-inner">
        <a class="navbar-brand" href="index.php">
            <img src="<?php echo ASSETS_URL; ?>/images/logo.png" alt="BiancoNeriHub">
            <span class="d-none d-sm-inline text-white">BiancoNeriHub</span>
        </a>
        <div class="nav-buttons">
            <a href="login.php" class="btn-navbar login">
                <i class="fas fa-sign-in-alt"></i>
                <span class="d-none d-sm-inline">Accedi</span>
            </a>
            <a href="register.php" class="btn-navbar register">
                <i class="fas fa-user-plus"></i>
                <span class="d-none d-sm-inline">Registrati</span>
            </a>
        </div>
    </div>
</nav>

<script>
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

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
