<?php
/**
 * Dashboard utente
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per visualizzare la dashboard', 'error');
    redirect(SITE_URL . '/login.php');
}

// Otteniamo i dati dell'utente
$userId = $_SESSION['user_id'];
$userQuery = "SELECT * FROM users WHERE id = {$userId}";
$userResult = $conn->query($userQuery);
$user = $userResult->fetch_assoc();

// Statistiche dell'utente
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM posts WHERE user_id = {$userId}) as posts_count,
    (SELECT COUNT(*) FROM friendships WHERE follower_id = {$userId} AND status = 'accepted') as following_count,
    (SELECT COUNT(*) FROM friendships WHERE following_id = {$userId} AND status = 'accepted') as followers_count,
    (SELECT COUNT(*) FROM likes WHERE user_id = {$userId}) as likes_given_count,
    (SELECT COUNT(*) FROM comments WHERE user_id = {$userId}) as comments_count,
    (SELECT COUNT(*) FROM likes l JOIN posts p ON l.post_id = p.id WHERE p.user_id = {$userId}) as likes_received_count,
    (SELECT COUNT(*) FROM events WHERE created_by = {$userId}) as events_created_count,
    (SELECT COUNT(*) FROM event_participants WHERE user_id = {$userId} AND status = 'going') as events_attending_count
";
$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();

// Post recenti
$recentPostsQuery = "SELECT p.*, 
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
                    FROM posts p 
                    WHERE p.user_id = {$userId}
                    ORDER BY p.created_at DESC 
                    LIMIT 5";
$recentPostsResult = $conn->query($recentPostsQuery);
$recentPosts = [];
if ($recentPostsResult->num_rows > 0) {
    while ($post = $recentPostsResult->fetch_assoc()) {
        $recentPosts[] = $post;
    }
}

// Attività recenti (commenti, like, follow)
$activitiesQuery = "SELECT 'like' as type, l.created_at, p.id as reference_id, u.username, u.full_name, u.profile_pic,
                   SUBSTRING(p.content, 1, 50) as content
                   FROM likes l
                   JOIN posts p ON l.post_id = p.id
                   JOIN users u ON p.user_id = u.id
                   WHERE l.user_id = {$userId} AND u.id != {$userId}
                   
                   UNION ALL
                   
                   SELECT 'comment' as type, c.created_at, p.id as reference_id, u.username, u.full_name, u.profile_pic,
                   SUBSTRING(c.content, 1, 50) as content
                   FROM comments c
                   JOIN posts p ON c.post_id = p.id
                   JOIN users u ON p.user_id = u.id
                   WHERE c.user_id = {$userId} AND u.id != {$userId}
                   
                   UNION ALL
                   
                   SELECT 'follow' as type, f.created_at, u.id as reference_id, u.username, u.full_name, u.profile_pic,
                   NULL as content
                   FROM friendships f
                   JOIN users u ON f.following_id = u.id
                   WHERE f.follower_id = {$userId}
                   AND f.status = 'accepted'
                   
                   ORDER BY created_at DESC
                   LIMIT 10";
$activitiesResult = $conn->query($activitiesQuery);
$activities = [];
if ($activitiesResult->num_rows > 0) {
    while ($activity = $activitiesResult->fetch_assoc()) {
        $activities[] = $activity;
    }
}

// Notifiche recenti
$notificationsQuery = "SELECT n.*, u.username, u.full_name, u.profile_pic 
                      FROM notifications n
                      JOIN users u ON n.sender_id = u.id
                      WHERE n.user_id = {$userId}
                      ORDER BY n.created_at DESC
                      LIMIT 5";
$notificationsResult = $conn->query($notificationsQuery);
$notifications = [];
if ($notificationsResult->num_rows > 0) {
    while ($notification = $notificationsResult->fetch_assoc()) {
        $notifications[] = $notification;
    }
}

// Eventi a cui partecipi
$eventsQuery = "SELECT e.*, u.username, u.full_name, u.profile_pic,
               (SELECT COUNT(*) FROM event_participants WHERE event_id = e.id AND status = 'going') as participants_count
               FROM events e
               JOIN event_participants ep ON e.id = ep.event_id
               JOIN users u ON e.created_by = u.id
               WHERE ep.user_id = {$userId} AND ep.status = 'going'
               AND e.start_date >= NOW() AND e.status != 'cancelled'
               ORDER BY e.start_date ASC
               LIMIT 3";
$eventsResult = $conn->query($eventsQuery);
$upcomingEvents = [];
if ($eventsResult->num_rows > 0) {
    while ($event = $eventsResult->fetch_assoc()) {
        $upcomingEvents[] = $event;
    }
}

// Suggerimenti di utenti da seguire
$suggestionsQuery = "SELECT u.id, u.username, u.full_name, u.profile_pic, u.bio,
                    (SELECT COUNT(*) FROM friendships WHERE following_id = u.id AND status = 'accepted') as followers_count
                    FROM users u
                    WHERE u.id != {$userId}
                    AND u.id NOT IN (SELECT following_id FROM friendships WHERE follower_id = {$userId})
                    AND u.account_status = 'active'
                    ORDER BY followers_count DESC, RAND()
                    LIMIT 5";
$suggestionsResult = $conn->query($suggestionsQuery);
$suggestions = [];
if ($suggestionsResult->num_rows > 0) {
    while ($suggestion = $suggestionsResult->fetch_assoc()) {
        $suggestions[] = $suggestion;
    }
}
?>

<div class="container py-4">
    <div class="row">
        <!-- Colonna laterale sinistra con info utente e statistiche -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $user['profile_pic']; ?>" alt="<?php echo $user['username']; ?>" class="rounded-circle img-fluid mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                    <h5 class="card-title mb-0"><?php echo $user['full_name']; ?></h5>
                    <p class="text-muted">@<?php echo $user['username']; ?></p>
                    <div class="d-grid gap-2">
                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $userId; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-user"></i> Visualizza profilo
                        </a>
                        <a href="<?php echo SITE_URL; ?>/settings.php" class="btn btn-outline-secondary">
                            <i class="fas fa-cog"></i> Impostazioni
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-white p-0">
                    <div class="row text-center g-0">
                        <div class="col-4 border-end py-2">
                            <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $userId; ?>" class="text-decoration-none">
                                <h5 class="mb-0"><?php echo $stats['posts_count']; ?></h5>
                                <small class="text-muted">Post</small>
                            </a>
                        </div>
                        <div class="col-4 border-end py-2">
                            <a href="<?php echo SITE_URL; ?>/followers.php?id=<?php echo $userId; ?>" class="text-decoration-none">
                                <h5 class="mb-0"><?php echo $stats['followers_count']; ?></h5>
                                <small class="text-muted">Follower</small>
                            </a>
                        </div>
                        <div class="col-4 py-2">
                            <a href="<?php echo SITE_URL; ?>/following.php?id=<?php echo $userId; ?>" class="text-decoration-none">
                                <h5 class="mb-0"><?php echo $stats['following_count']; ?></h5>
                                <small class="text-muted">Seguiti</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistiche utente -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-line"></i> Le tue statistiche
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-heart text-danger"></i> Mi piace ricevuti</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $stats['likes_received_count']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-thumbs-up text-primary"></i> Mi piace dati</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $stats['likes_given_count']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-comment text-info"></i> Commenti</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $stats['comments_count']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-calendar-alt text-success"></i> Eventi creati</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $stats['events_created_count']; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-users text-warning"></i> Eventi a cui partecipi</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $stats['events_attending_count']; ?></span>
                        </li>
                    </ul>
                </div>
                <div class="card-footer bg-white text-center">
                    <small class="text-muted">Ultimo aggiornamento: <?php echo date('d/m/Y H:i'); ?></small>
                </div>
            </div>
            
            <!-- Suggerimenti di utenti da seguire -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user-plus"></i> Potresti conoscere
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($suggestions)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($suggestions as $suggestion): ?>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $suggestion['id']; ?>" class="me-3">
                                            <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $suggestion['profile_pic']; ?>" class="rounded-circle" width="48" height="48" alt="<?php echo $suggestion['username']; ?>">
                                        </a>
                                        <div class="flex-grow-1">
                                            <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $suggestion['id']; ?>" class="text-decoration-none">
                                                <h6 class="mb-0"><?php echo $suggestion['full_name']; ?></h6>
                                            </a>
                                            <small class="text-muted">@<?php echo $suggestion['username']; ?></small>
                                        </div>
                                        <button class="btn btn-sm btn-primary follow-button" data-user-id="<?php echo $suggestion['id']; ?>">
                                            <i class="fas fa-user-plus"></i> Segui
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Nessun suggerimento disponibile</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="<?php echo SITE_URL; ?>/search.php" class="text-decoration-none">Trova altre persone</a>
                </div>
            </div>
        </div>
        
        <!-- Contenuto principale -->
        <div class="col-md-6">
            <!-- Creazione post rapida -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Cosa vuoi condividere?</h5>
                    <form action="<?php echo SITE_URL; ?>/actions/create_post.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <textarea class="form-control" id="postContent" name="content" rows="3" placeholder="Condividi le tue idee sul mondo bianconero..." required></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="input-group w-50">
                                <input type="file" class="form-control" id="postMedia" name="media" accept="image/*">
                                <label class="input-group-text" for="postMedia"><i class="fas fa-image"></i></label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Pubblica
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Sezione post recenti -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-newspaper"></i> I tuoi post recenti
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($recentPosts)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($recentPosts as $post): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $user['profile_pic']; ?>" class="rounded-circle me-2" width="40" height="40" alt="<?php echo $user['username']; ?>">
                                        <div>
                                            <div class="fw-bold"><?php echo $user['full_name']; ?></div>
                                            <div class="text-muted small">
                                                <span title="<?php echo date('d/m/Y H:i', strtotime($post['created_at'])); ?>">
                                                    <?php echo timeAgo(strtotime($post['created_at'])); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mb-3"><?php echo nl2br(htmlspecialchars(truncateText($post['content'], 150))); ?></p>
                                    <?php if (!empty($post['media'])): ?>
                                        <div class="mb-3">
                                            <img src="<?php echo UPLOADS_URL; ?>/posts/<?php echo $post['media']; ?>" class="img-fluid rounded" alt="Post media">
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex">
                                        <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none me-3">
                                            <i class="fas fa-heart"></i> <?php echo $post['likes_count']; ?> Mi piace
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none me-3">
                                            <i class="fas fa-comment"></i> <?php echo $post['comments_count']; ?> Commenti
                                        </a>
                                        <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none ms-auto">
                                            <i class="fas fa-eye"></i> Visualizza
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="far fa-newspaper fa-3x text-muted mb-3"></i>
                            <h5>Nessun post recente</h5>
                            <p class="text-muted">Non hai ancora pubblicato nessun post.<br>Condividi qualcosa con la community!</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $userId; ?>" class="text-decoration-none">Visualizza tutti i tuoi post</a>
                </div>
            </div>
            
            <!-- Attività recenti -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-history"></i> Attività recenti
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($activities)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($activities as $activity): ?>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <?php if ($activity['type'] == 'like'): ?>
                                            <div class="activity-icon me-3 rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-heart"></i>
                                            </div>
                                            <div>
                                                Hai messo mi piace a un post di 
                                                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $activity['reference_id']; ?>" class="fw-bold text-decoration-none">
                                                    <?php echo $activity['full_name']; ?>
                                                </a>
                                                <div class="text-muted small">
                                                    <?php echo timeAgo(strtotime($activity['created_at'])); ?>
                                                </div>
                                                <?php if (!empty($activity['content'])): ?>
                                                    <div class="small text-muted mt-1">
                                                        "<?php echo htmlspecialchars(truncateText($activity['content'], 50)); ?>..."
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                        <?php elseif ($activity['type'] == 'comment'): ?>
                                            <div class="activity-icon me-3 rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-comment"></i>
                                            </div>
                                            <div>
                                                Hai commentato un post di 
                                                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $activity['reference_id']; ?>" class="fw-bold text-decoration-none">
                                                    <?php echo $activity['full_name']; ?>
                                                </a>
                                                <div class="text-muted small">
                                                    <?php echo timeAgo(strtotime($activity['created_at'])); ?>
                                                </div>
                                                <?php if (!empty($activity['content'])): ?>
                                                    <div class="small text-muted mt-1">
                                                        "<?php echo htmlspecialchars(truncateText($activity['content'], 50)); ?>..."
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            
                                        <?php elseif ($activity['type'] == 'follow'): ?>
                                            <div class="activity-icon me-3 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user-plus"></i>
                                            </div>
                                            <div>
                                                Hai iniziato a seguire 
                                                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $activity['reference_id']; ?>" class="fw-bold text-decoration-none">
                                                    <?php echo $activity['full_name']; ?>
                                                </a>
                                                <div class="text-muted small">
                                                    <?php echo timeAgo(strtotime($activity['created_at'])); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5>Nessuna attività recente</h5>
                            <p class="text-muted">Inizia a interagire con altri utenti per vedere le tue attività qui.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Colonna laterale destra con notifiche ed eventi -->
        <div class="col-md-3">
            <!-- Notifiche recenti -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell"></i> Notifiche recenti
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($notifications)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($notifications as $notification): ?>
                                <li class="list-group-item <?php echo $notification['is_read'] ? '' : 'bg-light'; ?>">
                                    <div class="d-flex">
                                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $notification['sender_id']; ?>" class="me-2">
                                            <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $notification['profile_pic']; ?>" class="rounded-circle" width="40" height="40" alt="<?php echo $notification['username']; ?>">
                                        </a>
                                        <div>
                                            <div class="small">
                                                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $notification['sender_id']; ?>" class="fw-bold text-decoration-none">
                                                    <?php echo $notification['full_name']; ?>
                                                </a> 
                                                <?php echo $notification['content']; ?>
                                            </div>
                                            <small class="text-muted">
                                                <?php echo timeAgo(strtotime($notification['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="far fa-bell fa-3x text-muted mb-3"></i>
                            <h5>Nessuna notifica</h5>
                            <p class="text-muted">Non hai ancora notifiche.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="<?php echo SITE_URL; ?>/notifications.php" class="text-decoration-none">Visualizza tutte le notifiche</a>
                </div>
            </div>
            
            <!-- Eventi a cui partecipi -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt"></i> Prossimi eventi
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($upcomingEvents)): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($upcomingEvents as $event): ?>
                                <?php 
                                // Formattiamo la data
                                $eventDate = date("d M Y, H:i", strtotime($event['start_date']));
                                ?>
                                <li class="list-group-item">
                                    <h6 class="mb-1">
                                        <a href="<?php echo SITE_URL; ?>/event.php?id=<?php echo $event['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($event['title']); ?>
                                        </a>
                                    </h6>
                                    <div class="small mb-2">
                                        <i class="fas fa-map-marker-alt text-danger"></i> <?php echo htmlspecialchars(truncateText($event['location'], 30)); ?>
                                    </div>
                                    <div class="small mb-2">
                                        <i class="fas fa-calendar-day text-primary"></i> <?php echo $eventDate; ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-users"></i> <?php echo $event['participants_count']; ?> partecipanti
                                        </small>
                                        <a href="<?php echo SITE_URL; ?>/event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            Dettagli
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="far fa-calendar-alt fa-3x text-muted mb-3"></i>
                            <h5>Nessun evento imminente</h5>
                            <p class="text-muted">Non stai partecipando a eventi futuri.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center">
                    <div class="d-flex justify-content-between">
                        <a href="<?php echo SITE_URL; ?>/events.php" class="text-decoration-none">Sfoglia eventi</a>
                        <a href="<?php echo SITE_URL; ?>/create_event.php" class="text-decoration-none">Crea evento</a>
                    </div>
                </div>
            </div>
            
            <!-- Collegamenti rapidi -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link"></i> Collegamenti rapidi
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="<?php echo SITE_URL; ?>/messages.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-envelope text-primary"></i> Messaggi</span>
                            <span class="badge bg-primary rounded-pill">
                                <?php 
                                // Conteggio messaggi non letti
                                $unreadMsgQuery = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = {$userId} AND is_read = 0";
                                $unreadMsgResult = $conn->query($unreadMsgQuery);
                                $unreadMsgCount = $unreadMsgResult->fetch_assoc()['count'];
                                echo $unreadMsgCount;
                                ?>
                            </span>
                        </a>
                        <a href="<?php echo SITE_URL; ?>/events.php?filter=participating" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-check text-success"></i> I miei eventi
                        </a>
                        <a href="<?php echo SITE_URL; ?>/search.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-search text-info"></i> Cerca utenti
                        </a>
                        <?php if (isAdmin()): ?>
                            <a href="<?php echo SITE_URL; ?>/admin/index.php" class="list-group-item list-group-item-action">
                                <i class="fas fa-user-shield text-danger"></i> Pannello di amministrazione
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Gestione del follow/unfollow
    $('.follow-button').click(function() {
        var button = $(this);
        var userId = button.data('user-id');
        
        $.ajax({
            url: SITE_URL + '/api/toggle_follow.php',
            type: 'POST',
            data: { user_id: userId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.is_following) {
                        button.removeClass('btn-primary').addClass('btn-secondary');
                        button.html('<i class="fas fa-user-check"></i> Segui già');
                    } else {
                        button.removeClass('btn-secondary').addClass('btn-primary');
                        button.html('<i class="fas fa-user-plus"></i> Segui');
                    }
                    
                    // Aggiorniamo la pagina dopo un breve ritardo
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            }
        });
    });
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

/**
 * Funzione per troncare un testo a una lunghezza massima
 * 
 * @param string $text Testo da troncare
 * @param int $maxLength Lunghezza massima
 * @return string Testo troncato
 */
function truncateText($text, $maxLength) {
    if (strlen($text) > $maxLength) {
        return substr($text, 0, $maxLength);
    }
    return $text;
}
?>
