<?php
/**
 * Landing page per utenti non autenticati
 * BiancoNeriHub - Social network per tifosi della Juventus
 */
?>

<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<!-- Stili CSS precedentemente aggiunti -->
<style>
/* Stili per la landing page interattiva */
.hero-section {
    position: relative;
    background: var(--juventus-gradient),
                linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7)), 
                url('<?php echo ASSETS_URL; ?>/images/juventus-stadium.jpg') no-repeat center center;
    background-size: cover;
    height: 100vh;
    min-height: 700px;
    color: var(--juventus-white);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    padding: 2rem;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('<?php echo ASSETS_URL; ?>/images/jersey-pattern.png');
    opacity: 0.05;
    z-index: 1;
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    max-width: 1000px;
    padding: 2rem;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: var(--shadow-lg);
}

.hero-title {
    font-family: var(--font-heading);
    font-size: clamp(3rem, 8vw, 5rem);
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -2px;
    margin-bottom: 1.5rem;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    animation: fadeInDown 1.2s ease-out;
    background: linear-gradient(135deg, var(--juventus-white) 0%, var(--juventus-gold) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    line-height: 1.1;
}

.hero-subtitle {
    font-family: var(--font-body);
    font-size: clamp(1.2rem, 4vw, 1.5rem);
    font-weight: 400;
    margin-bottom: 3rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    animation: fadeInUp 1.2s ease-out;
    opacity: 0.9;
    max-width: 80%;
    margin-left: auto;
    margin-right: auto;
}

.trophy-slider {
    position: absolute;
    bottom: 30px;
    left: 0;
    width: 100%;
    height: 120px;
    overflow: hidden;
    z-index: 2;
}

.trophy-slider .slider-container {
    display: flex;
    animation: slideRight 30s linear infinite;
    width: max-content;
}

.trophy-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin: 0 30px;
    opacity: 0.7;
    transition: all 0.3s ease;
}

.trophy-item:hover {
    opacity: 1;
    transform: translateY(-10px);
}

.trophy-item img {
    height: 70px;
    margin-bottom: 10px;
}

.trophy-item span {
    color: var(--juventus-white);
    font-size: 0.8rem;
    text-align: center;
}

.feature-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
    z-index: 1;
}

.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 6px;
    background: linear-gradient(90deg, var(--juventus-black) 50%, var(--juventus-white) 50%);
    z-index: -1;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.feature-icon {
    background: var(--juventus-black);
    color: var(--juventus-white);
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.feature-card:hover .feature-icon {
    transform: rotateY(360deg);
}

.stats-section {
    background: linear-gradient(135deg, var(--juventus-black) 0%, #333 100%);
    position: relative;
    overflow: hidden;
}

.stats-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('<?php echo ASSETS_URL; ?>/images/pattern.png');
    opacity: 0.05;
}

.stat-item {
    text-align: center;
    padding: 30px;
    position: relative;
}

.stat-number {
    font-size: 3.5rem;
    font-weight: 900;
    margin-bottom: 0;
    background: linear-gradient(to right, var(--juventus-white), var(--juventus-gold));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: inline-block;
}

.stat-label {
    color: var(--juventus-white);
    font-size: 1.2rem;
    font-weight: 600;
}

.testimonial-slider {
    position: relative;
    padding: 60px 0;
}

.testimonial-card {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin: 15px;
    position: relative;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.testimonial-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.testimonial-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--juventus-black);
    margin-bottom: 15px;
}

.parallax-section {
    height: 400px;
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.parallax-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
}

.parallax-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: var(--juventus-white);
    max-width: 800px;
    padding: 0 20px;
}

.stadium-section {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.stadium-tour {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 1s ease-in-out;
}

.stadium-tour.active {
    opacity: 1;
}

.stadium-tour img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.stadium-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(rgba(0, 0, 0, 0), rgba(0, 0, 0, 0.8));
    display: flex;
    align-items: flex-end;
    padding: 40px;
    color: white;
}

