<?php
/**
 * Pagina di recupero password
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Define environment
define('ENVIRONMENT', 'development');

// Titolo della pagina
$pageTitle = "Password Dimenticata";

// Includiamo l'header
require_once 'includes/header.php';

// Se l'utente è già autenticato, lo reindirizziamo alla home
if (isLoggedIn()) {
    redirect(SITE_URL);
}

// Variabili per i messaggi
$error = '';
$success = '';
$email = '';

// Verifichiamo se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    
    // Validazione email
    if (empty($email)) {
        $error = "Inserisci l'indirizzo email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Indirizzo email non valido";
    } else {
        // Verifichiamo se l'email esiste nel database
        $stmt = $conn->prepare("SELECT id, username, full_name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error = "Nessun account trovato con questo indirizzo email";
        } else {
            $user = $result->fetch_assoc();
            
            // Generiamo un token univoco
            $token = bin2hex(random_bytes(32));
            
            // Inseriamo il token nel database
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, email, token, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iss", $user['id'], $email, $token);
            
            if ($stmt->execute()) {
                // Componiamo il link di reset
                $resetLink = SITE_URL . '/reset_password.php?token=' . $token . '&email=' . urlencode($email);
                
                // Invio email (simulato per ora)
                // In produzione, utilizzare una libreria come PHPMailer o SwiftMailer
                $to = $email;
                $subject = "Reimposta la tua password su BiancoNeriHub";
                $message = "
                    <html>
                    <head>
                        <title>Reimposta Password</title>
                    </head>
                    <body>
                        <div style='max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;'>
                            <div style='text-align: center; margin-bottom: 20px;'>
                                <img src='" . SITE_URL . "/assets/images/logo.png' alt='BiancoNeriHub Logo' style='max-width: 150px;'>
                            </div>
                            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px;'>
                                <h2 style='color: #000; margin-top: 0;'>Reimposta la tua password</h2>
                                <p>Ciao " . htmlspecialchars($user['full_name']) . ",</p>
                                <p>Hai richiesto di reimpostare la password per il tuo account BiancoNeriHub.</p>
                                <p>Clicca sul link seguente per reimpostare la tua password:</p>
                                <p style='text-align: center;'>
                                    <a href='" . $resetLink . "' style='display: inline-block; background-color: #000; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reimposta Password</a>
                                </p>
                                <p>Oppure copia e incolla il seguente link nel tuo browser:</p>
                                <p style='background-color: #eee; padding: 10px; border-radius: 3px; word-break: break-all;'>" . $resetLink . "</p>
                                <p>Questo link scadrà tra 24 ore.</p>
                                <p>Se non hai richiesto questa operazione, ignora questa email.</p>
                            </div>
                            <div style='margin-top: 20px; text-align: center; color: #6c757d; font-size: 12px;'>
                                <p>&copy; " . date('Y') . " BiancoNeriHub. Tutti i diritti riservati.</p>
                                <p>Questo messaggio è stato inviato a " . htmlspecialchars($email) . "</p>
                            </div>
                        </div>
                    </body>
                // Intestazioni per l'email HTML
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8\r\n";
                $headers .= "From: BiancoNeriHub <noreply@bianconerihub.com>\r\n";
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: BiancoNeriHub <noreply@bianconerihub.com>" . "\r\n";
                
                // Invio dell'email
                // mail($to, $subject, $message, $headers);
                
                // Per ora simuliamo l'invio
                $success = "Abbiamo inviato un link per reimpostare la password al tuo indirizzo email. Se non lo trovi, controlla anche nella cartella spam.";
                    $success .= "<br><small>Link di reset (solo per sviluppo): <a href='$resetLink'>$resetLink</a></small>";
                // In un ambiente di sviluppo, mostriamo il link
                if (ENVIRONMENT === 'development') {
                    $success .= "<br><small>Link di reset (solo per sviluppo): <a href='" . $resetLink . "'>" . $resetLink . "</a></small>";
                }
                
                $email = ''; // Puliamo il campo email
            } else {
                $error = "Si è verificato un errore. Riprova più tardi.";
            }
        }
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h2><i class="fas fa-key"></i> Password Dimenticata</h2>
                    <p class="mb-0">Reimposta la tua password</p>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (empty($success)): ?>
                        <p class="mb-4">Inserisci l'indirizzo email associato al tuo account. Ti invieremo un link per reimpostare la tua password.</p>
                        
                        <form method="POST" action="">
                            <div class="mb-4">
                                <label for="email" class="form-label">Indirizzo Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Inserisci il tuo indirizzo email" required>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-paper-plane"></i> Invia Link di Reset
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <div class="text-center mt-4">
                        <a href="<?php echo SITE_URL; ?>/login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left"></i> Torna al login
                        </a>
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
