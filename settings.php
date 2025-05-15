<?php
/**
 * Pagina delle impostazioni utente
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per modificare le impostazioni', 'error');
    redirect(SITE_URL . '/login.php');
}

$userId = $_SESSION['user_id'];
$currentUser = getCurrentUser();

// Se l'utente non esiste (nel caso in cui fosse stato eliminato)
if (!$currentUser) {
    // Distruggiamo la sessione
    session_destroy();
    setFlashMessage('La tua sessione è scaduta. Effettua nuovamente l\'accesso.', 'error');
    redirect(SITE_URL . '/login.php');
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog"></i> Impostazioni
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush settings-nav">
                        <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                            <i class="fas fa-user"></i> Profilo
                        </a>
                        <a href="#account" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-lock"></i> Account
                        </a>
                        <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-bell"></i> Notifiche
                        </a>
                        <a href="#privacy" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-shield-alt"></i> Privacy
                        </a>
                        <a href="#appearance" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-palette"></i> Aspetto
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-body text-center">
                    <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $currentUser['profile_pic']; ?>" alt="<?php echo $currentUser['username']; ?>" class="rounded-circle mb-3" width="100" height="100">
                    <h5><?php echo $currentUser['full_name']; ?></h5>
                    <p class="text-muted">@<?php echo $currentUser['username']; ?></p>
                    <a href="<?php echo SITE_URL; ?>/profile.php" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye"></i> Visualizza profilo
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Impostazioni Profilo -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user"></i> Modifica profilo
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>/actions/update_profile.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Nome completo</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($currentUser['full_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Biografia</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3"><?php echo htmlspecialchars($currentUser['bio'] ?? ''); ?></textarea>
                                    <div class="form-text">Racconta qualcosa su di te e sulla tua passione per la Juventus.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="location" class="form-label">Posizione</label>
                                    <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($currentUser['location'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="profile_pic" class="form-label">Immagine profilo</label>
                                    <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                                    <div class="form-text">File supportati: JPG, PNG, GIF. Dimensione massima: 5MB.</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Salva modifiche
                                    </button>
                                    <a href="<?php echo SITE_URL; ?>/profile.php" class="btn btn-outline-secondary">
                                        Annulla
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Impostazioni Account -->
                <div class="tab-pane fade" id="account">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-envelope"></i> Modifica email
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>/actions/update_email.php" method="POST">
                                <div class="mb-3">
                                    <label for="current_email" class="form-label">Email attuale</label>
                                    <input type="email" class="form-control" id="current_email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" disabled readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="new_email" class="form-label">Nuova email</label>
                                    <input type="email" class="form-control" id="new_email" name="new_email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password_email" class="form-label">Password attuale</label>
                                    <input type="password" class="form-control" id="password_email" name="password" required>
                                    <div class="form-text">Inserisci la tua password attuale per confermare la modifica.</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Aggiorna email
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-lock"></i> Modifica password
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>/actions/update_password.php" method="POST">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password attuale</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Nuova password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                                    <div class="form-text">La password deve contenere almeno 8 caratteri, inclusi lettere, numeri e caratteri speciali.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Conferma nuova password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Aggiorna password
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Impostazioni Notifiche -->
                <div class="tab-pane fade" id="notifications">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bell"></i> Preferenze notifiche
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>/actions/update_notifications.php" method="POST">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" <?php echo isset($currentUser['email_notifications']) && $currentUser['email_notifications'] == 1 ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="email_notifications">Ricevi notifiche via email</label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Notifiche per:</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="notify_likes" name="notify_likes" value="1" <?php echo isset($currentUser['notify_likes']) && $currentUser['notify_likes'] == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="notify_likes">Mi piace sui tuoi post</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="notify_comments" name="notify_comments" value="1" <?php echo isset($currentUser['notify_comments']) && $currentUser['notify_comments'] == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="notify_comments">Commenti sui tuoi post</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="notify_follows" name="notify_follows" value="1" <?php echo isset($currentUser['notify_follows']) && $currentUser['notify_follows'] == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="notify_follows">Nuovi follower</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="notify_messages" name="notify_messages" value="1" <?php echo isset($currentUser['notify_messages']) && $currentUser['notify_messages'] == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="notify_messages">Nuovi messaggi</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="notify_mentions" name="notify_mentions" value="1" <?php echo isset($currentUser['notify_mentions']) && $currentUser['notify_mentions'] == 1 ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="notify_mentions">Menzioni nei post e commenti</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salva preferenze
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Impostazioni Privacy -->
                <div class="tab-pane fade" id="privacy">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-shield-alt"></i> Impostazioni privacy
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>/actions/update_privacy.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Chi può visualizzare il tuo profilo:</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="profile_visibility" id="profile_public" value="public" <?php echo (!isset($currentUser['profile_visibility']) || $currentUser['profile_visibility'] == 'public') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="profile_public">
                                            Pubblico (tutti gli utenti)
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="profile_visibility" id="profile_followers" value="followers" <?php echo (isset($currentUser['profile_visibility']) && $currentUser['profile_visibility'] == 'followers') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="profile_followers">
                                            Solo follower
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Chi può inviarti messaggi:</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="message_privacy" id="message_everyone" value="everyone" <?php echo (!isset($currentUser['message_privacy']) || $currentUser['message_privacy'] == 'everyone') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="message_everyone">
                                            Tutti gli utenti
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="message_privacy" id="message_followers" value="followers" <?php echo (isset($currentUser['message_privacy']) && $currentUser['message_privacy'] == 'followers') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="message_followers">
                                            Solo follower
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="message_privacy" id="message_mutual" value="mutual" <?php echo (isset($currentUser['message_privacy']) && $currentUser['message_privacy'] == 'mutual') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="message_mutual">
                                            Solo chi segui anche tu
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" id="show_online_status" name="show_online_status" value="1" <?php echo (!isset($currentUser['show_online_status']) || $currentUser['show_online_status'] == 1) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="show_online_status">Mostra il tuo stato online</label>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salva impostazioni
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Impostazioni Aspetto -->
                <div class="tab-pane fade" id="appearance">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-palette"></i> Personalizza aspetto
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>/actions/update_appearance.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Tema:</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="theme" id="theme_light" value="light" <?php echo (!isset($currentUser['theme']) || $currentUser['theme'] == 'light') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="theme_light">
                                            Chiaro
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="theme" id="theme_dark" value="dark" <?php echo (isset($currentUser['theme']) && $currentUser['theme'] == 'dark') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="theme_dark">
                                            Scuro
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="theme" id="theme_auto" value="auto" <?php echo (isset($currentUser['theme']) && $currentUser['theme'] == 'auto') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="theme_auto">
                                            Automatico (segue le impostazioni del sistema)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Layout della timeline:</label>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="timeline_layout" id="timeline_compact" value="compact" <?php echo (isset($currentUser['timeline_layout']) && $currentUser['timeline_layout'] == 'compact') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="timeline_compact">
                                            Compatto
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="timeline_layout" id="timeline_standard" value="standard" <?php echo (!isset($currentUser['timeline_layout']) || $currentUser['timeline_layout'] == 'standard') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="timeline_standard">
                                            Standard
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="timeline_layout" id="timeline_comfortable" value="comfortable" <?php echo (isset($currentUser['timeline_layout']) && $currentUser['timeline_layout'] == 'comfortable') ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="timeline_comfortable">
                                            Confortevole (più spazioso)
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Applica preferenze
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestisce le tab in base all'hash URL
    var hash = window.location.hash;
    if (hash) {
        $('.settings-nav a[href="' + hash + '"]').tab('show');
    }
    
    // Aggiorna URL hash quando si cambia tab
    $('.settings-nav a').on('click', function(e) {
        window.location.hash = $(this).attr('href');
    });
    
    // Preview immagine profilo
    $('#profile_pic').change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('.card-body img').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>

<?php
// Includiamo il footer
require_once 'includes/footer.php';
?>
