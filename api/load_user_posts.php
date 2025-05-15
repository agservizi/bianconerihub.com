<?php
/**
 * API per caricare i post di un utente specifico
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    exit;
}

// Verifichiamo se abbiamo ricevuto i parametri necessari
if (!isset($_GET['user_id']) || empty($_GET['user_id']) || !isset($_GET['page']) || empty($_GET['page'])) {
    exit;
}

// Otteniamo i dati
$profileId = intval($_GET['user_id']);
$page = intval($_GET['page']);
$postsPerPage = POSTS_PER_PAGE;
$offset = ($page - 1) * $postsPerPage;
$userId = $_SESSION['user_id'];

// Verifichiamo se l'utente esiste
$userCheck = $conn->query("SELECT id, username, full_name, profile_pic FROM users WHERE id = {$profileId}");
if ($userCheck->num_rows === 0) {
    exit;
}

$profileUser = $userCheck->fetch_assoc();

// Otteniamo i post dell'utente
$postsQuery = "SELECT p.*, 
              (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
              (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
              FROM posts p 
              WHERE p.user_id = {$profileId} 
              ORDER BY p.created_at DESC 
              LIMIT {$postsPerPage} OFFSET {$offset}";

$postsResult = $conn->query($postsQuery);

if ($postsResult && $postsResult->num_rows > 0) {
    while ($post = $postsResult->fetch_assoc()) {
        // Calcoliamo il tempo trascorso
        $timestamp = strtotime($post['created_at']);
        $timeAgo = timeAgo($timestamp);
        
        // Verifichiamo se l'utente ha già messo like
        $likeCheck = $conn->query("SELECT id FROM likes WHERE post_id = {$post['id']} AND user_id = {$userId}");
        $hasLiked = ($likeCheck->num_rows > 0);
?>
        <div class="card mb-4 post" data-post-id="<?php echo $post['id']; ?>">
            <div class="card-header bg-white d-flex align-items-center">
                <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $profileUser['profile_pic']; ?>" alt="<?php echo $profileUser['username']; ?>" class="rounded-circle me-2" width="40" height="40">
                <div>
                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $profileUser['id']; ?>" class="text-decoration-none text-dark fw-bold">
                        <?php echo $profileUser['full_name']; ?>
                    </a>
                    <div class="text-muted small">
                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $profileUser['id']; ?>" class="text-decoration-none text-muted">
                            @<?php echo $profileUser['username']; ?>
                        </a> · 
                        <span title="<?php echo formatDate($post['created_at']); ?>"><?php echo $timeAgo; ?></span>
                    </div>
                </div>
                <?php if ($profileUser['id'] == $userId || isAdmin()): ?>
                    <div class="dropdown ms-auto">
                        <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?php echo SITE_URL; ?>/edit_post.php?id=<?php echo $post['id']; ?>">
                                    <i class="fas fa-edit"></i> Modifica
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger delete-post" href="#" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="fas fa-trash"></i> Elimina
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <p class="card-text"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                <?php if ($post['media']): ?>
                    <div class="mt-3">
                        <img src="<?php echo UPLOADS_URL; ?>/posts/<?php echo $post['media']; ?>" alt="Post media" class="img-fluid rounded">
                    </div>
                <?php endif; ?>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <button class="btn btn-sm <?php echo $hasLiked ? 'btn-primary' : 'btn-outline-primary'; ?> like-button" data-post-id="<?php echo $post['id']; ?>">
                            <i class="fas fa-thumbs-up"></i> Mi piace 
                            <span class="likes-count"><?php echo $post['likes_count']; ?></span>
                        </button>
                        <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-outline-secondary ms-2">
                            <i class="fas fa-comment"></i> Commenti 
                            <span class="comments-count"><?php echo $post['comments_count']; ?></span>
                        </a>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary share-button" data-post-id="<?php echo $post['id']; ?>">
                            <i class="fas fa-share"></i> Condividi
                        </button>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}

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
