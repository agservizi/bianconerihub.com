<?php
/**
 * Landing page per utenti non autenticati
 * BiancoNeriHub - Social network per tifosi della Juventus
 */
?>

<!-- Hero section con video/immagine di sfondo -->
<div class="hero-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 text-center">
                <h1 class="hero-title">BiancoNeriHub</h1>
                <p class="hero-subtitle">Il social network dedicato ai veri tifosi della Juventus</p>
                <div class="mt-4">
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-user-plus"></i> Registrati ora
                    </a>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Accedi
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sezione caratteristiche -->
<div class="container">
    <div class="row text-center mb-5">
        <div class="col-12">
            <h2 class="section-title">Cosa offre BiancoNeriHub?</h2>
            <p class="lead">Unisciti alla più grande community di tifosi juventini online</p>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="feature-card shadow h-100">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h4>Community</h4>
                <p>Connettiti con migliaia di appassionati bianconeri da tutto il mondo. Condividi la tua passione per la Juventus con persone che la amano quanto te.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card shadow h-100">
                <div class="feature-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h4>Discussioni</h4>
                <p>Partecipa a discussioni su partite, giocatori, calciomercato e strategie. La tua opinione conta nella nostra community di veri tifosi.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card shadow h-100">
                <div class="feature-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h4>News</h4>
                <p>Rimani sempre aggiornato con le ultime notizie sulla Juventus. Articoli, interviste esclusive e contenuti creati dagli utenti per i veri tifosi.</p>
            </div>
        </div>
    </div>
    
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="feature-card shadow h-100">
                <div class="feature-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4>Eventi</h4>
                <p>Organizza o partecipa a eventi dedicati ai tifosi. Raduni per vedere le partite insieme, eventi speciali e molto altro ancora.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card shadow h-100">
                <div class="feature-icon">
                    <i class="fas fa-images"></i>
                </div>
                <h4>Gallerie</h4>
                <p>Condividi foto e video delle tue esperienze allo stadio, dei tuoi cimeli più preziosi o dei momenti più belli della storia bianconera.</p>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="feature-card shadow h-100">
                <div class="feature-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h4>Storia</h4>
                <p>Rivivi i momenti più gloriosi della storia della Juventus attraverso contenuti esclusivi, timeline interattive e testimonianze dei tifosi di lunga data.</p>
            </div>
        </div>
    </div>
</div>

<!-- Sezione statistiche -->
<div class="bg-primary text-white py-5 mb-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3 mb-4 mb-md-0">
                <h2 class="display-4 fw-bold">10K+</h2>
                <p class="lead">Utenti registrati</p>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <h2 class="display-4 fw-bold">50K+</h2>
                <p class="lead">Post condivisi</p>
            </div>
            <div class="col-md-3 mb-4 mb-md-0">
                <h2 class="display-4 fw-bold">5K+</h2>
                <p class="lead">Foto e video</p>
            </div>
            <div class="col-md-3">
                <h2 class="display-4 fw-bold">1K+</h2>
                <p class="lead">Eventi organizzati</p>
            </div>
        </div>
    </div>
</div>

<!-- Sezione testimonianze -->
<div class="container mb-5">
    <div class="row text-center mb-5">
        <div class="col-12">
            <h2 class="section-title">Cosa dicono i tifosi</h2>
            <p class="lead">Scopri le opinioni di chi è già parte della nostra community</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="testimonial-card shadow">
                <img src="<?php echo ASSETS_URL; ?>/images/testimoni/user1.jpg" alt="Marco" class="testimonial-img">
                <h5>Marco, 32 anni</h5>
                <p class="text-muted">Milano</p>
                <p>"BiancoNeriHub mi ha permesso di connettermi con altri tifosi anche quando sono lontano da Torino. L'atmosfera che si respira qui è quella dello Stadium!"</p>
                <div class="text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="testimonial-card shadow">
                <img src="<?php echo ASSETS_URL; ?>/images/testimoni/user2.jpg" alt="Laura" class="testimonial-img">
                <h5>Laura, 27 anni</h5>
                <p class="text-muted">Roma</p>
                <p>"Finalmente un social network pensato per noi tifosi della Juve! Qui posso discutere liberamente senza essere sommersa dai commenti degli haters."</p>
                <div class="text-warning">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="testimonial-card shadow">
                <img src="<?php echo ASSETS_URL; ?>/images/testimoni/user3.jpg" alt="Giovanni" class="testimonial-img">
                <h5>Giovanni, 45 anni</h5>
                <p class="text-muted">Torino</p>
                <p>"Sono juventino da sempre e BiancoNeriHub mi ha fatto riscoprire il piacere di condividere la mia passione. Ho incontrato tantissimi nuovi amici!"</p>
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

