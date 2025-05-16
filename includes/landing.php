<?php
/**
 * Landing page per utenti non autenticati
 * BiancoNeriHub - Social network per tifosi della Juventus
 * Versione interattiva creata il 15 maggio 2025
 */
?>

<!-- CSS specifico per la landing page -->
<style>
/* Stili per la landing page interattiva */
.hero-section {
    position: relative;
    background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.8)), 
                url('<?php echo ASSETS_URL; ?>/images/juventus-stadium.jpg') no-repeat center center;
    background-size: cover;
    height: 100vh;
    min-height: 600px;
    color: var(--juventus-white);
    display: flex;
    align-items: center;
    overflow: hidden;
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
}

.hero-title {
    font-size: 5rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: -2px;
    margin-bottom: 1rem;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
    animation: fadeInDown 1.2s ease-out;
}

.hero-subtitle {
    font-size: 1.5rem;
    font-weight: 400;
    margin-bottom: 2rem;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    animation: fadeInUp 1.2s ease-out;
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

/* Responsive classes */
@media (max-width: 992px) {
    .hero-title {
        font-size: 3.5rem;
    }
    
    .trophy-slider {
        height: 100px;
    }
    
    .trophy-item img {
        height: 60px;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 3rem;
    }
    
    .hero-subtitle {
        font-size: 1.2rem;
    }
    
    .stats-section {
        padding: 40px 0;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .trophy-slider {
        display: none;
    }
}
</style>

<!-- Hero section con video/immagine di sfondo -->
<div class="hero-section">
    <div class="hero-content container">
        <div class="row justify-content-center">
            <div class="col-lg-10 text-center">
                <h1 class="hero-title">
                    <span class="d-block">Bianco</span>
                    <span class="d-block" style="color: var(--juventus-gold);">Neri</span>
                    <span class="d-block">Hub</span>
                </h1>
                <p class="hero-subtitle">Il social network definitivo per i veri tifosi della Juventus</p>
                <div class="mt-5 d-flex justify-content-center" data-aos="fade-up" data-aos-delay="300">
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary btn-lg me-3 px-4 py-3" style="animation: pulse 2s infinite;">
                        <i class="fas fa-user-plus me-2"></i> Unisciti a noi
                    </a>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-light btn-lg px-4 py-3">
                        <i class="fas fa-sign-in-alt me-2"></i> Accedi
                    </a>
                </div>
                <div class="social-proof mt-5">
                    <div class="avatar-group">
                        <div class="avatar"><img src="<?php echo ASSETS_URL; ?>/images/users/user1.jpg" alt="User"></div>
                        <div class="avatar"><img src="<?php echo ASSETS_URL; ?>/images/users/user2.jpg" alt="User"></div>
                        <div class="avatar"><img src="<?php echo ASSETS_URL; ?>/images/users/user3.jpg" alt="User"></div>
                        <div class="avatar"><img src="<?php echo ASSETS_URL; ?>/images/users/user4.jpg" alt="User"></div>
                        <div class="avatar"><img src="<?php echo ASSETS_URL; ?>/images/users/user5.jpg" alt="User"></div>
                    </div>
                    <span class="ms-3 text-white">+10.000 tifosi si sono già uniti</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Trophy slider -->
    <div class="trophy-slider">
        <div class="slider-container">
            <!-- Trofei duplicati per infinite loop -->
            <?php
            $trophies = [
                ['name' => 'Serie A', 'count' => '36', 'img' => 'scudetto.png'],
                ['name' => 'Coppa Italia', 'count' => '14', 'img' => 'coppa-italia.png'],
                ['name' => 'Supercoppa', 'count' => '9', 'img' => 'supercoppa.png'],
                ['name' => 'Champions League', 'count' => '2', 'img' => 'champions.png'],
                ['name' => 'Coppa UEFA', 'count' => '3', 'img' => 'europa-league.png'],
                ['name' => 'Intercontinentale', 'count' => '2', 'img' => 'intercontinentale.png'],
                ['name' => 'Supercoppa UEFA', 'count' => '2', 'img' => 'supercoppa-uefa.png'],
                ['name' => 'Coppa delle Coppe', 'count' => '1', 'img' => 'coppadellecoppe.png']
            ];
            
            // Duplicare per effetto infinito
            $allTrophies = array_merge($trophies, $trophies);
            
            foreach ($allTrophies as $trophy) {
                echo '<div class="trophy-item">
                        <img src="' . ASSETS_URL . '/images/trophies/' . $trophy['img'] . '" alt="' . $trophy['name'] . '">
                        <span>' . $trophy['count'] . ' x ' . $trophy['name'] . '</span>
                      </div>';
            }
            ?>
        </div>
    </div>
</div>

<!-- Sezione caratteristiche -->
<div class="container py-5">
    <div class="row text-center mb-5 animate-on-scroll">
        <div class="col-12">
            <h2 class="display-4 fw-bold mb-3">La tua passione, la nostra community</h2>
            <p class="lead text-muted">Scopri tutto ciò che BiancoNeriHub ha da offrirti</p>
            <div class="d-flex justify-content-center mt-4">
                <div style="width: 100px; height: 4px; background: var(--juventus-gold);"></div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4 animate-on-scroll">
            <div class="feature-card h-100">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4 class="mt-4 mb-3">Community Esclusiva</h4>
                <p class="text-muted">Connettiti con migliaia di tifosi bianconeri che condividono la tua stessa passione. Discuti, condividi e celebra ogni momento insieme.</p>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-sm btn-outline-dark mt-3">Unisciti ora</a>
            </div>
        </div>
        <div class="col-md-4 mb-4 animate-on-scroll" data-delay="200">
            <div class="feature-card h-100">
                <div class="feature-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h4 class="mt-4 mb-3">Dibattiti Appassionati</h4>
                <p class="text-muted">Partecipa a discussioni su tattica, calciomercato, partite e tutto ciò che riguarda la Juventus. La tua opinione conta.</p>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-sm btn-outline-dark mt-3">Inizia a discutere</a>
            </div>
        </div>
        <div class="col-md-4 mb-4 animate-on-scroll" data-delay="400">
            <div class="feature-card h-100">
                <div class="feature-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h4 class="mt-4 mb-3">News in Tempo Reale</h4>
                <p class="text-muted">Rimani aggiornato con le ultime notizie sulla Juventus. Articoli, interviste e contenuti esclusivi direttamente dalla community.</p>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-sm btn-outline-dark mt-3">Scopri le news</a>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-4 mb-4 animate-on-scroll">
            <div class="feature-card h-100">
                <div class="feature-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4 class="mt-4 mb-3">Eventi dal Vivo</h4>
                <p class="text-muted">Partecipa a raduni per vedere le partite insieme, incontri con altri tifosi ed eventi speciali organizzati dalla nostra community.</p>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-sm btn-outline-dark mt-3">Scopri gli eventi</a>
            </div>
        </div>
        <div class="col-md-4 mb-4 animate-on-scroll" data-delay="200">
            <div class="feature-card h-100">
                <div class="feature-icon">
                    <i class="fas fa-images"></i>
                </div>
                <h4 class="mt-4 mb-3">Gallerie Multimediali</h4>
                <p class="text-muted">Condividi foto e video delle tue esperienze allo stadio, dei tuoi incontri con i giocatori e di ogni momento importante per te.</p>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-sm btn-outline-dark mt-3">Esplora la galleria</a>
            </div>
        </div>
        <div class="col-md-4 mb-4 animate-on-scroll" data-delay="400">
            <div class="feature-card h-100">
                <div class="feature-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h4 class="mt-4 mb-3">Storia Bianconera</h4>
                <p class="text-muted">Rivivi i momenti più gloriosi della Juventus attraverso timeline interattive, archivi storici e ricordi condivisi dai tifosi.</p>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-sm btn-outline-dark mt-3">Esplora la storia</a>
            </div>
        </div>
    </div>
</div>

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
</script>

<!-- Inclusione biblioteca Owl Carousel -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
