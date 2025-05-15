<?php
/**
 * API per caricare più post nel feed
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo la configurazione
require_once '../config/config.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    exit;
}

// Verifichiamo se abbiamo ricevuto la pagina
if (!isset($_GET['page']) || empty($_GET['page'])) {
    exit;
}

// Otteniamo i dati
$page = intval($_GET['page']);
$userId = $_SESSION['user_id'];
$postsPerPage = POSTS_PER_PAGE;
$offset = ($page - 1) * $postsPerPage;

// Query per ottenere i post più recenti degli amici e dell'utente corrente
$query = "SELECT p.*, u.username, u.full_name, u.profile_pic 
          FROM posts p 
          JOIN users u ON p.user_id = u.id 
          WHERE p.user_id = {$userId} 
          OR p.user_id IN (
              SELECT following_id FROM friendships 
              WHERE follower_id = {$userId} AND status = 'accepted'
          ) 
          ORDER BY p.created_at DESC 
          LIMIT {$postsPerPage} OFFSET {$offset}";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($post = $result->fetch_assoc()) {
        // Calcoliamo il tempo trascorso
        $timestamp = strtotime($post['created_at']);
        $timeAgo = timeAgo($timestamp);
        
        // Verifichiamo se l'utente ha già messo like
        $likeQuery = "SELECT id FROM likes WHERE post_id = {$post['id']} AND user_id = {$userId}";
        $likeResult = $conn->query($likeQuery);
        $hasLiked = ($likeResult && $likeResult->num_rows > 0);
        
        // Otteniamo alcuni commenti
        $commentsQuery = "SELECT c.*, u.username, u.profile_pic 
                         FROM comments c 
                         JOIN users u ON c.user_id = u.id 
                         WHERE c.post_id = {$post['id']} 
                         ORDER BY c.created_at DESC 
                         LIMIT 3";
        $commentsResult = $conn->query($commentsQuery);
?>
        <div class="card mb-4 post" data-post-id="<?php echo $post['id']; ?>">
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
                <?php if ($post['user_id'] == $userId): ?>
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
                        <button class="btn btn-sm btn-outline-secondary ms-2 comment-button" data-post-id="<?php echo $post['id']; ?>">
                            <i class="fas fa-comment"></i> Commenti 
                            <span class="comments-count"><?php echo $post['comments_count']; ?></span>
                        </button>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary share-button" data-post-id="<?php echo $post['id']; ?>">
                            <i class="fas fa-share"></i> Condividi
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Sezione commenti -->
            <div class="card-footer bg-white">
                <div class="comments-section">
                    <?php if ($commentsResult && $commentsResult->num_rows > 0): ?>
                        <?php while ($comment = $commentsResult->fetch_assoc()): ?>
                            <div class="d-flex mb-3 comment">
                                <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $comment['profile_pic']; ?>" alt="<?php echo $comment['username']; ?>" class="rounded-circle me-2" width="32" height="32">
                                <div class="comment-content p-2 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $comment['user_id']; ?>" class="text-decoration-none text-dark fw-bold">
                                            @<?php echo $comment['username']; ?>
                                        </a>
                                        <small class="text-muted"><?php echo timeAgo(strtotime($comment['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <?php if ($post['comments_count'] > 3): ?>
                            <div class="text-center mb-3">
                                <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $post['id']; ?>" class="text-decoration-none">
                                    Visualizza tutti i <?php echo $post['comments_count']; ?> commenti
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <!-- Form per aggiungere un commento -->
                    <div class="d-flex">
                        <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo getCurrentUser()['profile_pic']; ?>" alt="<?php echo getCurrentUser()['username']; ?>" class="rounded-circle me-2" width="32" height="32">
                        <div class="flex-grow-1">
                            <form action="<?php echo SITE_URL; ?>/actions/add_comment.php" method="POST" class="comment-form">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="content" placeholder="Scrivi un commento...">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
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
