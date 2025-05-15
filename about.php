<?php
/**
 * Pagina Chi Siamo
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Chi Siamo";

// Includiamo l'header
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h1 class="text-center mb-5">Chi Siamo</h1>
                    
                    <div class="row mb-5">
                        <div class="col-md-6 text-center">
                            <img src="<?php echo SITE_URL; ?>/assets/images/logo.png" 
                                 alt="Logo BiancoNeriHub" 
                                 class="img-fluid mb-4" 
                                 style="max-width: 200px;">
                        </div>
                        <div class="col-md-6">
                            <h3 class="mb-4">La nostra storia</h3>
                            <p>BiancoNeriHub nasce nel 2025 dalla passione di un gruppo di tifosi juventini che volevano creare uno spazio digitale dedicato esclusivamente ai sostenitori della Vecchia Signora.</p>
                            <p>La nostra missione è quella di unire i tifosi bianconeri di tutto il mondo, creando una community attiva e appassionata dove condividere emozioni, ricordi e discussioni sulla nostra amata Juventus.</p>
                        </div>
                    </div>
                    
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <h3 class="mb-4 text-center">Cosa offriamo</h3>
                            <div class="row">
                                <div class="col-md-4 text-center mb-4">
                                    <i class="fas fa-users fa-3x mb-3 text-primary"></i>
                                    <h4>Community</h4>
                                    <p>Un luogo dove incontrare altri tifosi, scambiare opinioni e condividere la passione per i colori bianconeri.</p>
                                </div>
                                <div class="col-md-4 text-center mb-4">
                                    <i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
                                    <h4>Eventi</h4>
                                    <p>Organizzazione di incontri, visione collettiva delle partite e altri eventi dedicati ai tifosi.</p>
                                </div>
                                <div class="col-md-4 text-center mb-4">
                                    <i class="fas fa-comments fa-3x mb-3 text-primary"></i>
                                    <h4>Discussioni</h4>
                                    <p>Forum e chat per discutere di tattiche, mercato, storia e tutto ciò che riguarda la Juventus.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-5">
                        <div class="col-md-12">
                            <h3 class="mb-4 text-center">I nostri valori</h3>
                            <div class="row">
                                <div class="col-md-3 text-center mb-4">
                                    <i class="fas fa-heart fa-2x mb-3 text-danger"></i>
                                    <h5>Passione</h5>
                                    <p>L'amore per la Juventus è ciò che ci unisce e ci motiva.</p>
                                </div>
                                <div class="col-md-3 text-center mb-4">
                                    <i class="fas fa-handshake fa-2x mb-3 text-success"></i>
                                    <h5>Rispetto</h5>
                                    <p>Promuoviamo il rispetto reciproco tra tutti i membri della community.</p>
                                </div>
                                <div class="col-md-3 text-center mb-4">
                                    <i class="fas fa-shield-alt fa-2x mb-3 text-warning"></i>
                                    <h5>Lealtà</h5>
                                    <p>Siamo leali ai nostri colori e ai nostri principi.</p>
                                </div>
                                <div class="col-md-3 text-center mb-4">
                                    <i class="fas fa-users fa-2x mb-3 text-info"></i>
                                    <h5>Inclusività</h5>
                                    <p>Accogliamo tutti i tifosi juventini, da ogni parte del mondo.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mb-4">
                        <h3 class="mb-4">Unisciti a noi</h3>
                        <p>Entra a far parte della più grande community di tifosi juventini!</p>
                        <?php if (!isLoggedIn()): ?>
                            <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-primary btn-lg">Registrati ora</a>
                        <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-primary">Scopri gli eventi</a>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row mt-5">
                        <div class="col-md-12">
                            <div class="alert alert-secondary text-center">
                                <small>BiancoNeriHub non è affiliato alla Juventus Football Club S.p.A. Tutti i marchi citati appartengono ai rispettivi proprietari.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Includiamo il footer
require_once 'includes/footer.php';
?>