.faq-card {
    border-radius: 15px;
    overflow: hidden;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
}

.faq-card:hover {
    transform: translateY(-5px);
}

.faq-header {
    background: var(--juventus-black);
    color: white;
    padding: 15px 25px;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.faq-content {
    padding: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.5s ease, padding 0.5s ease;
}

.faq-content.open {
    padding: 25px;
    max-height: 300px;
}

.news-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    position: relative;
    margin-bottom: 30px;
    transition: transform 0.3s ease;
}

.news-card:hover {
    transform: translateY(-10px);
}

.news-img {
    height: 200px;
    object-fit: cover;
}

.news-content {
    padding: 20px;
}

.news-date {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--juventus-black);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.8rem;
}

.cta-section {
    background: linear-gradient(135deg, var(--juventus-gold) 0%, #e0c79b 100%);
    border-radius: 15px;
    padding: 60px 30px;
    position: relative;
    overflow: hidden;
}

.cta-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url('<?php echo ASSETS_URL; ?>/images/pattern-light.png');
    opacity: 0.1;
}

.social-proof {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}

.avatar-group {
    display: flex;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-left: -10px;
}

.avatar:first-child {
    margin-left: 0;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.newsletter-form {
    background: var(--juventus-white);
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.newsletter-form::before {
    content: '';
    position: absolute;
    width: 150px;
    height: 150px;
    background: var(--juventus-gold);
    opacity: 0.1;
    border-radius: 50%;
    top: -75px;
    left: -75px;
}

.newsletter-form::after {
    content: '';
    position: absolute;
    width: 100px;
    height: 100px;
    background: var(--juventus-black);
    opacity: 0.1;
    border-radius: 50%;
    bottom: -50px;
    right: -50px;
}

.app-mockup {
    position: relative;
    z-index: 1;
    transform: perspective(500px) rotateY(-15deg);
    transition: transform 0.5s ease;
}

.app-mockup:hover {
    transform: perspective(500px) rotateY(0deg);
}

.champions-trophy {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
    100% {
        transform: translateY(0px);
    }
}

@keyframes slideRight {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-50%);
    }
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

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

.animate-on-scroll.fade-in {
    opacity: 1;
    transform: translateY(0);
}

.cta-container {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    animation: fadeInUp 1.2s ease-out 0.3s both;
}

.cta-button {
    padding: 1rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 600;
    text-transform: uppercase;
    border-radius: 50px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    font-family: var(--font-body);
}

.cta-primary {
    background: var(--juventus-white);
    color: var(--juventus-black);
    border: none;
    box-shadow: var(--shadow-md);
}

.cta-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    background: var(--juventus-gold);
    color: var(--juventus-black);
}

.cta-secondary {
    background: transparent;
    color: var(--juventus-white);
    border: 2px solid var(--juventus-white);
}

.cta-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateY(-2px);
}

/* Effetto particelle sullo sfondo */
.particles {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    pointer-events: none;
}

.particle {
    position: absolute;
    background: var(--juventus-white);
    border-radius: 50%;
    opacity: 0.1;
    animation: float 20s infinite linear;
}

/* Nuovi stili per le sezioni aggiuntive */
.features-section {
    padding: 6rem 0;
    background: var(--juventus-white);
}

.section-title {
    text-align: center;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 4rem;
    color: var(--juventus-black);
    position: relative;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: var(--juventus-gold);
    border-radius: 2px;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    padding: 0 1rem;
}

.feature-card {
    background: var(--juventus-white);
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-md);
    border: 1px solid rgba(0,0,0,0.05);
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: var(--shadow-lg);
}

.feature-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--juventus-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.feature-icon i {
    font-size: 2rem;
    color: var(--juventus-white);
}