<!-- Sezione call-to-action -->
<div class="bg-secondary py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 text-center text-md-start">
                <h2 class="text-black">Pronto a unirti alla community bianconera?</h2>
                <p class="text-black mb-md-0">Registrati oggi stesso e inizia a condividere la tua passione per la Juventus!</p>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-dark btn-lg">
                    <i class="fas fa-user-plus"></i> Registrati ora
                </a>
            </div>
        </div>
    </div>
</div>

<!-- FAQ -->
<div class="container mb-5">
    <div class="row text-center mb-5">
        <div class="col-12">
            <h2 class="section-title">Domande frequenti</h2>
            <p class="lead">Tutto quello che devi sapere su BiancoNeriHub</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h5><i class="fas fa-question-circle text-primary me-2"></i>Chi può registrarsi a BiancoNeriHub?</h5>
                    <p class="mb-0">BiancoNeriHub è aperto a tutti i tifosi della Juventus, indipendentemente da dove vivano. L'unico requisito è la passione per i colori bianconeri!</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h5><i class="fas fa-question-circle text-primary me-2"></i>BiancoNeriHub è gratuito?</h5>
                    <p class="mb-0">Sì, la registrazione e l'utilizzo di BiancoNeriHub sono completamente gratuiti. Offriamo tutti i nostri servizi senza costi nascosti.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h5><i class="fas fa-question-circle text-primary me-2"></i>BiancoNeriHub è affiliato alla Juventus FC?</h5>
                    <p class="mb-0">No, BiancoNeriHub è un progetto indipendente creato da tifosi per tifosi. Non siamo ufficialmente affiliati alla Juventus Football Club S.p.A.</p>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h5><i class="fas fa-question-circle text-primary me-2"></i>Come posso contribuire alla community?</h5>
                    <p class="mb-0">Puoi contribuire condividendo contenuti originali, partecipando alle discussioni, commentando i post di altri utenti e partecipando agli eventi organizzati dalla community.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- App download section -->
<div class="bg-light py-5 mb-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <h2>Scarica la nostra app</h2>
                <p class="lead">Porta BiancoNeriHub sempre con te! Disponibile per iOS e Android.</p>
                <div class="mt-4">
                    <a href="#" class="me-3">
                        <img src="<?php echo ASSETS_URL; ?>/images/app-store-badge.png" alt="Download on App Store" height="50">
                    </a>
                    <a href="#">
                        <img src="<?php echo ASSETS_URL; ?>/images/google-play-badge.png" alt="Get it on Google Play" height="50">
                    </a>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="<?php echo ASSETS_URL; ?>/images/app-mockup.png" alt="BiancoNeriHub App" class="img-fluid mt-4 mt-md-0">
            </div>
        </div>
    </div>
</div>

<!-- Newsletter -->
<div class="container mb-5">
    <div class="card shadow">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h3><i class="fas fa-envelope-open-text text-primary me-2"></i>Iscriviti alla nostra newsletter</h3>
                    <p class="mb-0">Ricevi aggiornamenti sulle novità di BiancoNeriHub e sul mondo Juventus direttamente nella tua casella email.</p>
                </div>
                <div class="col-md-6">
                    <form action="<?php echo SITE_URL; ?>/actions/subscribe_newsletter.php" method="POST" class="row g-2">
                        <div class="col-sm-8">
                            <input type="email" class="form-control" placeholder="La tua email" required>
                        </div>
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-primary w-100">Iscriviti</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
