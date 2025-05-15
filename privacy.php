<?php
/**
 * Pagina Privacy Policy
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Privacy Policy";

// Includiamo l'header
require_once 'includes/header.php';
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h1 class="text-center mb-5">Privacy Policy</h1>
                    
                    <div class="mb-5">
                        <h3>1. Introduzione</h3>
                        <p>La presente Privacy Policy descrive come BiancoNeriHub raccoglie, utilizza e protegge le informazioni personali degli utenti. Utilizziamo i tuoi dati personali per fornire e migliorare il nostro servizio.</p>
                        <p>Utilizzando BiancoNeriHub, accetti la raccolta e l'utilizzo delle informazioni in conformità con questa policy.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3>2. Dati raccolti</h3>
                        <p>Raccogliamo diversi tipi di informazioni per vari scopi:</p>
                        <ul>
                            <li><strong>Dati personali</strong>: nome, email, nome utente</li>
                            <li><strong>Dati di profilo</strong>: foto profilo, biografia, informazioni opzionali</li>
                            <li><strong>Dati di utilizzo</strong>: interazioni, post, commenti</li>
                            <li><strong>Dati tecnici</strong>: indirizzo IP, browser, dispositivo</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>3. Utilizzo dei dati</h3>
                        <p>Utilizziamo i dati raccolti per:</p>
                        <ul>
                            <li>Fornire e mantenere il servizio</li>
                            <li>Notificare cambiamenti al servizio</li>
                            <li>Permettere la partecipazione a funzionalità interattive</li>
                            <li>Fornire supporto</li>
                            <li>Analizzare l'uso del servizio</li>
                            <li>Prevenire attività fraudolente</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>4. Conservazione dei dati</h3>
                        <p>Conserviamo i dati personali solo per il tempo necessario agli scopi indicati in questa Privacy Policy. Conserveremo e useremo i dati nella misura necessaria per rispettare i nostri obblighi legali, risolvere controversie e applicare i nostri accordi.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3>5. Sicurezza dei dati</h3>
                        <p>La sicurezza dei tuoi dati è importante per noi. Adottiamo pratiche standard del settore per proteggere le informazioni inviate, sia durante la trasmissione che dopo la ricezione.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3>6. Cookie</h3>
                        <p>Utilizziamo i cookie per:</p>
                        <ul>
                            <li>Mantenere la tua sessione attiva</li>
                            <li>Memorizzare le tue preferenze</li>
                            <li>Migliorare il servizio</li>
                        </ul>
                        <p>Puoi scegliere di disabilitare i cookie attraverso le impostazioni del tuo browser, ma questo potrebbe influire sul funzionamento del servizio.</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3>7. I tuoi diritti</h3>
                        <p>Hai il diritto di:</p>
                        <ul>
                            <li>Accedere ai tuoi dati personali</li>
                            <li>Correggere dati inaccurati</li>
                            <li>Richiedere la cancellazione dei tuoi dati</li>
                            <li>Opporti al trattamento dei tuoi dati</li>
                            <li>Richiedere la portabilità dei dati</li>
                        </ul>
                    </div>
                    
                    <div class="mb-5">
                        <h3>8. Modifiche alla Privacy Policy</h3>
                        <p>Possiamo aggiornare la nostra Privacy Policy di tanto in tanto. Ti notificheremo eventuali modifiche pubblicando la nuova Privacy Policy su questa pagina e aggiornando la data di "ultima modifica".</p>
                    </div>
                    
                    <div class="mb-5">
                        <h3>9. Contattaci</h3>
                        <p>Per domande sulla Privacy Policy, contattaci:</p>
                        <ul>
                            <li>Email: privacy@bianconerihub.com</li>
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
