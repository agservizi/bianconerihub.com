<?php
/**
 * Pagina di registrazione
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Registrazione";

// Includiamo l'header
require_once 'includes/header.php';

// Se l'utente è già autenticato, lo reindirizziamo alla home
if (isLoggedIn()) {
    redirect(SITE_URL);
}

// Variabili per i dati del form
$username = '';
$email = '';
$fullName = '';
$errMsg = '';
$success = false;

// Verifichiamo se il form è stato inviato
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Otteniamo i dati inviati
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $fullName = sanitizeInput($_POST['full_name']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validazione dei dati
    if (empty($username) || empty($email) || empty($fullName) || empty($password) || empty($confirmPassword)) {
        $errMsg = "Tutti i campi sono obbligatori";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errMsg = "L'indirizzo email non è valido";
    } else if (strlen($username) < 3 || strlen($username) > 20) {
        $errMsg = "Il nome utente deve avere tra 3 e 20 caratteri";
    } else if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errMsg = "Il nome utente può contenere solo lettere, numeri e underscore";
    } else if (strlen($password) < 6) {
        $errMsg = "La password deve avere almeno 6 caratteri";
    } else if ($password !== $confirmPassword) {
        $errMsg = "Le password non corrispondono";
    } else {
        // Verifichiamo se l'username esiste già
        $checkUsername = $conn->query("SELECT id FROM users WHERE username = '{$username}'");
        if ($checkUsername->num_rows > 0) {
            $errMsg = "Il nome utente è già in uso";
        } else {
            // Verifichiamo se l'email esiste già
            $checkEmail = $conn->query("SELECT id FROM users WHERE email = '{$email}'");
            if ($checkEmail->num_rows > 0) {
                $errMsg = "L'indirizzo email è già registrato";
            } else {
                // Hash della password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                  // Codice di verifica
                $verificationCode = md5(uniqid(mt_rand(), true));
                
                // Usiamo il logo come immagine di profilo predefinita
                $profilePic = 'logo.png';
                
                // Copiamo il logo nella cartella delle immagini di profilo se non esiste già
                $sourceLogo = __DIR__ . '/assets/images/logo.png';
                $destLogo = __DIR__ . '/uploads/profile_pics/logo.png';
                if (!file_exists($destLogo) && file_exists($sourceLogo)) {
                    copy($sourceLogo, $destLogo);
                }
                
                // Query di inserimento
                $query = "INSERT INTO users (username, email, password, full_name, profile_pic, verification_code)
                         VALUES ('{$username}', '{$email}', '{$hashedPassword}', '{$fullName}', '{$profilePic}', '{$verificationCode}')";
                
                if ($conn->query($query)) {
                    // Invio email di verifica (qui solo simulato)
                    // In una implementazione reale, invieresti una vera email
                    
                    // Registrazione completata con successo
                    $success = true;
                    
                    // Prepariamo un messaggio di notifica
                    setFlashMessage("Registrazione completata con successo! Controlla la tua email per verificare il tuo account.", "success");
                    
                    // Opzionale: autenticazione automatica
                    $userId = $conn->insert_id;
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;
                    $_SESSION['is_admin'] = 0;
                      // Aggiorniamo la data dell'ultimo accesso
                    try {
                        $updateLogin = $conn->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
                        $updateLogin->bind_param("i", $userId);
                        $updateLogin->execute();
                    } catch (Exception $e) {
                        error_log("Errore nell'aggiornamento last_login: " . $e->getMessage());
                        // Non blocchiamo il processo se questo update fallisce
                    }
                    
                    // Redirect alla home page
                    redirect(SITE_URL);
                } else {
                    $errMsg = "Si è verificato un errore durante la registrazione: " . $conn->error;
                }
            }
        }
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center py-3">
                <h2><i class="fas fa-user-plus"></i> Registrati a BiancoNeriHub</h2>
                <p class="mb-0">Unisciti alla community dei tifosi bianconeri</p>
            </div>
            <div class="card-body p-4">
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <h4 class="alert-heading">Registrazione completata!</h4>
                        <p>Il tuo account è stato creato con successo. Controlla la tua email per verificare il tuo account.</p>
                        <hr>
                        <p class="mb-0">Stai per essere reindirizzato alla home page...</p>
                    </div>
                    <script>
                        setTimeout(function() {
                            window.location.href = '<?php echo SITE_URL; ?>';
                        }, 5000);
                    </script>
                <?php else: ?>
                    <?php if ($errMsg): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $errMsg; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome utente <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Es. bianconero1897" required pattern="[a-zA-Z0-9_]{3,20}">
                            </div>
                            <small class="text-muted">Può contenere lettere, numeri e underscore (3-20 caratteri)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Es. tuo.nome@email.com" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Nome completo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($fullName); ?>" placeholder="Es. Mario Rossi" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimo 6 caratteri</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Conferma password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6" required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="termsCheck" required>
                            <label class="form-check-label" for="termsCheck">
                                Accetto i <a href="<?php echo SITE_URL; ?>/terms.php" target="_blank">Termini di servizio</a> e la <a href="<?php echo SITE_URL; ?>/privacy.php" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i> Registrati
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-light text-center py-3">
                <p class="mb-0">Hai già un account? <a href="<?php echo SITE_URL; ?>/login.php">Accedi qui</a></p>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript aggiuntivo per questa pagina
$extraJs = <<<EOT
<script>
$(document).ready(function() {
    // Validazione form
    (function() {
        'use strict';
        
        var forms = document.querySelectorAll('.needs-validation');
        
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                
                form.classList.add('was-validated');
            }, false);
        });
    })();
    
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
    
    $('#toggleConfirmPassword').click(function() {
        var passwordInput = $('#confirm_password');
        var icon = $(this).find('i');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordInput.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
    
    // Verifica username in tempo reale
    var usernameTimer;
    $('#username').on('input', function() {
        clearTimeout(usernameTimer);
        var username = $(this).val();
        
        if (username.length >= 3) {
            usernameTimer = setTimeout(function() {
                $.ajax({
                    url: SITE_URL + '/api/check_username.php',
                    type: 'POST',
                    data: { username: username },
                    dataType: 'json',
                    success: function(response) {
                        if (response.available) {
                            $('#username').removeClass('is-invalid').addClass('is-valid');
                        } else {
                            $('#username').removeClass('is-valid').addClass('is-invalid');
                        }
                    }
                });
            }, 500);
        }
    });
    
    // Verifica email in tempo reale
    var emailTimer;
    $('#email').on('input', function() {
        clearTimeout(emailTimer);
        var email = $(this).val();
        
        if (email.length >= 5 && email.includes('@')) {
            emailTimer = setTimeout(function() {
                $.ajax({
                    url: SITE_URL + '/api/check_email.php',
                    type: 'POST',
                    data: { email: email },
                    dataType: 'json',
                    success: function(response) {
                        if (response.available) {
                            $('#email').removeClass('is-invalid').addClass('is-valid');
                        } else {
                            $('#email').removeClass('is-valid').addClass('is-invalid');
                        }
                    }
                });
            }, 500);
        }
    });
    
    // Verifica corrispondenza password
    $('#confirm_password').on('input', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        if (confirmPassword !== '') {
            if (password === confirmPassword) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        }
    });
});
</script>
EOT;

// Includiamo il footer
require_once 'includes/footer.php';
?>
