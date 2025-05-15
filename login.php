<?php
/**
 * Pagina di login
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Accedi";

// Includiamo l'header
require_once 'includes/header.php';

// Se l'utente è già autenticato, lo reindirizziamo alla home
if (isLoggedIn()) {
    redirect(SITE_URL);
}

// Variabili per i dati del form
$username = '';
$errMsg = '';

// Verifichiamo se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Otteniamo i dati inviati
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $rememberMe = isset($_POST['remember_me']);
    
    // Validazione dei dati
    if (empty($username) || empty($password)) {
        $errMsg = "Inserisci nome utente e password";
    } else {
        // Query per verificare le credenziali
        $query = "SELECT id, username, password, full_name, is_admin, account_status, is_verified FROM users WHERE username = '{$username}' OR email = '{$username}'";
        $result = $conn->query($query);
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verifichiamo se l'account è attivo
            if ($user['account_status'] !== 'active') {
                $errMsg = "Il tuo account è stato sospeso. Contatta l'amministratore.";
            } else {
                // Verifichiamo la password
                if (password_verify($password, $user['password'])) {
                    // Autenticazione riuscita
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    
                    // Impostazione cookie "Ricordami"
                    if ($rememberMe) {
                        $token = bin2hex(random_bytes(32));
                        $expires = time() + 30 * 24 * 60 * 60; // 30 giorni
                        
                        // Salviamo il token nel database
                        $tokenHash = password_hash($token, PASSWORD_DEFAULT);
                        $query = "INSERT INTO remember_tokens (user_id, token, expires) VALUES ({$user['id']}, '{$tokenHash}', FROM_UNIXTIME({$expires}))";
                        $conn->query($query);
                        
                        // Impostiamo il cookie
                        setcookie('remember_token', $user['id'] . ':' . $token, $expires, '/', '', false, true);
                    }
                    
                    // Aggiorniamo la data dell'ultimo accesso
                    $conn->query("UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");
                    
                    // Se l'account non è verificato, mostriamo un avviso
                    if (!$user['is_verified']) {
                        setFlashMessage("Il tuo account non è ancora verificato. Controlla la tua email per completare la verifica.", "warning");
                    } else {
                        setFlashMessage("Benvenuto, {$user['full_name']}!", "success");
                    }
                    
                    // Redirect alla home page
                    redirect(SITE_URL);
                } else {
                    $errMsg = "Password non corretta";
                }
            }
        } else {
            $errMsg = "Utente non trovato";
        }
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h2><i class="fas fa-sign-in-alt"></i> Accedi a BiancoNeriHub</h2>
                <p class="mb-0">Bentornato nella community bianconera</p>
            </div>
            <div class="card-body p-4">
                <?php if ($errMsg): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $errMsg; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome utente o Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Inserisci nome utente o email" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Inserisci la tua password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember_me">
                        <label class="form-check-label" for="remember_me">Ricordami</label>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Accedi
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-3">
                    <a href="<?php echo SITE_URL; ?>/forgot_password.php" class="text-decoration-none">
                        <i class="fas fa-key"></i> Password dimenticata?
                    </a>
                </div>
            </div>
            <div class="card-footer bg-light text-center py-3">
                <p class="mb-0">Non hai un account? <a href="<?php echo SITE_URL; ?>/register.php">Registrati qui</a></p>
            </div>
        </div>
        
        <div class="card mt-3 shadow">
            <div class="card-body text-center">
                <h5><i class="fas fa-info-circle"></i> Primo accesso?</h5>
                <p>Unisciti alla più grande community di tifosi bianconeri in Italia!</p>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-outline-primary">
                    <i class="fas fa-user-plus"></i> Crea un account
                </a>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript aggiuntivo per questa pagina
$extraJs = <<<EOT
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        var passwordInput = $('#password');
        var icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>
EOT;

// Includiamo il footer
require_once 'includes/footer.php';
?>
