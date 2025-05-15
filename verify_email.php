<?php
/**
 * Pagina di verifica email
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Verifica Email";

// Includiamo l'header
require_once 'includes/header.php';

// Otteniamo il token
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$message = '';
$messageType = '';

if (!empty($token)) {
    // Cerchiamo il token nel database
    $stmt = $conn->prepare("SELECT user_id, created_at FROM email_verifications WHERE token = ? AND used = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $verification = $result->fetch_assoc();
        
        // Verifichiamo che il token non sia scaduto (24 ore)
        $tokenTime = strtotime($verification['created_at']);
        $currentTime = time();
        
        if (($currentTime - $tokenTime) <= 86400) { // 24 ore in secondi
            // Aggiorniamo lo stato dell'utente
            $stmt = $conn->prepare("UPDATE users SET email_verified = 1 WHERE id = ?");
            $stmt->bind_param("i", $verification['user_id']);
            
            if ($stmt->execute()) {
                // Marchiamo il token come usato
                $stmt = $conn->prepare("UPDATE email_verifications SET used = 1 WHERE token = ?");
                $stmt->bind_param("s", $token);
                $stmt->execute();
                
                $message = 'Email verificata con successo! Ora puoi accedere a tutte le funzionalità del sito.';
                $messageType = 'success';
            } else {
                $message = 'Si è verificato un errore durante la verifica dell\'email.';
                $messageType = 'error';
            }
        } else {
            $message = 'Il link di verifica è scaduto. Richiedi un nuovo link di verifica.';
            $messageType = 'error';
        }
    } else {
        $message = 'Token di verifica non valido o già utilizzato.';
        $messageType = 'error';
    }
} else {
    $message = 'Token di verifica mancante.';
    $messageType = 'error';
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body text-center">
                    <h2 class="card-title mb-4">Verifica Email</h2>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> mb-4">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($messageType === 'success'): ?>
                        <p>Puoi ora tornare alla <a href="<?php echo SITE_URL; ?>">homepage</a> o accedere al tuo <a href="<?php echo SITE_URL; ?>/profile.php">profilo</a>.</p>
                    <?php else: ?>
                        <p>Se hai bisogno di un nuovo link di verifica, <a href="<?php echo SITE_URL; ?>/resend_verification.php">clicca qui</a>.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Includiamo il footer
require_once 'includes/footer.php';
?>
