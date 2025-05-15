<?php
/**
 * Pagina per visualizzare un singolo post
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se è stato specificato l'ID del post
if (!isset($_GET['id']) || empty($_GET['id'])) {
    setFlashMessage('Post non specificato', 'error');
    redirect(SITE_URL);
}

$postId = intval($_GET['id']);
$userId = isLoggedIn() ? $_SESSION['user_id'] : 0;

// Otteniamo i dettagli del post
$postQuery = "SELECT p.*, u.username, u.full_name, u.profile_pic 
             FROM posts p 
             JOIN users u ON p.user_id = u.id 
             WHERE p.id = {$postId}";

$postResult = $conn->query($postQuery);

// Se il post non esiste, reindirizzamento alla home
if (!$postResult || $postResult->num_rows === 0) {
    setFlashMessage('Post non trovato', 'error');
    redirect(SITE_URL);
}

$post = $postResult->fetch_assoc();

// Verifichiamo se l'utente ha già messo like
$hasLiked = false;
if ($userId > 0) {
    $likeQuery = "SELECT id FROM likes WHERE post_id = {$postId} AND user_id = {$userId}";
    $likeResult = $conn->query($likeQuery);
    $hasLiked = ($likeResult && $likeResult->num_rows > 0);
}

// Otteniamo tutti i commenti del post
$commentsPerPage = COMMENTS_PER_PAGE;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $commentsPerPage;

$commentsQuery = "SELECT c.*, u.username, u.full_name, u.profile_pic 
                 FROM comments c 
                 JOIN users u ON c.user_id = u.id 
                 WHERE c.post_id = {$postId} 
                 ORDER BY c.created_at DESC
                 LIMIT {$commentsPerPage} OFFSET {$offset}";

$commentsResult = $conn->query($commentsQuery);
$comments = [];

if ($commentsResult && $commentsResult->num_rows > 0) {
    while ($comment = $commentsResult->fetch_assoc()) {
        $comments[] = $comment;
    }
}

// Otteniamo il conteggio totale dei commenti per la paginazione
$totalCommentsQuery = "SELECT COUNT(*) as count FROM comments WHERE post_id = {$postId}";
$totalCommentsResult = $conn->query($totalCommentsQuery);
$totalComments = ($totalCommentsResult) ? $totalCommentsResult->fetch_assoc()['count'] : 0;
$totalPages = ceil($totalComments / $commentsPerPage);

// Calcoliamo il tempo trascorso dalla pubblicazione del post
$timestamp = strtotime($post['created_at']);
$timeAgo = timeAgo($timestamp);
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Post principale -->
            <div class="card mb-4">
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
                    <?php if ($userId && ($post['user_id'] == $userId || isAdmin())): ?>
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
                            <?php if ($userId): ?>
                                <button class="btn btn-sm <?php echo $hasLiked ? 'btn-primary' : 'btn-outline-primary'; ?> like-button" data-post-id="<?php echo $post['id']; ?>">
                                    <i class="fas fa-thumbs-up"></i> Mi piace 
                                    <span class="likes-count"><?php echo $post['likes_count']; ?></span>
                                </button>
                            <?php else: ?>
                                <span class="btn btn-sm btn-outline-primary disabled">
                                    <i class="fas fa-thumbs-up"></i> Mi piace 
                                    <span class="likes-count"><?php echo $post['likes_count']; ?></span>
                                </span>
                            <?php endif; ?>
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
                    <h5 class="mb-3"><?php echo $totalComments; ?> commenti</h5>
                    
                    <?php if ($userId): ?>
                        <!-- Form per aggiungere un commento -->
                        <div class="d-flex mb-4">
                            <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo getCurrentUser()['profile_pic']; ?>" alt="<?php echo getCurrentUser()['username']; ?>" class="rounded-circle me-2" width="40" height="40">
                            <div class="flex-grow-1">
                                <form action="<?php echo SITE_URL; ?>/actions/add_comment.php" method="POST" class="comment-form">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <div class="mb-2">
                                        <textarea class="form-control" name="content" rows="2" placeholder="Scrivi un commento..." required></textarea>
                                    </div>
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Commenta
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Lista commenti -->
                    <div class="comments-list">
                        <?php if (count($comments) > 0): ?>
                            <?php foreach ($comments as $comment): ?>
                                <?php $commentTimeAgo = timeAgo(strtotime($comment['created_at'])); ?>
                                <div class="d-flex mb-3 comment">
                                    <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $comment['profile_pic']; ?>" alt="<?php echo $comment['username']; ?>" class="rounded-circle me-2" width="40" height="40">
                                    <div class="flex-grow-1">
                                        <div class="comment-content p-3 bg-light rounded">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div>
                                                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $comment['user_id']; ?>" class="text-decoration-none text-dark fw-bold">
                                                        <?php echo $comment['full_name']; ?>
                                                    </a>
                                                    <small class="text-muted">
                                                        @<?php echo $comment['username']; ?>
                                                    </small>
                                                </div>
                                                <small class="text-muted" title="<?php echo formatDate($comment['created_at']); ?>">
                                                    <?php echo $commentTimeAgo; ?>
                                                </small>
                                            </div>
                                            <p class="mb-0"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                                        </div>
                                        
                                        <?php if ($userId && ($comment['user_id'] == $userId || isAdmin())): ?>
                                            <div class="comment-actions mt-1">
                                                <a href="#" class="text-danger delete-comment" data-comment-id="<?php echo $comment['id']; ?>">
                                                    <i class="fas fa-trash-alt"></i> Elimina
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <!-- Paginazione -->
                            <?php if ($totalPages > 1): ?>
                                <nav aria-label="Navigazione commenti" class="mt-4">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $postId; ?>&page=<?php echo $page - 1; ?>">
                                                    Precedente
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                                <a class="page-link" href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $postId; ?>&page=<?php echo $i; ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $postId; ?>&page=<?php echo $page + 1; ?>">
                                                    Successiva
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted mb-0">Nessun commento ancora. Sii il primo a commentare!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestione like
    $('.like-button').click(function() {
        var postId = $(this).data('post-id');
        var button = $(this);
        
        $.ajax({
            url: SITE_URL + '/api/toggle_like.php',
            type: 'POST',
            data: { post_id: postId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Aggiorniamo l'aspetto del pulsante
                    if (response.liked) {
                        button.removeClass('btn-outline-primary').addClass('btn-primary');
                    } else {
                        button.removeClass('btn-primary').addClass('btn-outline-primary');
                    }
                    
                    // Aggiorniamo il conteggio dei like
                    button.find('.likes-count').text(response.likes_count);
                }
            }
        });
    });
    
    // Gestione condivisione
    $('.share-button').click(function() {
        var postId = $(this).data('post-id');
        var postUrl = SITE_URL + '/post.php?id=' + postId;
        
        // Creiamo un elemento input temporaneo
        var tempInput = document.createElement('input');
        tempInput.value = postUrl;
        document.body.appendChild(tempInput);
        
        // Selezioniamo e copiamo il testo
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        
        // Avvisiamo l'utente
        alert('Link copiato negli appunti: ' + postUrl);
    });
    
    // Gestione eliminazione post
    $('.delete-post').click(function(e) {
        e.preventDefault();
        
        if (confirm('Sei sicuro di voler eliminare questo post? Questa azione non può essere annullata.')) {
            var postId = $(this).data('post-id');
            
            $.ajax({
                url: SITE_URL + '/api/delete_post.php',
                type: 'POST',
                data: { post_id: postId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Reindirizzamento alla home
                        window.location.href = SITE_URL;
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });
    
    // Gestione eliminazione commento
    $('.delete-comment').click(function(e) {
        e.preventDefault();
        
        if (confirm('Sei sicuro di voler eliminare questo commento? Questa azione non può essere annullata.')) {
            var commentId = $(this).data('comment-id');
            var commentElement = $(this).closest('.comment');
            
            $.ajax({
                url: SITE_URL + '/api/delete_comment.php',
                type: 'POST',
                data: { comment_id: commentId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Rimuoviamo il commento dalla pagina
                        commentElement.fadeOut(function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
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
?>
