<?php
/**
 * Pagina Termini di Servizio
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Termini di Servizio";

// Includiamo l'header
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h1 class="text-center mb-5">Termini di Servizio</h1>
                    
                    <div class="mb-5">
                        <h3>1. Accettazione dei termini</h3>
                        <p>Utilizzando BiancoNeriHub, accetti i presenti Termini di Servizio. Se non accetti questi termini, ti preghiamo di non utilizzare il servizio.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3>2. Chi può utilizzare il servizio</h3>
                        <p>Per utilizzare BiancoNeriHub devi:</p>
                        <ul>
                            <li>Avere almeno 16 anni</li>
                            <li>Registrarti con informazioni veritiere</li>
                            <li>Non essere stato precedentemente bannato</li>
                            <li>Rispettare le leggi applicabili</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>3. Il tuo account</h3>
                        <p>Sei responsabile di:</p>
                        <ul>
                            <li>Mantenere la sicurezza delle tue credenziali</li>
                            <li>Tutte le attività che avvengono con il tuo account</li>
                            <li>Notificarci immediatamente di qualsiasi accesso non autorizzato</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>4. Contenuti</h3>
                        <p>Utilizzando BiancoNeriHub, accetti di non pubblicare contenuti che:</p>
                        <ul>
                            <li>Violano leggi o diritti di terzi</li>
                            <li>Sono offensivi, discriminatori o inappropriati</li>
                            <li>Contengono spam o pubblicità non autorizzata</li>
                            <li>Includono malware o codice dannoso</li>
                            <li>Violano il copyright o altri diritti di proprietà intellettuale</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>5. Regole della community</h3>
                        <p>Gli utenti devono:</p>
                        <ul>
                            <li>Essere rispettosi verso gli altri utenti</li>
                            <li>Non utilizzare linguaggio offensivo o discriminatorio</li>
                            <li>Non pubblicare contenuti falsi o fuorvianti</li>
                            <li>Non molestare o intimidire altri utenti</li>
                            <li>Rispettare le decisioni dei moderatori</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>6. Proprietà intellettuale</h3>
                        <p>BiancoNeriHub rispetta i diritti di proprietà intellettuale. Gli utenti mantengono i diritti sui contenuti che pubblicano, ma concedono a BiancoNeriHub una licenza non esclusiva per utilizzarli nell'ambito del servizio.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3>7. Limitazione di responsabilità</h3>
                        <p>BiancoNeriHub fornisce il servizio "così com'è" e non sarà responsabile per:</p>
                        <ul>
                            <li>Interruzioni del servizio</li>
                            <li>Perdita di dati</li>
                            <li>Danni diretti o indiretti</li>
                            <li>Contenuti pubblicati dagli utenti</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>8. Modifiche al servizio</h3>
                        <p>Ci riserviamo il diritto di:</p>
                        <ul>
                            <li>Modificare o terminare il servizio</li>
                            <li>Aggiornare questi termini</li>
                            <li>Rimuovere contenuti inappropriati</li>
                            <li>Sospendere o terminare account</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>9. Risoluzione delle controversie</h3>
                        <p>Qualsiasi controversia relativa a questi termini o all'utilizzo del servizio sarà risolta secondo le leggi italiane. Il foro competente sarà quello di Torino.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3>10. Contatti</h3>
                        <p>Per domande sui Termini di Servizio, contattaci:</p>
                        <ul>
                            <li>Email: legal@bianconerihub.com</li>
                            <li>Tramite il modulo di <a href="<?php echo SITE_URL; ?>/contact.php">contatto</a></li>
                        </ul>
                    </div>
                    
                    <div class="text-center mt-5">
                        <p><small>Ultima modifica: <?php echo date('d/m/Y'); ?></small></p>
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
