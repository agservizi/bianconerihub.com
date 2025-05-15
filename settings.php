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
    <h1 class="mb-4">Impostazioni</h1>
    
    <div class="row">
        <!-- Menu laterale -->
        <div class="col-md-3">
            <div class="list-group">
                <a href="#appearance" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                    <i class="fas fa-user-circle"></i> Aspetto Profilo
                </a>
                <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-lock"></i> Sicurezza
                </a>
                <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-bell"></i> Notifiche
                </a>
                <a href="#privacy" class="list-group-item list-group-item-action" data-bs-toggle="list">
                    <i class="fas fa-shield-alt"></i> Privacy
                </a>
            </div>
        </div>
        
        <!-- Contenuto -->
        <div class="col-md-9">
            <div class="tab-content">
                <!-- Aspetto Profilo -->
                <div class="tab-pane fade show active" id="appearance">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Modifica Aspetto Profilo</h5>
                        </div>
                        <div class="card-body">
                            <form id="appearanceForm" enctype="multipart/form-data">
                                <!-- Immagine del profilo -->
                                <div class="mb-4 text-center">
                                    <img src="<?php echo UPLOADS_URL . '/profile_pics/' . $currentUser['profile_pic']; ?>" 
                                         alt="Immagine profilo" 
                                         class="rounded-circle profile-pic-preview mb-3" 
                                         style="width: 150px; height: 150px; object-fit: cover;">
                                    <div class="mt-2">
                                        <label for="profile_pic" class="btn btn-outline-primary">
                                            <i class="fas fa-camera"></i> Cambia foto
                                        </label>
                                        <input type="file" id="profile_pic" name="profile_pic" class="d-none" accept="image/*">
                                    </div>
                                </div>
                                
                                <!-- Bio -->
                                <div class="mb-3">
                                    <label for="bio" class="form-label">Bio</label>
                                    <textarea class="form-control" id="bio" name="bio" rows="3" 
                                              placeholder="Racconta qualcosa di te..."><?php echo htmlspecialchars($currentUser['bio'] ?? ''); ?></textarea>
                                </div>
                                
                                <!-- Località -->
                                <div class="mb-3">
                                    <label for="location" class="form-label">Località</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           placeholder="La tua città" 
                                           value="<?php echo htmlspecialchars($currentUser['location'] ?? ''); ?>">
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salva Modifiche
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Altre sezioni (da implementare) -->
                <div class="tab-pane fade" id="security">
                    <!-- Contenuto sicurezza -->
                </div>
                
                <div class="tab-pane fade" id="notifications">
                    <!-- Contenuto notifiche -->
                </div>
                
                <div class="tab-pane fade" id="privacy">
                    <!-- Contenuto privacy -->
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
