<?php
/**
 * Pagina eventi
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Otteniamo il filtro dalle query string
$filter = isset($_GET['filter']) ? sanitizeInput($_GET['filter']) : 'upcoming';
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : 'all';

// Definiamo le categorie disponibili
$categories = [
    'all' => 'Tutte le categorie',
    'match' => 'Partite',
    'meeting' => 'Incontri',
    'fan_event' => 'Eventi dei tifosi',
    'watch_party' => 'Watch Party',
    'other' => 'Altri eventi'
];

// Costruiamo la query base
$query = "SELECT e.*, u.id as user_id, u.username, u.full_name, u.profile_pic, 
          (SELECT COUNT(*) FROM event_participants WHERE event_id = e.id AND status = 'going') as participants_count,
          (SELECT COUNT(*) FROM event_comments WHERE event_id = e.id) as comments_count
          FROM events e
          JOIN users u ON e.created_by = u.id
          WHERE e.visibility = 'public'";

// Aggiungiamo filtri per data
if ($filter == 'upcoming') {
    $query .= " AND e.start_date > NOW() AND e.status = 'upcoming'";
} elseif ($filter == 'ongoing') {
    $query .= " AND e.start_date <= NOW() AND (e.end_date IS NULL OR e.end_date >= NOW()) AND e.status = 'ongoing'";
} elseif ($filter == 'past') {
    $query .= " AND ((e.end_date IS NOT NULL AND e.end_date < NOW()) OR e.status = 'completed')";
} elseif ($filter == 'my_events' && isLoggedIn()) {
    $query .= " AND e.created_by = " . $_SESSION['user_id'];
} elseif ($filter == 'participating' && isLoggedIn()) {
    $query .= " AND EXISTS (SELECT 1 FROM event_participants WHERE event_id = e.id AND user_id = " . $_SESSION['user_id'] . " AND status = 'going')";
}

// Aggiungiamo filtro per categoria
if ($category != 'all') {
    $query .= " AND e.category = '{$category}'";
}

// Ordiniamo per data
if ($filter == 'past') {
    $query .= " ORDER BY e.start_date DESC";
} else {
    $query .= " ORDER BY e.start_date ASC";
}

// Eseguiamo la query
$result = $conn->query($query);
$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Otteniamo la partecipazione dell'utente per ogni evento se l'utente è loggato
$userParticipation = [];
if (isLoggedIn()) {
    $eventIds = [];
    foreach ($events as $event) {
        $eventIds[] = $event['id'];
    }
    
    if (!empty($eventIds)) {
        $eventIdsStr = implode(',', $eventIds);
        $participationQuery = "SELECT event_id, status FROM event_participants 
                              WHERE user_id = " . $_SESSION['user_id'] . " 
                              AND event_id IN ({$eventIdsStr})";
        
        $participationResult = $conn->query($participationQuery);
        
        if ($participationResult && $participationResult->num_rows > 0) {
            while ($row = $participationResult->fetch_assoc()) {
                $userParticipation[$row['event_id']] = $row['status'];
            }
        }
    }
}
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5 mb-0">
                <i class="fas fa-calendar-alt text-primary"></i> Eventi
            </h1>
            <p class="text-muted">Scopri gli eventi della comunità bianconera</p>
        </div>
        <?php if (isLoggedIn()): ?>
            <div class="col-md-4 text-end">
                <a href="<?php echo SITE_URL; ?>/create_event.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crea evento
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Filtri -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-3">
                    <div class="d-flex flex-wrap justify-content-between">
                        <div class="mb-2 mb-md-0">
                            <div class="btn-group" role="group">
                                <a href="<?php echo SITE_URL; ?>/events.php?filter=upcoming&category=<?php echo $category; ?>" class="btn btn-<?php echo $filter == 'upcoming' ? 'primary' : 'outline-primary'; ?>">
                                    <i class="fas fa-calendar-plus"></i> Prossimi
                                </a>
                                <a href="<?php echo SITE_URL; ?>/events.php?filter=ongoing&category=<?php echo $category; ?>" class="btn btn-<?php echo $filter == 'ongoing' ? 'primary' : 'outline-primary'; ?>">
                                    <i class="fas fa-calendar-day"></i> In corso
                                </a>
                                <a href="<?php echo SITE_URL; ?>/events.php?filter=past&category=<?php echo $category; ?>" class="btn btn-<?php echo $filter == 'past' ? 'primary' : 'outline-primary'; ?>">
                                    <i class="fas fa-calendar-check"></i> Passati
                                </a>
                                <?php if (isLoggedIn()): ?>
                                    <a href="<?php echo SITE_URL; ?>/events.php?filter=my_events&category=<?php echo $category; ?>" class="btn btn-<?php echo $filter == 'my_events' ? 'primary' : 'outline-primary'; ?>">
                                        <i class="fas fa-user-edit"></i> I miei eventi
                                    </a>
                                    <a href="<?php echo SITE_URL; ?>/events.php?filter=participating&category=<?php echo $category; ?>" class="btn btn-<?php echo $filter == 'participating' ? 'primary' : 'outline-primary'; ?>">
                                        <i class="fas fa-user-check"></i> Partecipo
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div>
                            <select class="form-select" id="categoryFilter" onchange="filterByCategory()">
                                <?php foreach ($categories as $key => $value): ?>
                                    <option value="<?php echo $key; ?>" <?php echo $category == $key ? 'selected' : ''; ?>><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (empty($events)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h3>Nessun evento trovato</h3>
                        <p class="text-muted">
                            <?php if ($filter == 'upcoming'): ?>
                                Non ci sono eventi in programma al momento.
                            <?php elseif ($filter == 'ongoing'): ?>
                                Non ci sono eventi in corso al momento.
                            <?php elseif ($filter == 'past'): ?>
                                Non ci sono eventi passati da visualizzare.
                            <?php elseif ($filter == 'my_events'): ?>
                                Non hai ancora creato nessun evento.
                            <?php elseif ($filter == 'participating'): ?>
                                Non stai partecipando a nessun evento al momento.
                            <?php endif; ?>
                        </p>
                        <?php if (isLoggedIn() && ($filter == 'upcoming' || $filter == 'my_events')): ?>
                            <a href="<?php echo SITE_URL; ?>/create_event.php" class="btn btn-primary mt-2">
                                <i class="fas fa-plus"></i> Crea un nuovo evento
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($events as $event): ?>
                <?php
                // Formattiamo le date
                $startDate = date("d M Y, H:i", strtotime($event['start_date']));
                $endDate = !empty($event['end_date']) ? date("d M Y, H:i", strtotime($event['end_date'])) : null;
                
                // Verifichiamo lo stato
                $isPast = (strtotime($event['start_date']) < time() && (!$event['end_date'] || strtotime($event['end_date']) < time())) || $event['status'] == 'completed';
                $isOngoing = strtotime($event['start_date']) <= time() && (!$event['end_date'] || strtotime($event['end_date']) >= time()) && $event['status'] == 'ongoing';
                
                // Partecipazione dell'utente
                $isParticipating = isLoggedIn() && isset($userParticipation[$event['id']]) && $userParticipation[$event['id']] == 'going';
                $isInterested = isLoggedIn() && isset($userParticipation[$event['id']]) && $userParticipation[$event['id']] == 'interested';
                $isNotGoing = isLoggedIn() && isset($userParticipation[$event['id']]) && $userParticipation[$event['id']] == 'not_going';
                
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
                ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 <?php echo $cardBorderClass; ?>">
                        <?php if (!empty($event['image']) && $event['image'] != 'event_default.jpg'): ?>
                            <img src="<?php echo UPLOADS_URL; ?>/events/<?php echo $event['image']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>" style="height: 180px; object-fit: cover;">
                        <?php else: ?>
                            <div class="card-img-top bg-light d-flex justify-content-center align-items-center" style="height: 180px;">
                                <i class="fas fa-<?php echo $categoryIcon; ?> fa-4x text-muted"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-header <?php echo $cardHeaderClass; ?> text-white py-2">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-<?php echo $categoryIcon; ?>"></i> 
                                <?php echo htmlspecialchars($event['title']); ?>
                            </h5>
                        </div>
                        
                        <div class="card-body">
                            <p class="card-text"><?php echo truncateText(htmlspecialchars($event['description']), 150); ?></p>
                            
                            <div class="mb-2">
                                <i class="fas fa-calendar-day"></i> <strong>Data:</strong> 
                                <?php echo $startDate; ?>
                                <?php if ($endDate): ?>
                                    <br><i class="fas fa-calendar-day"></i> <strong>Fine:</strong> <?php echo $endDate; ?>
                                <?php endif; ?>
                            </div>
                            
                            <div class="mb-2">
                                <i class="fas fa-map-marker-alt"></i> <strong>Luogo:</strong> 
                                <?php echo htmlspecialchars($event['location']); ?>
                            </div>
                            
                            <div class="mb-3">
                                <i class="fas fa-user-alt"></i> <strong>Organizzato da:</strong> 
                                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $event['user_id']; ?>" class="text-decoration-none">
                                    <?php echo htmlspecialchars($event['full_name']); ?>
                                </a>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-users"></i> <?php echo $event['participants_count']; ?> partecipanti
                                    </span>
                                    
                                    <span class="badge bg-secondary ms-1">
                                        <i class="fas fa-comments"></i> <?php echo $event['comments_count']; ?> commenti
                                    </span>
                                </div>
                                
                                <?php if (isLoggedIn() && $event['created_by'] == $_SESSION['user_id']): ?>
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-user-edit"></i> Il tuo evento
                                    </span>
                                <?php endif; ?>
                                
                                <?php if ($isParticipating): ?>
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle"></i> Partecipi
                                    </span>
                                <?php elseif ($isInterested): ?>
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-star"></i> Interessato
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-footer bg-white">
                            <div class="d-grid">
                                <a href="<?php echo SITE_URL; ?>/event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-<?php echo $isPast ? 'secondary' : ($isOngoing ? 'success' : 'primary'); ?>">
                                    <?php if ($isPast): ?>
                                        <i class="fas fa-history"></i> Visualizza dettagli
                                    <?php elseif ($isOngoing): ?>
                                        <i class="fas fa-door-open"></i> Evento in corso
                                    <?php else: ?>
                                        <i class="fas fa-calendar-alt"></i> Visualizza dettagli
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function filterByCategory() {
    const category = document.getElementById('categoryFilter').value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('category', category);
    window.location.href = currentUrl.toString();
}
</script>

<?php
// Includiamo il footer
require_once 'includes/footer.php';
?>
