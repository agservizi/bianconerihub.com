<?php
/**
 * Pagina dettaglio evento
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se è stato specificato l'ID dell'evento
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('Evento non specificato', 'error');
    redirect(SITE_URL . '/events.php');
}

// Otteniamo l'ID dell'evento
$eventId = intval($_GET['id']);

// Otteniamo i dettagli dell'evento
$eventQuery = "SELECT e.*, u.id as user_id, u.username, u.full_name, u.profile_pic, 
              (SELECT COUNT(*) FROM event_participants WHERE event_id = e.id AND status = 'going') as participants_count,
              (SELECT COUNT(*) FROM event_comments WHERE event_id = e.id) as comments_count
              FROM events e
              JOIN users u ON e.created_by = u.id
              WHERE e.id = {$eventId}";

$eventResult = $conn->query($eventQuery);

// Se l'evento non esiste, reindirizzamento alla pagina eventi
if (!$eventResult || $eventResult->num_rows === 0) {
    setFlashMessage('Evento non trovato', 'error');
    redirect(SITE_URL . '/events.php');
}

$event = $eventResult->fetch_assoc();

// Verifichiamo i permessi per gli eventi non pubblici
if ($event['visibility'] != 'public') {
    if (!isLoggedIn()) {
        setFlashMessage('Devi effettuare l\'accesso per visualizzare questo evento', 'error');
        redirect(SITE_URL . '/login.php');
    }
    
    if ($event['visibility'] == 'private' && $event['created_by'] != $_SESSION['user_id']) {
        setFlashMessage('Non hai i permessi per visualizzare questo evento privato', 'error');
        redirect(SITE_URL . '/events.php');
    }
    
    if ($event['visibility'] == 'followers') {
        // Verifichiamo se l'utente è un follower dell'organizzatore
        $followCheck = $conn->query("SELECT * FROM friendships 
                                    WHERE follower_id = {$_SESSION['user_id']} 
                                    AND following_id = {$event['created_by']}
                                    AND status = 'accepted'");
        
        if ($followCheck->num_rows === 0 && $event['created_by'] != $_SESSION['user_id']) {
            setFlashMessage('Questo evento è visibile solo ai follower dell\'organizzatore', 'error');
            redirect(SITE_URL . '/events.php');
        }
    }
}

// Formattiamo le date
$startDate = date("d M Y, H:i", strtotime($event['start_date']));
$endDate = !empty($event['end_date']) ? date("d M Y, H:i", strtotime($event['end_date'])) : null;

// Verifichiamo lo stato dell'evento
$isPast = (strtotime($event['start_date']) < time() && (!$event['end_date'] || strtotime($event['end_date']) < time())) || $event['status'] == 'completed';
$isOngoing = strtotime($event['start_date']) <= time() && (!$event['end_date'] || strtotime($event['end_date']) >= time()) && $event['status'] == 'ongoing';

// Verifichiamo la partecipazione dell'utente corrente se loggato
$userParticipation = null;
if (isLoggedIn()) {
    $participationQuery = "SELECT status FROM event_participants 
                          WHERE event_id = {$eventId} AND user_id = {$_SESSION['user_id']}";
    
    $participationResult = $conn->query($participationQuery);
    
    if ($participationResult && $participationResult->num_rows > 0) {
        $participation = $participationResult->fetch_assoc();
        $userParticipation = $participation['status'];
    }
}

// Otteniamo l'elenco dei partecipanti
$participantsQuery = "SELECT u.id, u.username, u.full_name, u.profile_pic, ep.status, ep.created_at 
                     FROM event_participants ep
                     JOIN users u ON ep.user_id = u.id
                     WHERE ep.event_id = {$eventId} AND ep.status = 'going'
                     ORDER BY ep.created_at DESC
                     LIMIT 20";

$participantsResult = $conn->query($participantsQuery);
$participants = [];

if ($participantsResult && $participantsResult->num_rows > 0) {
    while ($participant = $participantsResult->fetch_assoc()) {
        $participants[] = $participant;
    }
}

// Otteniamo i commenti all'evento
$commentsQuery = "SELECT ec.id, ec.comment, ec.created_at, 
                 u.id as user_id, u.username, u.full_name, u.profile_pic 
                 FROM event_comments ec
                 JOIN users u ON ec.user_id = u.id
                 WHERE ec.event_id = {$eventId}
                 ORDER BY ec.created_at DESC";

$commentsResult = $conn->query($commentsQuery);
$comments = [];

if ($commentsResult && $commentsResult->num_rows > 0) {
    while ($comment = $commentsResult->fetch_assoc()) {
        $comments[] = $comment;
    }
}

// Definiamo la classe del bordo in base allo stato
$cardBorderClass = $isPast ? 'border-secondary' : ($isOngoing ? 'border-success' : 'border-primary');
$cardHeaderClass = $isPast ? 'bg-secondary' : ($isOngoing ? 'bg-success' : 'bg-primary');

// Definiamo l'icona in base alla categoria
$categoryIcon = 'calendar-alt';
switch ($event['category']) {
    case 'match':
        $categoryIcon = 'futbol';
        break;
    case 'meeting':
        $categoryIcon = 'handshake';
        break;
    case 'fan_event':
        $categoryIcon = 'users';
        break;
    case 'watch_party':
        $categoryIcon = 'tv';
        break;
}

// Mappiamo il nome della categoria
$categoryNames = [
    'match' => 'Partita',
    'meeting' => 'Incontro',
    'fan_event' => 'Evento dei tifosi',
    'watch_party' => 'Watch Party',
    'other' => 'Altro'
];
$categoryName = isset($categoryNames[$event['category']]) ? $categoryNames[$event['category']] : 'Evento';

// Mappiamo il nome della visibilità
$visibilityNames = [
    'public' => 'Pubblico',
    'followers' => 'Solo follower',
    'private' => 'Privato'
];
$visibilityName = isset($visibilityNames[$event['visibility']]) ? $visibilityNames[$event['visibility']] : 'Sconosciuto';

// Mappiamo il nome dello stato
$statusNames = [
    'upcoming' => 'In arrivo',
    'ongoing' => 'In corso',
    'completed' => 'Completato',
    'cancelled' => 'Annullato'
];
$statusName = isset($statusNames[$event['status']]) ? $statusNames[$event['status']] : 'Sconosciuto';
?>

<div class="container py-4">
    <!-- Intestazione dell'evento -->
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/events.php">Eventi</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($event['title']); ?></li>
                </ol>
            </nav>
            
            <div class="card <?php echo $cardBorderClass; ?>">
                <?php if (!empty($event['image']) && $event['image'] != 'event_default.jpg'): ?>
                    <img src="<?php echo UPLOADS_URL; ?>/events/<?php echo $event['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>" style="max-height: 300px; object-fit: cover;">
                <?php endif; ?>
                
                <div class="card-header <?php echo $cardHeaderClass; ?> text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">
                            <i class="fas fa-<?php echo $categoryIcon; ?>"></i> <?php echo htmlspecialchars($event['title']); ?>
                        </h1>
                        
                        <?php if ($event['status'] == 'cancelled'): ?>
                            <span class="badge bg-danger">Annullato</span>
                        <?php elseif ($isPast): ?>
                            <span class="badge bg-secondary">Terminato</span>
                        <?php elseif ($isOngoing): ?>
                            <span class="badge bg-success">In corso</span>
                        <?php else: ?>
                            <span class="badge bg-primary">In arrivo</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Dettagli dell'evento -->
                            <div class="mb-4">
                                <div class="d-flex align-items-center mb-3">
                                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $event['user_id']; ?>">
                                        <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $event['profile_pic']; ?>" class="rounded-circle me-2" alt="<?php echo $event['username']; ?>" width="50" height="50">
                                    </a>
                                    <div>
                                        <h5 class="mb-0">
                                            <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $event['user_id']; ?>" class="text-decoration-none text-dark">
                                                <?php echo htmlspecialchars($event['full_name']); ?>
                                            </a>
                                        </h5>
                                        <div class="text-muted">
                                            @<?php echo $event['username']; ?> · 
                                            <small>Creato il <?php echo date("d M Y", strtotime($event['created_at'])); ?></small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="card-text fs-5"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <i class="fas fa-calendar-day text-primary"></i> <strong>Data inizio:</strong> 
                                            <?php echo $startDate; ?>
                                        </div>
                                        
                                        <?php if ($endDate): ?>
                                            <div class="mb-2">
                                                <i class="fas fa-calendar-check text-primary"></i> <strong>Data fine:</strong> 
                                                <?php echo $endDate; ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="mb-2">
                                            <i class="fas fa-map-marker-alt text-primary"></i> <strong>Luogo:</strong> 
                                            <?php echo htmlspecialchars($event['location']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-2">
                                            <i class="fas fa-<?php echo $categoryIcon; ?> text-primary"></i> <strong>Categoria:</strong> 
                                            <?php echo $categoryName; ?>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <i class="fas fa-lock text-primary"></i> <strong>Visibilità:</strong> 
                                            <?php echo $visibilityName; ?>
                                        </div>
                                        
                                        <div class="mb-2">
                                            <i class="fas fa-info-circle text-primary"></i> <strong>Stato:</strong> 
                                            <?php echo $statusName; ?>
                                        </div>
                                        
                                        <?php if ($event['capacity']): ?>
                                            <div class="mb-2">
                                                <i class="fas fa-users text-primary"></i> <strong>Capacità:</strong> 
                                                <?php echo $event['capacity']; ?> persone
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Azioni per l'evento -->
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Partecipazione</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (isLoggedIn()): ?>
                                        <?php if (!$isPast && $event['status'] != 'cancelled'): ?>
                                            <div class="d-grid gap-2 mb-3">
                                                <button class="btn btn-success participation-btn <?php echo $userParticipation == 'going' ? 'active' : ''; ?>" data-status="going" data-event-id="<?php echo $eventId; ?>">
                                                    <i class="fas fa-check-circle"></i> Parteciperò
                                                </button>
                                                <button class="btn btn-info participation-btn <?php echo $userParticipation == 'interested' ? 'active' : ''; ?>" data-status="interested" data-event-id="<?php echo $eventId; ?>">
                                                    <i class="fas fa-star"></i> Sono interessato
                                                </button>
                                                <button class="btn btn-danger participation-btn <?php echo $userParticipation == 'not_going' ? 'active' : ''; ?>" data-status="not_going" data-event-id="<?php echo $eventId; ?>">
                                                    <i class="fas fa-times-circle"></i> Non parteciperò
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-secondary mb-3">
                                                <?php if ($event['status'] == 'cancelled'): ?>
                                                    <i class="fas fa-ban"></i> L'evento è stato annullato.
                                                <?php else: ?>
                                                    <i class="fas fa-history"></i> L'evento è già terminato.
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="alert alert-primary mb-3">
                                            <i class="fas fa-info-circle"></i> <a href="<?php echo SITE_URL; ?>/login.php">Accedi</a> per indicare la tua partecipazione.
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span><i class="fas fa-users"></i> Partecipanti:</span>
                                            <span class="badge bg-primary"><?php echo $event['participants_count']; ?></span>
                                        </div>
                                        
                                        <?php if (!empty($participants)): ?>
                                            <div class="participant-avatars d-flex flex-wrap">
                                                <?php foreach (array_slice($participants, 0, 8) as $participant): ?>
                                                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $participant['id']; ?>" title="<?php echo htmlspecialchars($participant['full_name']); ?>">
                                                        <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $participant['profile_pic']; ?>" class="rounded-circle m-1" width="40" height="40" alt="<?php echo $participant['username']; ?>">
                                                    </a>
                                                <?php endforeach; ?>
                                                
                                                <?php if (count($participants) > 8): ?>
                                                    <a href="#" class="d-flex align-items-center justify-content-center rounded-circle bg-light m-1" style="width: 40px; height: 40px;" data-bs-toggle="modal" data-bs-target="#participantsModal">
                                                        <span class="text-muted">+<?php echo count($participants) - 8; ?></span>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted small">Nessun partecipante per ora</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isLoggedIn() && ($event['created_by'] == $_SESSION['user_id'] || isAdmin())): ?>
                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Gestione evento</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <a href="<?php echo SITE_URL; ?>/edit_event.php?id=<?php echo $eventId; ?>" class="btn btn-warning">
                                                <i class="fas fa-edit"></i> Modifica evento
                                            </a>
                                            
                                            <?php if ($event['status'] != 'cancelled'): ?>
                                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelEventModal">
                                                    <i class="fas fa-ban"></i> Annulla evento
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Condividi evento</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary share-btn" data-type="facebook">
                                            <i class="fab fa-facebook-f"></i> Facebook
                                        </button>
                                        <button class="btn btn-outline-info share-btn" data-type="twitter">
                                            <i class="fab fa-twitter"></i> Twitter
                                        </button>
                                        <button class="btn btn-outline-success share-btn" data-type="whatsapp">
                                            <i class="fab fa-whatsapp"></i> WhatsApp
                                        </button>
                                        <button class="btn btn-outline-secondary" id="copyLinkBtn">
                                            <i class="fas fa-link"></i> Copia link
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Sezione commenti -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="fas fa-comments"></i> Commenti (<?php echo count($comments); ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (isLoggedIn()): ?>
                        <form id="commentForm" class="mb-4">
                            <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
                            <div class="mb-3">
                                <textarea name="comment" class="form-control" placeholder="Scrivi un commento..." rows="3" required></textarea>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Invia commento
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="alert alert-primary mb-4">
                            <i class="fas fa-info-circle"></i> <a href="<?php echo SITE_URL; ?>/login.php">Accedi</a> per lasciare un commento.
                        </div>
                    <?php endif; ?>
                    
                    <div id="commentsList">
                        <?php if (empty($comments)): ?>
                            <div class="text-center py-4" id="noComments">
                                <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                                <h5>Nessun commento</h5>
                                <p class="text-muted">Sii il primo a commentare questo evento!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item mb-3" id="comment-<?php echo $comment['id']; ?>">
                                    <div class="d-flex">
                                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $comment['user_id']; ?>">
                                            <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $comment['profile_pic']; ?>" class="rounded-circle me-2" width="40" height="40" alt="<?php echo $comment['username']; ?>">
                                        </a>
                                        <div class="flex-grow-1">
                                            <div class="card">
                                                <div class="card-header bg-light py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $comment['user_id']; ?>" class="text-decoration-none text-dark fw-bold">
                                                                <?php echo htmlspecialchars($comment['full_name']); ?>
                                                            </a>
                                                            <span class="text-muted ms-2">@<?php echo $comment['username']; ?></span>
                                                        </div>
                                                        <small class="text-muted" title="<?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?>">
                                                            <?php echo timeAgo(strtotime($comment['created_at'])); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="card-body py-2">
                                                    <p class="card-text mb-0"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                                                </div>
                                                <?php if (isLoggedIn() && ($comment['user_id'] == $_SESSION['user_id'] || isAdmin() || $event['created_by'] == $_SESSION['user_id'])): ?>
                                                    <div class="card-footer bg-white py-1 text-end">
                                                        <button class="btn btn-sm btn-danger delete-comment-btn" data-comment-id="<?php echo $comment['id']; ?>">
                                                            <i class="fas fa-trash-alt"></i> Elimina
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal partecipanti -->
<?php if (!empty($participants)): ?>
<div class="modal fade" id="participantsModal" tabindex="-1" aria-labelledby="participantsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="participantsModalLabel">
                    <i class="fas fa-users"></i> Partecipanti all'evento (<?php echo count($participants); ?>)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($participants as $participant): ?>
                        <li class="list-group-item">
                            <div class="d-flex align-items-center">
                                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $participant['id']; ?>">
                                    <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $participant['profile_pic']; ?>" class="rounded-circle me-3" width="50" height="50" alt="<?php echo $participant['username']; ?>">
                                </a>
                                <div>
                                    <h6 class="mb-0">
                                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $participant['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($participant['full_name']); ?>
                                        </a>
                                    </h6>
                                    <div class="text-muted">@<?php echo $participant['username']; ?></div>
                                    <small class="text-muted">
                                        Partecipa da <?php echo timeAgo(strtotime($participant['created_at'])); ?>
                                    </small>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modal annullamento evento -->
<?php if (isLoggedIn() && ($event['created_by'] == $_SESSION['user_id'] || isAdmin()) && $event['status'] != 'cancelled'): ?>
<div class="modal fade" id="cancelEventModal" tabindex="-1" aria-labelledby="cancelEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="cancelEventModalLabel">
                    <i class="fas fa-exclamation-triangle"></i> Annulla evento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Sei sicuro di voler annullare questo evento?</p>
                <p>Tutti i partecipanti verranno notificati dell'annullamento.</p>
                <p class="mb-0 text-danger"><strong>Questa azione non può essere annullata.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annulla</button>
                <form id="cancelEventForm">
                    <input type="hidden" name="event_id" value="<?php echo $eventId; ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban"></i> Conferma annullamento
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
$(document).ready(function() {
    // Gestione della partecipazione all'evento
    $('.participation-btn').click(function() {
        var button = $(this);
        var status = button.data('status');
        var eventId = button.data('event-id');
        
        // Disabilitiamo tutti i pulsanti durante l'operazione
        $('.participation-btn').prop('disabled', true);
        
        $.ajax({
            url: '<?php echo SITE_URL; ?>/api/toggle_event_participation.php',
            type: 'POST',
            data: {
                event_id: eventId,
                status: status
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Aggiorniamo l'UI
                    $('.participation-btn').removeClass('active');
                    button.addClass('active');
                    
                    // Aggiorniamo il contatore dei partecipanti se necessario
                    if (response.participants_count !== undefined) {
                        $('.badge.bg-primary').text(response.participants_count);
                    }
                    
                    // Mostriamo un messaggio di conferma
                    showAlert('success', response.message);
                    
                    // Ricarichiamo la pagina se richiesto
                    if (response.reload) {
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                } else {
                    showAlert('danger', response.message || 'Si è verificato un errore');
                }
            },
            error: function() {
                showAlert('danger', 'Si è verificato un errore di comunicazione');
            },
            complete: function() {
                // Riattiviamo i pulsanti
                $('.participation-btn').prop('disabled', false);
            }
        });
    });
    
    // Invio commento
    $('#commentForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        
        // Disabilitiamo il pulsante durante l'invio
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '<?php echo SITE_URL; ?>/api/add_event_comment.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Aggiungiamo il commento alla lista
                    $('#noComments').remove();
                    $('#commentsList').prepend(response.html);
                    
                    // Puliamo il form
                    $('#commentForm textarea').val('');
                    
                    // Aggiorniamo il contatore dei commenti
                    var commentsTitle = $('.card-header h5:contains("Commenti")');
                    var count = parseInt(commentsTitle.text().match(/\((\d+)\)/)[1]);
                    commentsTitle.html('<i class="fas fa-comments"></i> Commenti (' + (count + 1) + ')');
                    
                    // Mostriamo un messaggio di conferma
                    showAlert('success', 'Commento aggiunto con successo');
                } else {
                    showAlert('danger', response.message || 'Si è verificato un errore');
                }
            },
            error: function() {
                showAlert('danger', 'Si è verificato un errore di comunicazione');
            },
            complete: function() {
                // Riattiviamo il pulsante
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    // Eliminazione commento
    $(document).on('click', '.delete-comment-btn', function() {
        var commentId = $(this).data('comment-id');
        var commentItem = $('#comment-' + commentId);
        
        if (confirm('Sei sicuro di voler eliminare questo commento?')) {
            $.ajax({
                url: '<?php echo SITE_URL; ?>/api/delete_event_comment.php',
                type: 'POST',
                data: { comment_id: commentId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Rimuoviamo il commento dalla lista
                        commentItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // Se non ci sono più commenti, mostriamo il messaggio
                            if ($('#commentsList .comment-item').length === 0) {
                                $('#commentsList').html(`
                                    <div class="text-center py-4" id="noComments">
                                        <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                                        <h5>Nessun commento</h5>
                                        <p class="text-muted">Sii il primo a commentare questo evento!</p>
                                    </div>
                                `);
                            }
                            
                            // Aggiorniamo il contatore dei commenti
                            var commentsTitle = $('.card-header h5:contains("Commenti")');
                            var countMatch = commentsTitle.text().match(/\((\d+)\)/);
                            if (countMatch) {
                                var count = parseInt(countMatch[1]);
                                commentsTitle.html('<i class="fas fa-comments"></i> Commenti (' + (count - 1) + ')');
                            }
                        });
                        
                        // Mostriamo un messaggio di conferma
                        showAlert('success', response.message);
                    } else {
                        showAlert('danger', response.message || 'Si è verificato un errore');
                    }
                },
                error: function() {
                    showAlert('danger', 'Si è verificato un errore di comunicazione');
                }
            });
        }
    });
    
    // Annullamento evento
    $('#cancelEventForm').submit(function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        
        // Disabilitiamo il pulsante durante l'invio
        submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '<?php echo SITE_URL; ?>/api/cancel_event.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Chiudiamo il modal
                    $('#cancelEventModal').modal('hide');
                    
                    // Mostriamo un messaggio di conferma
                    showAlert('success', response.message);
                    
                    // Ricarichiamo la pagina dopo un breve ritardo
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('danger', response.message || 'Si è verificato un errore');
                }
            },
            error: function() {
                showAlert('danger', 'Si è verificato un errore di comunicazione');
            },
            complete: function() {
                // Riattiviamo il pulsante
                submitBtn.prop('disabled', false);
            }
        });
    });
    
    // Condivisione evento
    $('.share-btn').click(function() {
        var type = $(this).data('type');
        var url = encodeURIComponent(window.location.href);
        var title = encodeURIComponent('<?php echo htmlspecialchars($event['title']); ?>');
        var shareUrl = '';
        
        switch (type) {
            case 'facebook':
                shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + url;
                break;
            case 'twitter':
                shareUrl = 'https://twitter.com/intent/tweet?url=' + url + '&text=' + title;
                break;
            case 'whatsapp':
                shareUrl = 'https://api.whatsapp.com/send?text=' + title + ' ' + url;
                break;
        }
        
        if (shareUrl) {
            window.open(shareUrl, '_blank', 'width=600,height=400');
        }
    });
    
    // Copia link
    $('#copyLinkBtn').click(function() {
        var dummy = document.createElement('input');
        document.body.appendChild(dummy);
        dummy.value = window.location.href;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        
        // Cambiamo temporaneamente il testo del pulsante
        var originalText = $(this).html();
        $(this).html('<i class="fas fa-check"></i> Link copiato!');
        
        setTimeout(function() {
            $('#copyLinkBtn').html(originalText);
        }, 2000);
    });
    
    // Funzione per mostrare un alert
    function showAlert(type, message) {
        var alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Inseriamo l'alert all'inizio del contenuto
        $('.container').prepend(alertHtml);
        
        // Facciamo sparire l'alert dopo 5 secondi
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>

<?php
// Includiamo il footer
require_once 'includes/footer.php';

/**
 * Funzione per formattare il tempo trascorso
 * 
 * @param int $timestamp Timestamp da formattare
 * @return string Tempo trascorso in formato leggibile
 */
function timeAgo($timestamp) {
    $now = time();
    $diff = $now - $timestamp;
    
    if ($diff < 60) {
        return $diff . " secondi fa";
    } else if ($diff < 3600) {
        return floor($diff / 60) . " minuti fa";
    } else if ($diff < 86400) {
        return floor($diff / 3600) . " ore fa";
    } else if ($diff < 604800) {
        return floor($diff / 86400) . " giorni fa";
    } else if ($diff < 2592000) {
        return floor($diff / 604800) . " settimane fa";
    } else if ($diff < 31536000) {
        return floor($diff / 2592000) . " mesi fa";
    } else {
        return floor($diff / 31536000) . " anni fa";
    }
}
?>
