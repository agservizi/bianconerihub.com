<?php
/**
 * Pagina di reset password
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Reset Password";

// Includiamo l'header
require_once 'includes/header.php';

// Se l'utente è già loggato, lo reindirizziamo alla home
if (isLoggedIn()) {
    redirect(SITE_URL);
}

// Otteniamo il token
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$message = '';
$messageType = '';
$tokenValid = false;

if (!empty($token)) {
    // Verifichiamo il token
    $stmt = $conn->prepare("
        SELECT pr.user_id, pr.created_at, u.email 
        FROM password_resets pr
        JOIN users u ON pr.user_id = u.id
        WHERE pr.token = ? AND pr.used = 0
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $reset = $result->fetch_assoc();
        
        // Verifichiamo che il token non sia scaduto (1 ora)
        $tokenTime = strtotime($reset['created_at']);
        $currentTime = time();
        
        if (($currentTime - $tokenTime) <= 3600) { // 1 ora in secondi
            $tokenValid = true;
        } else {
            $message = 'Il link per il reset della password è scaduto. Richiedi un nuovo link.';
            $messageType = 'error';
        }
    } else {
        $message = 'Token non valido o già utilizzato.';
        $messageType = 'error';
    }
}

// Se è stato inviato il form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenValid) {
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    
    // Validazione
    if (empty($password)) {
        $message = 'La password è obbligatoria.';
        $messageType = 'error';
    } elseif (strlen($password) < 8) {
        $message = 'La password deve essere di almeno 8 caratteri.';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Le password non corrispondono.';
        $messageType = 'error';
    } else {
        // Aggiorniamo la password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $reset['user_id']);
        
        if ($stmt->execute()) {
            // Marchiamo il token come usato
            $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            
            $message = 'Password aggiornata con successo! Ora puoi effettuare il login con la nuova password.';
            $messageType = 'success';
            
            // Inviamo un'email di conferma
            $to = $reset['email'];
            $subject = 'Password Aggiornata - BiancoNeriHub';
            $emailMessage = "Ciao,\n\n";
            $emailMessage .= "La tua password è stata aggiornata con successo.\n";
            $emailMessage .= "Se non sei stato tu a fare questa modifica, contatta immediatamente il supporto.\n\n";
            $emailMessage .= "Cordiali saluti,\nIl team di BiancoNeriHub";
            
            mail($to, $subject, $emailMessage);
        } else {
            $message = 'Si è verificato un errore durante l\'aggiornamento della password.';
            $messageType = 'error';
        }
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Reset Password</h2>
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> mb-4">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($tokenValid && $messageType !== 'success'): ?>
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" class="needs-validation" novalidate>
                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Nuova Password</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="8">
                                <div class="form-text">La password deve essere di almeno 8 caratteri.</div>
                            </div>
                            
                            <!-- Conferma Password -->
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Conferma Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Aggiorna Password</button>
                        </form>
                    <?php elseif ($messageType === 'success'): ?>
                        <div class="text-center">
                            <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary">Vai al Login</a>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <p>Se hai bisogno di resettare la tua password, <a href="<?php echo SITE_URL; ?>/forgot_password.php">clicca qui</a>.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Validazione del form
    $('.needs-validation').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
});
</script>

<?php
// Includiamo il footer
require_once 'includes/footer.php';
?>
