<?php
/**
 * Pagina di ricerca
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se è stata effettuata una ricerca
$query = isset($_GET['q']) ? sanitizeInput($_GET['q']) : '';
$type = isset($_GET['type']) ? sanitizeInput($_GET['type']) : 'all';

// Validazione dei parametri
$validTypes = ['all', 'users', 'posts', 'news', 'events'];
if (!in_array($type, $validTypes)) {
    $type = 'all';
}

// Inizializziamo le variabili per i risultati
$users = [];
$posts = [];
$news = [];
$events = [];

// Effettuiamo la ricerca solo se la query ha almeno 3 caratteri
if (strlen($query) >= 3) {
    // Prepariamo la query con i caratteri % per la ricerca LIKE
    $searchQuery = '%' . $query . '%';
    
    // Ricerca utenti
    if ($type == 'all' || $type == 'users') {
        $usersQuery = "SELECT id, username, full_name, profile_pic, bio 
                      FROM users 
                      WHERE (username LIKE '{$searchQuery}' OR full_name LIKE '{$searchQuery}') 
                      ORDER BY full_name 
                      LIMIT 20";
        
        $usersResult = $conn->query($usersQuery);
        
        if ($usersResult && $usersResult->num_rows > 0) {
            while ($user = $usersResult->fetch_assoc()) {
                $users[] = $user;
            }
        }
    }
    
    // Ricerca post
    if ($type == 'all' || $type == 'posts') {
        $postsQuery = "SELECT p.id, p.content, p.media, p.created_at, p.likes_count, p.comments_count, 
                      u.id as user_id, u.username, u.full_name, u.profile_pic 
                      FROM posts p 
                      JOIN users u ON p.user_id = u.id 
                      WHERE p.content LIKE '{$searchQuery}' 
                      ORDER BY p.created_at DESC 
                      LIMIT 20";
        
        $postsResult = $conn->query($postsQuery);
        
        if ($postsResult && $postsResult->num_rows > 0) {
            while ($post = $postsResult->fetch_assoc()) {
                $posts[] = $post;
            }
        }
    }
    
    // Ricerca notizie (se esiste la tabella)
    if (($type == 'all' || $type == 'news') && tableExists($conn, 'news')) {
        $newsQuery = "SELECT n.id, n.title, n.content, n.image, n.created_at, n.views_count, 
                     u.id as author_id, u.username, u.full_name 
                     FROM news n 
                     JOIN users u ON n.author_id = u.id 
                     WHERE n.title LIKE '{$searchQuery}' OR n.content LIKE '{$searchQuery}' 
                     ORDER BY n.created_at DESC 
                     LIMIT 20";
        
        $newsResult = $conn->query($newsQuery);
        
        if ($newsResult && $newsResult->num_rows > 0) {
            while ($newsItem = $newsResult->fetch_assoc()) {
                $news[] = $newsItem;
            }
        }
    }
    
    // Ricerca eventi (se esiste la tabella)
    if (($type == 'all' || $type == 'events') && tableExists($conn, 'events')) {
        $eventsQuery = "SELECT e.id, e.title, e.description, e.location, e.start_date, e.end_date, 
                       u.id as creator_id, u.username, u.full_name 
                       FROM events e 
                       JOIN users u ON e.created_by = u.id 
                       WHERE e.title LIKE '{$searchQuery}' OR e.description LIKE '{$searchQuery}' OR e.location LIKE '{$searchQuery}' 
                       ORDER BY e.start_date DESC 
                       LIMIT 20";
        
        $eventsResult = $conn->query($eventsQuery);
        
        if ($eventsResult && $eventsResult->num_rows > 0) {
            while ($event = $eventsResult->fetch_assoc()) {
                $events[] = $event;
            }
        }
    }
}

// Otteniamo il conteggio totale dei risultati
$totalResults = count($users) + count($posts) + count($news) + count($events);
?>

<div class="container py-4">
    <!-- Form di ricerca -->
    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form action="<?php echo SITE_URL; ?>/search.php" method="GET" class="d-flex">
                        <input type="text" class="form-control me-2" name="q" value="<?php echo htmlspecialchars($query); ?>" placeholder="Cerca persone, post, notizie, eventi..." aria-label="Cerca" required minlength="3">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (strlen($query) >= 3): ?>
        <!-- Filtri -->
        <div class="row mb-4">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-center">
                    <div class="btn-group" role="group">
                        <a href="<?php echo SITE_URL; ?>/search.php?q=<?php echo urlencode($query); ?>&type=all" class="btn btn-<?php echo $type == 'all' ? 'primary' : 'outline-primary'; ?>">
                            Tutti (<?php echo $totalResults; ?>)
                        </a>
                        <a href="<?php echo SITE_URL; ?>/search.php?q=<?php echo urlencode($query); ?>&type=users" class="btn btn-<?php echo $type == 'users' ? 'primary' : 'outline-primary'; ?>">
                            Utenti (<?php echo count($users); ?>)
                        </a>
                        <a href="<?php echo SITE_URL; ?>/search.php?q=<?php echo urlencode($query); ?>&type=posts" class="btn btn-<?php echo $type == 'posts' ? 'primary' : 'outline-primary'; ?>">
                            Post (<?php echo count($posts); ?>)
                        </a>
                        <?php if (tableExists($conn, 'news')): ?>
                            <a href="<?php echo SITE_URL; ?>/search.php?q=<?php echo urlencode($query); ?>&type=news" class="btn btn-<?php echo $type == 'news' ? 'primary' : 'outline-primary'; ?>">
                                Notizie (<?php echo count($news); ?>)
                            </a>
                        <?php endif; ?>
                        <?php if (tableExists($conn, 'events')): ?>
                            <a href="<?php echo SITE_URL; ?>/search.php?q=<?php echo urlencode($query); ?>&type=events" class="btn btn-<?php echo $type == 'events' ? 'primary' : 'outline-primary'; ?>">
                                Eventi (<?php echo count($events); ?>)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Risultati della ricerca -->
        <div class="row">
            <div class="col-md-8 mx-auto">
                <?php if ($totalResults > 0): ?>
                    <!-- Utenti -->
                    <?php if (($type == 'all' || $type == 'users') && count($users) > 0): ?>
                        <h3 class="mb-3"><?php echo ($type == 'all') ? 'Utenti' : 'Risultati'; ?></h3>
                        <div class="card mb-4">
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($users as $user): ?>
                                        <li class="list-group-item">
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $user['profile_pic']; ?>" alt="<?php echo $user['username']; ?>" class="rounded-circle me-3" width="50" height="50">
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h5 class="mb-0">
                                                                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $user['id']; ?>" class="text-decoration-none text-dark">
                                                                    <?php echo $user['full_name']; ?>
                                                                </a>
                                                            </h5>
                                                            <div class="text-muted">@<?php echo $user['username']; ?></div>
                                                        </div>
                                                        <?php if (isLoggedIn() && $_SESSION['user_id'] != $user['id']): ?>
                                                            <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                                Visualizza profilo
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php if (!empty($user['bio'])): ?>
                                                        <p class="mb-0 mt-2"><?php echo truncateText(htmlspecialchars($user['bio']), 100); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Post -->
                    <?php if (($type == 'all' || $type == 'posts') && count($posts) > 0): ?>
                        <h3 class="mb-3"><?php echo ($type == 'all') ? 'Post' : 'Risultati'; ?></h3>
                        <?php foreach ($posts as $post): ?>
                            <?php 
                            // Calcoliamo il tempo trascorso
                            $timestamp = strtotime($post['created_at']);
                            $timeAgo = timeAgo($timestamp);
                            
                            // Evidenziamo il testo cercato
                            $highlightedContent = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="bg-warning">$1</span>', htmlspecialchars($post['content']));
                            ?>
                            <div class="card mb-3">
                                <div class="card-header bg-white d-flex align-items-center">
                                    <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $post['profile_pic']; ?>" alt="<?php echo $post['username']; ?>" class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-dark fw-bold">
                                            <?php echo $post['full_name']; ?>
                                        </a>
                                        <div class="text-muted small">
                                            <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $post['user_id']; ?>" class="text-decoration-none text-muted">
                                                @<?php echo $post['username']; ?>
                                            </a> · 
                                            <span title="<?php echo formatDate($post['created_at']); ?>"><?php echo $timeAgo; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="card-text"><?php echo nl2br($highlightedContent); ?></p>
                                    <?php if ($post['media']): ?>
                                        <div class="mt-3">
                                            <img src="<?php echo UPLOADS_URL; ?>/posts/<?php echo $post['media']; ?>" alt="Post media" class="img-fluid rounded">
                                        </div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <div>
                                            <span class="text-muted">
                                                <i class="fas fa-thumbs-up"></i> <?php echo $post['likes_count']; ?> Mi piace
                                            </span>
                                            <span class="text-muted ms-3">
                                                <i class="fas fa-comment"></i> <?php echo $post['comments_count']; ?> Commenti
                                            </span>
                                        </div>
                                        <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            Visualizza post
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    
                    <!-- Notizie -->
                    <?php if (($type == 'all' || $type == 'news') && count($news) > 0): ?>
                        <h3 class="mb-3"><?php echo ($type == 'all') ? 'Notizie' : 'Risultati'; ?></h3>
                        <div class="row">
                            <?php foreach ($news as $newsItem): ?>
                                <?php 
                                // Calcoliamo il tempo trascorso
                                $timestamp = strtotime($newsItem['created_at']);
                                $timeAgo = timeAgo($timestamp);
                                
                                // Evidenziamo il testo cercato nel titolo
                                $highlightedTitle = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="bg-warning">$1</span>', htmlspecialchars($newsItem['title']));
                                
                                // Evidenziamo il testo cercato nel contenuto e tronchiamo
                                $highlightedContent = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="bg-warning">$1</span>', htmlspecialchars(truncateText($newsItem['content'], 150)));
                                ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100">
                                        <?php if ($newsItem['image']): ?>
                                            <img src="<?php echo UPLOADS_URL; ?>/news/<?php echo $newsItem['image']; ?>" class="card-img-top" alt="<?php echo $newsItem['title']; ?>" style="height: 200px; object-fit: cover;">
                                        <?php endif; ?>
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $highlightedTitle; ?></h5>
                                            <p class="card-text"><?php echo $highlightedContent; ?></p>
                                        </div>
                                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?php echo $newsItem['full_name']; ?> · 
                                                <i class="fas fa-clock"></i> <?php echo $timeAgo; ?> · 
                                                <i class="fas fa-eye"></i> <?php echo $newsItem['views_count']; ?> visualizzazioni
                                            </small>
                                            <a href="<?php echo SITE_URL; ?>/news.php?id=<?php echo $newsItem['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                Leggi
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Eventi -->
                    <?php if (($type == 'all' || $type == 'events') && count($events) > 0): ?>
                        <h3 class="mb-3"><?php echo ($type == 'all') ? 'Eventi' : 'Risultati'; ?></h3>
                        <div class="row">
                            <?php foreach ($events as $event): ?>
                                <?php 
                                // Formattiamo le date
                                $startDate = formatDate($event['start_date'], 'd M Y, H:i');
                                $endDate = !empty($event['end_date']) ? formatDate($event['end_date'], 'd M Y, H:i') : '';
                                
                                // Evidenziamo il testo cercato nel titolo
                                $highlightedTitle = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="bg-warning">$1</span>', htmlspecialchars($event['title']));
                                
                                // Evidenziamo il testo cercato nella descrizione e tronchiamo
                                $highlightedDescription = preg_replace('/(' . preg_quote($query, '/') . ')/i', '<span class="bg-warning">$1</span>', htmlspecialchars(truncateText($event['description'], 100)));
                                
                                // Calcoliamo se l'evento è passato
                                $isPast = strtotime($event['start_date']) < time();
                                ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 <?php echo $isPast ? 'border-secondary' : 'border-primary'; ?>">
                                        <div class="card-header bg-<?php echo $isPast ? 'secondary' : 'primary'; ?> text-white">
                                            <h5 class="card-title mb-0"><?php echo $highlightedTitle; ?></h5>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text"><?php echo $highlightedDescription; ?></p>
                                            <div class="mb-2">
                                                <i class="fas fa-calendar-alt"></i> <strong>Data:</strong> <?php echo $startDate; ?>
                                                <?php if (!empty($endDate)): ?>
                                                    - <?php echo $endDate; ?>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($event['location'])): ?>
                                                <div>
                                                    <i class="fas fa-map-marker-alt"></i> <strong>Luogo:</strong> <?php echo htmlspecialchars($event['location']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> Organizzato da <?php echo $event['full_name']; ?>
                                            </small>
                                            <a href="<?php echo SITE_URL; ?>/event.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-<?php echo $isPast ? 'secondary' : 'primary'; ?>">
                                                <?php echo $isPast ? 'Visualizza' : 'Partecipa'; ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="py-5">
                                <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                <h4>Nessun risultato trovato</h4>
                                <p class="text-muted">
                                    Non è stato trovato alcun risultato per "<strong><?php echo htmlspecialchars($query); ?></strong>".
                                </p>
                                <p class="mb-0">Suggerimenti:</p>
                                <ul class="list-unstyled">
                                    <li>Verifica che tutte le parole siano scritte correttamente</li>
                                    <li>Prova con parole chiave diverse</li>
                                    <li>Prova con parole chiave più generiche</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="py-5">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <h4>Cerca in BiancoNeriHub</h4>
                            <p class="text-muted">
                                Cerca utenti, post, notizie ed eventi. <br>
                                Inserisci almeno 3 caratteri per avviare la ricerca.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

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
 * Funzione per verificare se una tabella esiste
 * 
 * @param mysqli $conn Connessione al database
 * @param string $tableName Nome della tabella
 * @return bool True se la tabella esiste, false altrimenti
 */
function tableExists($conn, $tableName) {
    $result = $conn->query("SHOW TABLES LIKE '{$tableName}'");
    return $result && $result->num_rows > 0;
}
?>