.feature-card h3 {
    color: var(--juventus-black);
    font-size: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.feature-card p {
    color: var(--juventus-gray);
    font-size: 1rem;
    line-height: 1.6;
}

/* Stats Section */
.stats-section {
    background: var(--juventus-gradient);
    padding: 4rem 0;
    color: var(--juventus-white);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    text-align: center;
}

.stat-item {
    padding: 2rem;
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

/* Community Section */
.community-section {
    padding: 6rem 0;
    background: var(--juventus-light-gray);
    overflow: hidden;
}

.community-section .container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.community-content h2 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 1.5rem;
    color: var(--juventus-black);
}

.community-content p {
    font-size: 1.1rem;
    line-height: 1.7;
    color: var(--juventus-gray);
    margin-bottom: 2rem;
}

.community-features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.feature-item i {
    color: var(--juventus-gold);
    font-size: 1.2rem;
}

.community-image {
    position: relative;
}

.community-image img {
    width: 100%;
    border-radius: 20px;
    box-shadow: var(--shadow-lg);
}

.btn-join {
    display: inline-block;
    padding: 1rem 2.5rem;
    background: var(--juventus-gradient);
    color: var(--juventus-white);
    text-decoration: none;
    border-radius: 50px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-md);
}

.btn-join:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    color: var(--juventus-white);
}

/* Scroll Indicator */
.scroll-indicator {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    animation: bounce 2s infinite;
    z-index: 2;
}

.mouse {
    width: 30px;
    height: 50px;
    border: 2px solid var(--juventus-white);
    border-radius: 15px;
    position: relative;
}

.mouse::before {
    content: '';
    position: absolute;
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    width: 4px;
    height: 8px;
    background: var(--juventus-white);
    border-radius: 2px;
    animation: scroll 1.5s infinite;
}

.scroll-indicator span {
    color: var(--juventus-white);
    font-size: 0.9rem;
    opacity: 0.8;
}

@keyframes scroll {
    0% { transform: translate(-50%, 0); opacity: 1; }
    100% { transform: translate(-50%, 15px); opacity: 0; }
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0) translateX(-50%); }
    40% { transform: translateY(-10px) translateX(-50%); }
    60% { transform: translateY(-5px) translateX(-50%); }
}

