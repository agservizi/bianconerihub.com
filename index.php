<?php
/**
 * Pagina principale
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Titolo della pagina
$pageTitle = "Home";

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    // Se non è autenticato, mostriamo la landing page
    include_once 'includes/landing.php';
} else {
    // Se è autenticato, mostriamo il feed
?>
<div class="row mt-4">
    <!-- Colonna sinistra - Profilo e menu -->
    <div class="col-lg-3">
        <div class="card mb-3">
            <div class="card-body text-center">
                <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $currentUser['profile_pic']; ?>" alt="<?php echo $currentUser['username']; ?>" class="rounded-circle img-thumbnail mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                <h5><?php echo $currentUser['full_name']; ?></h5>
                <p class="text-muted">@<?php echo $currentUser['username']; ?></p>
                <div class="d-grid gap-2">
                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $currentUser['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-user"></i> Visualizza profilo
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-bars"></i> Menu
            </div>
            <div class="list-group list-group-flush">
                <a href="<?php echo SITE_URL; ?>" class="list-group-item list-group-item-action active">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $currentUser['id']; ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-user"></i> Profilo
                </a>
                <a href="<?php echo SITE_URL; ?>/friends.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-users"></i> Amici
                </a>
                <a href="<?php echo SITE_URL; ?>/messages.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-envelope"></i> Messaggi
                    <?php if ($messagesCount > 0): ?>
                        <span class="badge bg-danger float-end"><?php echo $messagesCount; ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?php echo SITE_URL; ?>/events.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-calendar-alt"></i> Eventi
                </a>
                <a href="<?php echo SITE_URL; ?>/news.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-newspaper"></i> News
                </a>
                <a href="<?php echo SITE_URL; ?>/gallery.php" class="list-group-item list-group-item-action">
                    <i class="fas fa-images"></i> Galleria
                </a>
            </div>
        </div>
        
        <!-- Prossimi eventi -->
        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-calendar-alt"></i> Prossime partite
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Juventus - Milan</strong>
                                <div class="small text-muted">20 Maggio 2025 - 20:45</div>
                            </div>
                            <span class="badge bg-primary">Serie A</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Roma - Juventus</strong>
                                <div class="small text-muted">24 Maggio 2025 - 18:00</div>
                            </div>
                            <span class="badge bg-primary">Serie A</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Juventus - Napoli</strong>
                                <div class="small text-muted">28 Maggio 2025 - 20:45</div>
                            </div>
                            <span class="badge bg-primary">Serie A</span>
                        </div>
                    </li>
                </ul>
                <div class="card-footer text-center">
                    <a href="<?php echo SITE_URL; ?>/fixtures.php" class="text-decoration-none">Vedi tutte le partite</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Colonna centrale - Feed -->
    <div class="col-lg-6">
        <!-- Form per creare un nuovo post -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="<?php echo SITE_URL; ?>/actions/create_post.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <textarea class="form-control" name="content" rows="3" placeholder="Cosa stai pensando, <?php echo $currentUser['username']; ?>?"></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="btnAddPhoto">
                                <i class="fas fa-image"></i> Foto
                            </button>
                            <input type="file" name="media" id="mediaInput" class="d-none" accept="image/*">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btnAddEmoji">
                                <i class="far fa-smile"></i> Emoji
                            </button>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Pubblica
                        </button>
                    </div>
                    <div id="mediaPreview" class="mt-3 d-none">
                        <div class="position-relative">
                            <img src="" alt="Preview" class="img-fluid rounded">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" id="btnRemoveMedia">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Feed dei post -->
        <div id="postsFeed">
            <?php
            // Query per ottenere i post più recenti degli amici e dell'utente corrente
            $userId = $currentUser['id'];
            $query = "SELECT p.*, u.username, u.full_name, u.profile_pic 
                      FROM posts p 
                      JOIN users u ON p.user_id = u.id 
                      WHERE p.user_id = {$userId} 
                      OR p.user_id IN (
                          SELECT following_id FROM friendships 
                          WHERE follower_id = {$userId} AND status = 'accepted'
                      ) 
                      ORDER BY p.created_at DESC 
                      LIMIT " . POSTS_PER_PAGE;
            
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
                                <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $currentUser['profile_pic']; ?>" alt="<?php echo $currentUser['username']; ?>" class="rounded-circle me-2" width="32" height="32">
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
            } else {
            ?>
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <p class="mb-0">Non ci sono post da visualizzare. Inizia a seguire alcuni utenti o pubblica il tuo primo post!</p>
                    </div>
                </div>
            <?php
            }
            ?>
            
            <div class="text-center mb-4">
                <button id="loadMorePosts" class="btn btn-outline-primary">
                    <i class="fas fa-sync"></i> Carica altri post
                </button>
            </div>
        </div>
    </div>
    
    <!-- Colonna destra - Suggerimenti e notizie -->
    <div class="col-lg-3">
        <!-- Ultimi risultati -->
        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-futbol"></i> Ultimi risultati
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Juventus</span>
                            <span class="fw-bold">3 - 1</span>
                            <span>Inter</span>
                        </div>
                        <div class="small text-muted text-center">10 Maggio 2025</div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Atalanta</span>
                            <span class="fw-bold">0 - 2</span>
                            <span>Juventus</span>
                        </div>
                        <div class="small text-muted text-center">5 Maggio 2025</div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Juventus</span>
                            <span class="fw-bold">4 - 0</span>
                            <span>Torino</span>
                        </div>
                        <div class="small text-muted text-center">30 Aprile 2025</div>
                    </li>
                </ul>
                <div class="card-footer text-center">
                    <a href="<?php echo SITE_URL; ?>/results.php" class="text-decoration-none">Vedi tutti i risultati</a>
                </div>
            </div>
        </div>
        
        <!-- Suggerimenti amici -->
        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-user-plus"></i> Tifosi da seguire
            </div>
            <div class="card-body p-0">
                <?php
                // Query per ottenere utenti suggeriti
                $suggestedQuery = "SELECT u.id, u.username, u.full_name, u.profile_pic 
                                  FROM users u 
                                  WHERE u.id != {$userId} 
                                  AND u.id NOT IN (
                                      SELECT following_id FROM friendships 
                                      WHERE follower_id = {$userId}
                                  ) 
                                  LIMIT 5";
                
                $suggestedResult = $conn->query($suggestedQuery);
                
                if ($suggestedResult && $suggestedResult->num_rows > 0):
                ?>
                    <ul class="list-group list-group-flush">
                    <?php while ($user = $suggestedResult->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $user['profile_pic']; ?>" alt="<?php echo $user['username']; ?>" class="rounded-circle me-2" width="40" height="40">
                                    <div>
                                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $user['id']; ?>" class="text-decoration-none text-dark">
                                            <?php echo $user['full_name']; ?>
                                        </a>
                                        <div class="text-muted small">@<?php echo $user['username']; ?></div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary follow-button" data-user-id="<?php echo $user['id']; ?>">
                                    <i class="fas fa-user-plus"></i> Segui
                                </button>
                            </div>
                        </li>
                    <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="card-body text-center">
                        <p class="mb-0">Nessun suggerimento disponibile al momento.</p>
                    </div>
                <?php endif; ?>
                <div class="card-footer text-center">
                    <a href="<?php echo SITE_URL; ?>/find_friends.php" class="text-decoration-none">Trova altri tifosi</a>
                </div>
            </div>
        </div>
        
        <!-- Ultime notizie -->
        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-newspaper"></i> Ultime notizie
            </div>
            <div class="card-body p-0">
                <?php
                // Query per ottenere le ultime notizie
                $newsQuery = "SELECT n.id, n.title, n.image, n.created_at, u.username 
                             FROM news n 
                             JOIN users u ON n.author_id = u.id 
                             ORDER BY n.created_at DESC 
                             LIMIT 3";
                
                $newsResult = $conn->query($newsQuery);
                
                if ($newsResult && $newsResult->num_rows > 0):
                ?>
                    <ul class="list-group list-group-flush">
                    <?php while ($news = $newsResult->fetch_assoc()): ?>
                        <li class="list-group-item">
                            <div class="d-flex">
                                <?php if ($news['image']): ?>
                                    <img src="<?php echo UPLOADS_URL; ?>/news/<?php echo $news['image']; ?>" alt="<?php echo $news['title']; ?>" class="me-2 rounded" style="width: 80px; height: 60px; object-fit: cover;">
                                <?php endif; ?>
                                <div>
                                    <a href="<?php echo SITE_URL; ?>/news_article.php?id=<?php echo $news['id']; ?>" class="text-decoration-none text-dark">
                                        <?php echo $news['title']; ?>
                                    </a>
                                    <div class="text-muted small">
                                        <span>By @<?php echo $news['username']; ?></span> · 
                                        <span><?php echo timeAgo(strtotime($news['created_at'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="card-body text-center">
                        <p class="mb-0">Nessuna notizia disponibile al momento.</p>
                    </div>
                <?php endif; ?>
                <div class="card-footer text-center">
                    <a href="<?php echo SITE_URL; ?>/news.php" class="text-decoration-none">Leggi tutte le notizie</a>
                </div>
            </div>
        </div>
        
        <!-- Classifica Serie A -->
        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-trophy"></i> Classifica Serie A
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary me-2">1</span>
                                <span class="fw-bold">Juventus</span>
                            </div>
                            <span>88 pt</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">2</span>
                                <span>Inter</span>
                            </div>
                            <span>85 pt</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">3</span>
                                <span>Milan</span>
                            </div>
                            <span>79 pt</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">4</span>
                                <span>Napoli</span>
                            </div>
                            <span>72 pt</span>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-secondary me-2">5</span>
                                <span>Atalanta</span>
                            </div>
                            <span>65 pt</span>
                        </div>
                    </li>
                </ul>
                <div class="card-footer text-center">
                    <a href="<?php echo SITE_URL; ?>/standings.php" class="text-decoration-none">Visualizza classifica completa</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    // JavaScript aggiuntivo per la pagina
    $extraJs = <<<EOT
<script>
$(document).ready(function() {
    // Preview dell'immagine caricata
    $('#btnAddPhoto').click(function() {
        $('#mediaInput').click();
    });
    
    $('#mediaInput').change(function() {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#mediaPreview img').attr('src', e.target.result);
                $('#mediaPreview').removeClass('d-none');
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    $('#btnRemoveMedia').click(function() {
        $('#mediaInput').val('');
        $('#mediaPreview').addClass('d-none');
    });
    
    // Like dei post
    $('.like-button').click(function() {
        var button = $(this);
        var postId = button.data('post-id');
        
        $.ajax({
            url: SITE_URL + '/api/toggle_like.php',
            type: 'POST',
            data: { post_id: postId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Aggiorniamo il contatore dei like
                    button.find('.likes-count').text(response.likes_count);
                    
                    // Aggiorniamo lo stile del pulsante
                    if (response.has_liked) {
                        button.removeClass('btn-outline-primary').addClass('btn-primary');
                    } else {
                        button.removeClass('btn-primary').addClass('btn-outline-primary');
                    }
                }
            }
        });
    });
    
    // Seguire un utente
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
                        button.html('<i class="fas fa-user-check"></i> Seguo');
                    } else {
                        button.html('<i class="fas fa-user-plus"></i> Segui');
                    }
                    
                    // Dopo un po' rimuoviamo l'utente suggerito
                    if (response.is_following) {
                        setTimeout(function() {
                            button.closest('li').fadeOut(function() {
                                $(this).remove();
                            });
                        }, 2000);
                    }
                }
            }
        });
    });
    
    // Caricamento di più post
    var page = 1;
    
    $('#loadMorePosts').click(function() {
        var button = $(this);
        page++;
        
        button.html('<i class="fas fa-spinner fa-spin"></i> Caricamento...');
        
        $.ajax({
            url: SITE_URL + '/api/load_more_posts.php',
            type: 'GET',
            data: { page: page },
            dataType: 'html',
            success: function(response) {
                if (response.trim() !== '') {
                    // Inseriamo i nuovi post prima del pulsante di caricamento
                    $(response).insertBefore(button.parent());
                    button.html('<i class="fas fa-sync"></i> Carica altri post');
                } else {
                    // Non ci sono più post da caricare
                    button.html('Non ci sono altri post da caricare').prop('disabled', true);
                }
            }
        });
    });
    
    // Eliminazione dei post
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
                        // Rimuoviamo il post dalla pagina
                        $('.post[data-post-id="' + postId + '"]').fadeOut(function() {
                            $(this).remove();
                        });
                    }
                }
            });
        }
    });
    
    // Invio commenti tramite AJAX
    $('.comment-form').submit(function(e) {
        e.preventDefault();
        
        var form = $(this);
        var postId = form.find('input[name="post_id"]').val();
        var content = form.find('input[name="content"]').val();
        
        if (content.trim() === '') {
            return;
        }
        
        $.ajax({
            url: SITE_URL + '/api/add_comment.php',
            type: 'POST',
            data: { post_id: postId, content: content },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Aggiornamento del contatore dei commenti
                    var commentsCount = $('.post[data-post-id="' + postId + '"] .comments-count');
                    commentsCount.text(parseInt(commentsCount.text()) + 1);
                    
                    // Aggiungiamo il nuovo commento
                    var newComment = 
                        '<div class="d-flex mb-3 comment">' +
                            '<img src="' + UPLOADS_URL + '/profile_pics/' + response.profile_pic + '" alt="' + response.username + '" class="rounded-circle me-2" width="32" height="32">' +
                            '<div class="comment-content p-2 bg-light rounded">' +
                                '<div class="d-flex justify-content-between align-items-center">' +
                                    '<a href="' + SITE_URL + '/profile.php?id=' + response.user_id + '" class="text-decoration-none text-dark fw-bold">@' + response.username + '</a>' +
                                    '<small class="text-muted">Ora</small>' +
                                '</div>' +
                                '<p class="mb-0">' + response.content + '</p>' +
                            '</div>' +
                        '</div>';
                    
                    var commentsSection = form.closest('.comments-section');
                    $(newComment).insertBefore(commentsSection.find('.comment-form').closest('.d-flex'));
                    
                    // Resettiamo il form
                    form.find('input[name="content"]').val('');
                }
            }
        });
    });
});
</script>
EOT;
}

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
