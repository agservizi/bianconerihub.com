<?php
/**
 * Pagina di modifica evento
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Modifica Evento";

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per modificare un evento', 'error');
    redirect(SITE_URL . '/login.php');
}

// Verifichiamo se è stato specificato l'ID dell'evento
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('Evento non specificato', 'error');
    redirect(SITE_URL . '/events.php');
}

$eventId = intval($_GET['id']);

// Otteniamo i dettagli dell'evento
$stmt = $conn->prepare("
    SELECT e.*, u.username 
    FROM events e 
    JOIN users u ON e.user_id = u.id 
    WHERE e.id = ?
");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    setFlashMessage('Evento non trovato', 'error');
    redirect(SITE_URL . '/events.php');
}

$event = $result->fetch_assoc();

// Verifichiamo che l'utente sia il proprietario dell'evento o un amministratore
if ($event['user_id'] !== $_SESSION['user_id'] && !isAdmin()) {
    setFlashMessage('Non hai i permessi per modificare questo evento', 'error');
    redirect(SITE_URL . '/event.php?id=' . $eventId);
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title mb-0">Modifica Evento</h3>
                </div>
                <div class="card-body">
                    <form id="editEventForm" enctype="multipart/form-data">
                        <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
                        
                        <!-- Immagine dell'evento -->
                        <div class="mb-4 text-center">
                            <img src="<?php echo UPLOADS_URL . '/events/' . $event['image']; ?>" 
                                 alt="Immagine evento" 
                                 class="img-fluid mb-3 rounded" 
                                 style="max-height: 300px;">
                            <div class="mt-2">
                                <label for="image" class="btn btn-outline-primary">
                                    <i class="fas fa-camera"></i> Cambia immagine
                                </label>
                                <input type="file" id="image" name="image" class="d-none" accept="image/*">
                            </div>
                        </div>
                        
                        <!-- Titolo -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Titolo *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?php echo htmlspecialchars($event['title']); ?>" required>
                        </div>
                        
                        <!-- Descrizione -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrizione *</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="5" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                        </div>
                        
                        <!-- Luogo -->
                        <div class="mb-3">
                            <label for="location" class="form-label">Luogo *</label>
                            <input type="text" class="form-control" id="location" name="location" 
                                   value="<?php echo htmlspecialchars($event['location']); ?>" required>
                        </div>
                        
                        <!-- Data e ora inizio -->
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Data e ora inizio *</label>
                            <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($event['start_date'])); ?>" required>
                        </div>
                        
                        <!-- Data e ora fine -->
                        <div class="mb-3">
                            <label for="end_date" class="form-label">Data e ora fine</label>
                            <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                   value="<?php echo $event['end_date'] ? date('Y-m-d\TH:i', strtotime($event['end_date'])) : ''; ?>">
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo SITE_URL; ?>/event.php?id=<?php echo $eventId; ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Annulla
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salva Modifiche
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Preview dell'immagine
    $('#image').change(function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.img-fluid').attr('src', e.target.result);
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
    
    // Gestione del form
    $('#editEventForm').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin"></i> Salvataggio...');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: SITE_URL + '/api/edit_event.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Redirect alla pagina dell'evento dopo 1 secondo
                    setTimeout(function() {
                        window.location.href = SITE_URL + '/event.php?id=' + response.event_id;
                    }, 1000);
                } else {
                    showAlert('danger', response.message);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                showAlert('danger', 'Si è verificato un errore durante il salvataggio');
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>

<?php
// Includiamo il footer
require_once 'includes/footer.php';
?>