/* Responsive Design */
@media (max-width: 991px) {
    .community-section .container {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .community-features {
        justify-content: center;
    }
    
    .feature-item {
        justify-content: center;
    }
    
    .community-image {
        margin-top: 2rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
}

@media (max-width: 768px) {
    .section-title {
        font-size: 2rem;
    }
    
    .features-grid {
        grid-template-columns: 1fr;
        max-width: 400px;
        margin: 0 auto;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .stat-label {
        font-size: 1rem;
    }
    
    .community-content h2 {
        font-size: 2rem;
    }
}
</style>

<!-- Hero Section -->
<div class="hero-section">
    <div class="particles">
        <?php for($i = 0; $i < 50; $i++): ?>
            <div class="particle"></div>
        <?php endfor; ?>
    </div>
    
    <div class="hero-content">
        <h1 class="hero-title">BiancoNeriHub</h1>
        <p class="hero-subtitle">La casa digitale dei tifosi bianconeri. Unisciti alla community più appassionata del calcio italiano.</p>
        <div class="cta-container">
            <a href="register.php" class="cta-button cta-primary">
                <i class="fas fa-user-plus"></i>
                Unisciti Ora
            </a>
            <a href="#features" class="cta-button cta-secondary">
                <i class="fas fa-info-circle"></i>
                Scopri di più
            </a>
        </div>
    </div>
    
    <div class="scroll-indicator">
        <div class="mouse"></div>
        <span>Scorri per scoprire</span>
    </div>
</div>

<!-- Features Section -->
<section id="features" class="features-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Cosa offre BiancoNeriHub</h2>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Community Globale</h3>
                <p>Connettiti con tifosi da tutto il mondo e condividi la tua passione per la Juventus</p>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3>Eventi Dal Vivo</h3>
                <p>Organizza e partecipa a eventi per guardare le partite insieme ad altri tifosi</p>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon">
                    <i class="fas fa-comment-alt"></i>
                </div>
                <h3>Discussioni</h3>
                <p>Partecipa a discussioni animate su tattiche, trasferimenti e molto altro</p>
            </div>
            
            <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h3>Storia e Tradizione</h3>
                <p>Esplora la ricca storia del club attraverso contenuti esclusivi e memorie condivise</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item" data-aos="fade-up">
                <div class="stat-number" data-count="50000">0</div>
                <div class="stat-label">Membri della community</div>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-number" data-count="1000">0</div>
                <div class="stat-label">Eventi organizzati</div>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-number" data-count="100000">0</div>
                <div class="stat-label">Post condivisi</div>
            </div>
        </div>
    </div>
</section>

<!-- Community Section -->
<section class="community-section">
    <div class="container">
        <div class="community-content" data-aos="fade-right">
            <h2>Entra nella Community</h2>
            <p>Unisciti a migliaia di tifosi che condividono la tua stessa passione. BiancoNeriHub è più di un social network: è la tua casa bianconera digitale.</p>
            <div class="community-features">
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span>Profilo personalizzato</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span>Messaggistica diretta</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span>Notifiche in tempo reale</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span>Contenuti esclusivi</span>
                </div>
            </div>
            <a href="register.php" class="btn-join">Crea il tuo profilo</a>
        </div>
        <div class="community-image" data-aos="fade-left">
            <img src="<?php echo ASSETS_URL; ?>/images/community.jpg" alt="Community BiancoNeriHub">
        </div>
    </div>
</section>

<!-- Sezione stadio interattivo -->
<div class="stadium-section mb-5 animate-on-scroll">
    <div class="stadium-tour active" data-area="overview">
        <img src="<?php echo ASSETS_URL; ?>/images/stadium/overview.jpg" alt="Stadium Overview">
        <div class="stadium-overlay">
            <div>
                <h3>Juventus Stadium</h3>
                <p>La casa dei nostri sogni e delle nostre vittorie</p>
            </div>
        </div>
    </div>
    <div class="stadium-tour" data-area="curva-sud">
        <img src="<?php echo ASSETS_URL; ?>/images/stadium/curva-sud.jpg" alt="Curva Sud">
        <div class="stadium-overlay">
            <div>
                <h3>Curva Sud</h3>
                <p>Il cuore pulsante del tifo bianconero</p>
            </div>
        </div>
    </div>
    <div class="stadium-tour" data-area="spogliatoi">
        <img src="<?php echo ASSETS_URL; ?>/images/stadium/spogliatoi.jpg" alt="Spogliatoi">
        <div class="stadium-overlay">
            <div>
                <h3>Spogliatoi</h3>
                <p>Dove i campioni si preparano per la battaglia</p>
            </div>
        </div>
    </div>
    <div class="stadium-tour" data-area="campo">
        <img src="<?php echo ASSETS_URL; ?>/images/stadium/campo.jpg" alt="Campo">
        <div class="stadium-overlay">
            <div>
                <h3>Il Campo</h3>
                <p>Dove la magia prende vita</p>
            </div>
        </div>
    </div>
    <div class="stadium-tour" data-area="museo">
        <img src="<?php echo ASSETS_URL; ?>/images/stadium/museo.jpg" alt="Museo">
        <div class="stadium-overlay">
            <div>
                <h3>Museo della Juventus</h3>
                <p>La storia gloriosa raccontata attraverso i trofei</p>
            </div>
        </div>
    </div>
    
    <div class="stadium-controls position-absolute bottom-0 start-0 end-0 p-3 d-flex justify-content-center" style="z-index: 3;">
        <button class="btn btn-sm btn-light mx-1 stadium-btn" data-area="overview">Panoramica</button>
        <button class="btn btn-sm btn-light mx-1 stadium-btn" data-area="curva-sud">Curva Sud</button>
        <button class="btn btn-sm btn-light mx-1 stadium-btn" data-area="spogliatoi">Spogliatoi</button>
        <button class="btn btn-sm btn-light mx-1 stadium-btn" data-area="campo">Campo</button>
        <button class="btn btn-sm btn-light mx-1 stadium-btn" data-area="museo">Museo</button>
    </div>
</div>

<!-- Sezione statistiche -->
<div class="stats-section py-5 mb-5">
    <div class="container">        <div class="row text-center">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item animate-on-scroll">
                    <h2 class="stat-number" data-count="10000">0</h2>
                    <p class="stat-label">Tifosi registrati</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item animate-on-scroll" data-delay="200">
                    <h2 class="stat-number" data-count="50000">0</h2>
                    <p class="stat-label">Post condivisi</p>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-item animate-on-scroll" data-delay="400">
                    <h2 class="stat-number" data-count="5000">0</h2>
                    <p class="stat-label">Foto e video</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-item animate-on-scroll" data-delay="600">
                    <h2 class="stat-number" data-count="1000">0</h2>
                    <p class="stat-label">Eventi organizzati</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sezione parallax -->
<div class="parallax-section mb-5 animate-on-scroll" style="background-image: url('<?php echo ASSETS_URL; ?>/images/juventus-celebration.jpg');">
    <div class="parallax-content">
        <h2 class="display-4 fw-bold mb-4">Fino alla Fine</h2>
        <p class="lead mb-4">La Juventus non è solo una squadra, è uno stile di vita. Se sei juventino fino al midollo, BiancoNeriHub è il posto giusto per te.</p>
        <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-lg btn-primary">Unisciti ora</a>
    </div>
</div>

<!-- Sezione ultime news -->
<div class="container mb-5">
    <div class="row text-center mb-5 animate-on-scroll">
        <div class="col-12">
            <h2 class="display-4 fw-bold mb-3">Le ultime dalla Juventus</h2>
            <p class="lead text-muted">News, aggiornamenti e discussioni sempre fresche</p>
            <div class="d-flex justify-content-center mt-4">
                <div style="width: 100px; height: 4px; background: var(--juventus-gold);"></div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4 animate-on-scroll">
            <div class="news-card h-100">
                <img src="<?php echo ASSETS_URL; ?>/images/news/news1.jpg" alt="News 1" class="news-img w-100">
                <span class="news-date">14 Maggio 2025</span>
                <div class="news-content">
                    <h5>La Juventus trionfa in Champions League</h5>
                    <p class="text-muted">Una vittoria straordinaria per i bianconeri che alzano la terza Champions della loro storia...</p>
                    <a href="#" class="btn btn-sm btn-outline-dark mt-2">Leggi di più</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4 animate-on-scroll" data-delay="200">
            <div class="news-card h-100">
                <img src="<?php echo ASSETS_URL; ?>/images/news/news2.jpg" alt="News 2" class="news-img w-100">
                <span class="news-date">10 Maggio 2025</span>
                <div class="news-content">
                    <h5>Il nuovo acquisto che rivoluzionerà l'attacco</h5>
                    <p class="text-muted">Ufficiale l'acquisto del giovane fenomeno che si unirà alla squadra a partire dalla prossima stagione...</p>
                    <a href="#" class="btn btn-sm btn-outline-dark mt-2">Leggi di più</a>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6 mb-4 animate-on-scroll" data-delay="400">
            <div class="news-card h-100">
                <img src="<?php echo ASSETS_URL; ?>/images/news/news3.jpg" alt="News 3" class="news-img w-100">
                <span class="news-date">5 Maggio 2025</span>
                <div class="news-content">
                    <h5>L'intervista esclusiva al capitano</h5>
                    <p class="text-muted">Il capitano si racconta in un'intervista esclusiva: "Il segreto del nostro successo è l'unità del gruppo"...</p>
                    <a href="#" class="btn btn-sm btn-outline-dark mt-2">Leggi di più</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sezione testimonianze -->
<div class="container mb-5">
    <div class="row text-center mb-5 animate-on-scroll">
        <div class="col-12">
            <h2 class="display-4 fw-bold mb-3">La voce dei tifosi</h2>
            <p class="lead text-muted">Scopri cosa dicono i membri della nostra community</p>
            <div class="d-flex justify-content-center mt-4">
                <div style="width: 100px; height: 4px; background: var(--juventus-gold);"></div>
            </div>
        </div>
    </div>    
    <div class="testimonial-slider animate-on-scroll">
        <div class="owl-carousel">
            <div class="testimonial-card">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo ASSETS_URL; ?>/images/testimoni/user1.jpg" alt="Marco" class="testimonial-img">
                    <div class="ms-3">
                        <h5 class="mb-0">Marco Bianchi</h5>
                        <p class="text-muted mb-0">Milano, 32 anni</p>
                    </div>
                </div>
                <p>"BiancoNeriHub mi ha permesso di connettermi con tifosi juventini di tutto il mondo. Ora ho amici con cui guardo le partite in videochiamata!"</p>
                <div class="text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo ASSETS_URL; ?>/images/testimoni/user2.jpg" alt="Laura" class="testimonial-img">
                    <div class="ms-3">
                        <h5 class="mb-0">Laura Neri</h5>
                        <p class="text-muted mb-0">Roma, 27 anni</p>
                    </div>
                </div>
                <p>"Finalmente un social network pensato per noi tifosi della Juve! Qui posso discutere liberamente senza essere sommersa dai commenti degli haters."</p>
                <div class="text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo ASSETS_URL; ?>/images/testimoni/user3.jpg" alt="Giovanni" class="testimonial-img">
                    <div class="ms-3">
                        <h5 class="mb-0">Giovanni Rossi</h5>
                        <p class="text-muted mb-0">Torino, 45 anni</p>
                    </div>
                </div>
                <p>"Sono juventino da sempre e BiancoNeriHub mi ha fatto riscoprire il piacere di condividere la mia passione. Ho incontrato tantissimi nuovi amici!"</p>
                <div class="text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
            
            <div class="testimonial-card">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo ASSETS_URL; ?>/images/testimoni/user4.jpg" alt="Sofia" class="testimonial-img">
                    <div class="ms-3">
                        <h5 class="mb-0">Sofia Bianconeri</h5>
                        <p class="text-muted mb-0">Palermo, 23 anni</p>
                    </div>
                </div>
                <p>"Ho trovato amici veri su questa piattaforma! Ci siamo persino organizzati per vedere insieme la finale di Champions a Berlino. Un'esperienza indimenticabile!"</p>
                <div class="text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sezione FAQ -->
<div class="container mb-5">
    <div class="row text-center mb-5 animate-on-scroll">
        <div class="col-12">
            <h2 class="display-4 fw-bold mb-3">Domande frequenti</h2>
            <p class="lead text-muted">Risposte a tutto ciò che vuoi sapere su BiancoNeriHub</p>
            <div class="d-flex justify-content-center mt-4">
                <div style="width: 100px; height: 4px; background: var(--juventus-gold);"></div>
            </div>
        </div>
    </div>
    
    <div class="row animate-on-scroll">
        <div class="col-lg-6 mb-4">
            <div class="faq-card">
                <div class="faq-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Chi può registrarsi a BiancoNeriHub?</h5>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    <p>BiancoNeriHub è aperto a tutti i tifosi della Juventus, indipendentemente da dove vivano. L'unico requisito è la passione per i colori bianconeri! La registrazione è semplice, veloce e completamente gratuita.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="faq-card">
                <div class="faq-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>BiancoNeriHub è gratuito?</h5>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    <p>Assolutamente sì! La registrazione e l'utilizzo di BiancoNeriHub sono completamente gratuiti. Offriamo tutti i nostri servizi senza costi nascosti. Il nostro obiettivo è creare la più grande community di tifosi juventini al mondo.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="faq-card">
                <div class="faq-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>BiancoNeriHub è affiliato alla Juventus FC?</h5>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    <p>No, BiancoNeriHub è un progetto indipendente creato da tifosi per tifosi. Non siamo ufficialmente affiliati alla Juventus Football Club S.p.A., ma condividiamo la stessa passione per i colori bianconeri.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="faq-card">
                <div class="faq-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Come posso contribuire alla community?</h5>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    <p>Puoi contribuire in molti modi: condividendo contenuti originali, partecipando alle discussioni, commentando i post di altri utenti, organizzando e partecipando agli eventi della community. Ogni tuo contributo arricchisce la nostra piattaforma!</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="faq-card">
                <div class="faq-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Posso condividere i contenuti da altri social?</h5>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    <p>Certamente! Puoi condividere contenuti da altre piattaforme purché tu abbia i diritti per farlo. Ricorda sempre di citare la fonte originale e rispettare il copyright e i termini di servizio di BiancoNeriHub.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="faq-card">
                <div class="faq-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Esistono app per dispositivi mobili?</h5>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-content">
                    <p>Sì! Abbiamo app disponibili per iOS e Android che ti permettono di accedere a BiancoNeriHub ovunque tu sia. Scaricale dagli app store ufficiali per un'esperienza ottimizzata sui dispositivi mobili.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sezione app mobile -->
<div class="bg-light py-5 mb-5 animate-on-scroll">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-7">
                <h2 class="display-4 fw-bold mb-4">Porta BiancoNeriHub sempre con te</h2>
                <p class="lead mb-4">Scarica la nostra app per iOS e Android e non perderti mai un aggiornamento sulla tua squadra del cuore. Notifiche in tempo reale, chat con altri tifosi e tutte le funzionalità del sito ottimizzate per il tuo smartphone.</p>
                <div class="d-flex flex-wrap mt-4">
                    <a href="#" class="me-3 mb-3">
                        <img src="<?php echo ASSETS_URL; ?>/images/app-store-badge.png" alt="Download on App Store" style="height: 60px;">
                    </a>
                    <a href="#" class="mb-3">
                        <img src="<?php echo ASSETS_URL; ?>/images/google-play-badge.png" alt="Get it on Google Play" style="height: 60px;">
                    </a>
                </div>
                <div class="mt-4">
                    <p class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span>Chat private con altri tifosi</span>
                    </p>
                    <p class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span>Notifiche per gli eventi della Juventus</span>
                    </p>
                    <p class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span>Timeline personalizzata con i contenuti che ami</span>
                    </p>
                </div>
            </div>
            <div class="col-lg-6 col-md-5 text-center mt-5 mt-md-0">
                <img src="<?php echo ASSETS_URL; ?>/images/app-mockup.png" alt="BiancoNeriHub App" class="img-fluid app-mockup">
            </div>
        </div>
    </div>
</div>

<!-- CTA finale -->
<div class="container mb-5">
    <div class="cta-section animate-on-scroll">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <img src="<?php echo ASSETS_URL; ?>/images/champions-trophy.png" alt="Champions Trophy" class="champions-trophy me-4" style="height: 120px;">
                        <div>
                            <h2 class="display-4 fw-bold text-white mb-3">Unisciti alla famiglia bianconera</h2>
                            <p class="lead text-white mb-0">Una passione che unisce. Una community che cresce. Un'esperienza unica per ogni tifoso della Juventus.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-center text-lg-end">
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-light btn-lg px-4 py-3" style="animation: pulse 2s infinite;">
                        <i class="fas fa-user-plus me-2"></i> Registrati Ora
                    </a>
                    <p class="text-white mt-3">Ci vogliono solo 30 secondi!</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Newsletter -->
<div class="container mb-5">
    <div class="newsletter-form animate-on-scroll">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <h3 class="mb-3"><i class="fas fa-envelope text-primary me-2"></i>Resta aggiornato</h3>
                <p class="mb-0">Iscriviti alla nostra newsletter per ricevere aggiornamenti sulla Juventus, sulla community e per non perderti nessun contenuto esclusivo.</p>
            </div>
            <div class="col-lg-6">
                <form action="<?php echo SITE_URL; ?>/actions/subscribe_newsletter.php" method="POST" class="row g-2">
                    <div class="col-sm-8">
                        <input type="email" class="form-control form-control-lg" placeholder="La tua email" required>
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100">Iscriviti</button>
                    </div>
                    <div class="col-12 mt-2">
                        <small class="text-muted">Promettiamo di non inviarti spam. Puoi disiscriverti in qualsiasi momento.</small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript per la landing page interattiva -->
<script>
$(document).ready(function() {
    // Animazione per gli elementi quando diventano visibili
    function animateOnScroll() {
        $('.animate-on-scroll').each(function() {
            var position = $(this).offset().top;
            var windowHeight = $(window).height();
            var scrollPosition = $(window).scrollTop();
            
            if (position < scrollPosition + windowHeight - 100) {
                $(this).addClass('fade-in');
            }
        });
    }
    
    // Eseguiamo subito e poi al scroll
    animateOnScroll();
    $(window).scroll(animateOnScroll);
    
    // Animazione counter per le statistiche
    function animateCounter() {
        $('.stat-number').each(function() {
            var $this = $(this);
            var countTo = parseInt($this.attr('data-count'));
            
            if ($this.hasClass('counted')) return;
            
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum).toLocaleString());
                },
                complete: function() {
                    $this.text(countTo.toLocaleString());
                    $this.addClass('counted');
                }
            });
        });
    }
    
    // Controlliamo se le statistiche sono visibili
    var statsSection = $('.stats-section');
    $(window).scroll(function() {
        if (statsSection.offset().top < $(window).scrollTop() + $(window).height() - 100) {
            animateCounter();
        }
    });
    
    // Tour virtuale dello stadio
    $('.stadium-btn').click(function() {
        var area = $(this).data('area');
        $('.stadium-tour').removeClass('active');
        $('.stadium-tour[data-area="' + area + '"]').addClass('active');
        $('.stadium-btn').removeClass('btn-dark').addClass('btn-light');
        $(this).removeClass('btn-light').addClass('btn-dark');
    });
    
    // Slideshow automatico dello stadio ogni 5 secondi
    var stadiumAreas = ['overview', 'curva-sud', 'spogliatoi', 'campo', 'museo'];
    var currentArea = 0;
    
    function changeStadiumArea() {
        currentArea = (currentArea + 1) % stadiumAreas.length;
        $('.stadium-btn[data-area="' + stadiumAreas[currentArea] + '"]').click();
    }
    
    var stadiumInterval = setInterval(changeStadiumArea, 5000);
    
    // Ferma il cambio automatico quando l'utente interagisce
    $('.stadium-btn').click(function() {
        clearInterval(stadiumInterval);
        // Riprendi dopo 10 secondi di inattività
        stadiumInterval = setInterval(changeStadiumArea, 5000);
    });
    
    // FAQ toggle
    $('.faq-header').click(function() {
        var content = $(this).next('.faq-content');
        
        if (content.hasClass('open')) {
            content.removeClass('open');
            $(this).find('i.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $('.faq-content').removeClass('open');
            $('.faq-header i.fas').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            
            content.addClass('open');
            $(this).find('i.fas').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });
    
    // Inizializzazione carosello delle testimonianze
    if (typeof $.fn.owlCarousel !== 'undefined') {
        $('.owl-carousel').owlCarousel({
            loop: true,
            margin: 20,
            nav: true,
            dots: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            navText: ['<i class="fas fa-chevron-left"></i>', '<i class="fas fa-chevron-right"></i>'],
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 2
                },
                992: {
                    items: 3
                }
            }
        });
    } else {
        // Fallback se OwlCarousel non è caricato
        console.log('OwlCarousel not loaded, using fallback layout');
    }
});

// Aggiungiamo un effetto parallasse allo sfondo
document.addEventListener('mousemove', (e) => {
    const moveX = (e.clientX * -0.01);
    const moveY = (e.clientY * -0.01);
    document.querySelector('.hero-section').style.backgroundPosition = `calc(50% + ${moveX}px) calc(50% + ${moveY}px)`;
});
</script>

<!-- Inclusione biblioteca Owl Carousel -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
