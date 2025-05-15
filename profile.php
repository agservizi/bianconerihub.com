<?php
/**
 * Pagina del profilo utente
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se è stato specificato l'ID dell'utente
$profileId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Se non è stato specificato un ID, usiamo quello dell'utente corrente
if ($profileId === 0 && isLoggedIn()) {
    $profileId = $_SESSION['user_id'];
} else if ($profileId === 0) {
    // Se l'utente non è loggato e non è stato specificato un ID, reindirizzamento alla home
    redirect(SITE_URL);
}

// Otteniamo i dati dell'utente
$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as posts_count,
          (SELECT COUNT(*) FROM friendships WHERE following_id = u.id AND status = 'accepted') as followers_count,
          (SELECT COUNT(*) FROM friendships WHERE follower_id = u.id AND status = 'accepted') as following_count
          FROM users u WHERE u.id = {$profileId}";

$result = $conn->query($query);

// Se l'utente non esiste, reindirizzamento alla home
if ($result->num_rows === 0) {
    setFlashMessage('Utente non trovato', 'error');
    redirect(SITE_URL);
}

$profileUser = $result->fetch_assoc();

// Verifichiamo se l'utente corrente segue il profilo visualizzato
$isFollowing = false;
if (isLoggedIn() && $profileId != $_SESSION['user_id']) {
    $followCheck = $conn->query("SELECT id FROM friendships WHERE follower_id = {$_SESSION['user_id']} AND following_id = {$profileId} AND status = 'accepted'");
    $isFollowing = ($followCheck->num_rows > 0);
}

// Impostiamo il titolo della pagina
$pageTitle = $profileUser['full_name'] . ' (@' . $profileUser['username'] . ')';

// Otteniamo gli ultimi post dell'utente
$postsQuery = "SELECT p.*, 
              (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
              (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comments_count
              FROM posts p 
              WHERE p.user_id = {$profileId} 
              ORDER BY p.created_at DESC 
              LIMIT 10";

$postsResult = $conn->query($postsQuery);
?>

<!-- Intestazione del profilo -->
<div class="profile-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-3 text-center">
                <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $profileUser['profile_pic']; ?>" alt="<?php echo $profileUser['username']; ?>" class="img-fluid rounded-circle profile-pic" width="150" height="150">
            </div>
            <div class="col-md-6">
                <h1><?php echo $profileUser['full_name']; ?></h1>
                <p class="text-muted">@<?php echo $profileUser['username']; ?></p>
                
                <?php if (!empty($profileUser['bio'])): ?>
                    <p><?php echo nl2br(htmlspecialchars($profileUser['bio'])); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($profileUser['location'])): ?>
                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($profileUser['location']); ?></p>
                <?php endif; ?>
                
                <p><i class="fas fa-calendar-alt"></i> Membro dal <?php echo formatDate($profileUser['registration_date'], 'd/m/Y'); ?></p>
            </div>
            <div class="col-md-3 text-center">
                <?php if (isLoggedIn() && $profileId != $_SESSION['user_id']): ?>
                    <button class="btn btn-lg <?php echo $isFollowing ? 'btn-secondary' : 'btn-primary'; ?> w-100 mb-2 follow-button" data-user-id="<?php echo $profileId; ?>">
                        <?php if ($isFollowing): ?>
                            <i class="fas fa-user-check"></i> Segui già
                        <?php else: ?>
                            <i class="fas fa-user-plus"></i> Segui
                        <?php endif; ?>
                    </button>
                    
                    <a href="<?php echo SITE_URL; ?>/messages.php?user=<?php echo $profileId; ?>" class="btn btn-outline-primary w-100 mb-2">
                        <i class="fas fa-envelope"></i> Messaggio
                    </a>
                <?php endif; ?>
                
                <?php if (isLoggedIn() && ($profileId == $_SESSION['user_id'] || isAdmin())): ?>
                    <a href="<?php echo SITE_URL; ?>/edit_profile.php<?php echo $profileId != $_SESSION['user_id'] ? '?id=' . $profileId : ''; ?>" class="btn btn-outline-light w-100">
                        <i class="fas fa-edit"></i> <?php echo $profileId == $_SESSION['user_id'] ? 'Modifica profilo' : 'Modifica utente'; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Statistiche profilo -->
        <div class="profile-stats">
            <div class="profile-stat">
                <div class="profile-stat-count"><?php echo $profileUser['posts_count']; ?></div>
                <div class="profile-stat-label">Post</div>
            </div>
            <div class="profile-stat">
                <a href="<?php echo SITE_URL; ?>/followers.php?id=<?php echo $profileId; ?>" class="text-white text-decoration-none">
                    <div class="profile-stat-count"><?php echo $profileUser['followers_count']; ?></div>
                    <div class="profile-stat-label">Follower</div>
                </a>
            </div>
            <div class="profile-stat">
                <a href="<?php echo SITE_URL; ?>/following.php?id=<?php echo $profileId; ?>" class="text-white text-decoration-none">
                    <div class="profile-stat-count"><?php echo $profileUser['following_count']; ?></div>
                    <div class="profile-stat-label">Seguiti</div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Tabs di navigazione -->
    <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab" aria-controls="posts" aria-selected="true">
                <i class="fas fa-th-large"></i> Post
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" type="button" role="tab" aria-controls="about" aria-selected="false">
                <i class="fas fa-user"></i> Informazioni
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="photos-tab" data-bs-toggle="tab" data-bs-target="#photos" type="button" role="tab" aria-controls="photos" aria-selected="false">
                <i class="fas fa-images"></i> Foto
            </button>
        </li>
    </ul>
    
    <!-- Contenuto tabs -->
    <div class="tab-content" id="profileTabsContent">
        <!-- Tab Post -->
        <div class="tab-pane fade show active" id="posts" role="tabpanel" aria-labelledby="posts-tab">
            <?php if (isLoggedIn() && $profileId == $_SESSION['user_id']): ?>
                <!-- Form per creare un nuovo post -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form action="<?php echo SITE_URL; ?>/actions/create_post.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <textarea class="form-control" name="content" rows="3" placeholder="Cosa stai pensando, <?php echo getCurrentUser()['username']; ?>?"></textarea>
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
            <?php endif; ?>
            
            <!-- Post dell'utente -->
            <div id="userPosts">
                <?php if ($postsResult && $postsResult->num_rows > 0): ?>
                    <?php while ($post = $postsResult->fetch_assoc()): ?>
                        <!-- Calcoliamo il tempo trascorso -->
                        <?php
                        $timestamp = strtotime($post['created_at']);
                        $timeAgo = timeAgo($timestamp);
                        
                        // Verifichiamo se l'utente corrente ha già messo like
                        $hasLiked = false;
                        if (isLoggedIn()) {
                            $likeCheck = $conn->query("SELECT id FROM likes WHERE post_id = {$post['id']} AND user_id = {$_SESSION['user_id']}");
                            $hasLiked = ($likeCheck->num_rows > 0);
                        }
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
                                <?php if (isLoggedIn() && ($profileUser['id'] == $_SESSION['user_id'] || isAdmin())): ?>
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
                                        <?php if (isLoggedIn()): ?>
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
                    <?php endwhile; ?>
                    
                    <!-- Pulsante per caricare altri post -->
                    <div class="text-center mb-4">
                        <button id="loadMoreUserPosts" class="btn btn-outline-primary" data-page="2" data-user-id="<?php echo $profileId; ?>">
                            <i class="fas fa-sync"></i> Carica altri post
                        </button>
                    </div>
                <?php else: ?>
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <p class="mb-0">Nessun post da visualizzare.</p>
                            <?php if (isLoggedIn() && $profileId == $_SESSION['user_id']): ?>
                                <p class="mt-3">Inizia a condividere la tua passione per la Juventus creando il tuo primo post!</p>
                                <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#newPostForm">
                                    <i class="fas fa-plus"></i> Crea il tuo primo post
                                </button>
                                
                                <div class="collapse mt-4" id="newPostForm">
                                    <div class="card">
                                        <div class="card-body">
                                            <form action="<?php echo SITE_URL; ?>/actions/create_post.php" method="POST" enctype="multipart/form-data">
                                                <div class="mb-3">
                                                    <textarea class="form-control" name="content" rows="3" placeholder="Condividi i tuoi pensieri sulla Juventus..."></textarea>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" id="btnAddPhoto">
                                                            <i class="fas fa-image"></i> Foto
                                                        </button>
                                                        <input type="file" name="media" id="mediaInput" class="d-none" accept="image/*">
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
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Tab Informazioni -->
        <div class="tab-pane fade" id="about" role="tabpanel" aria-labelledby="about-tab">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Informazioni su <?php echo $profileUser['full_name']; ?></h4>
                    
                    <div class="mb-4">
                        <h5><i class="fas fa-info-circle"></i> Bio</h5>
                        <?php if (!empty($profileUser['bio'])): ?>
                            <p><?php echo nl2br(htmlspecialchars($profileUser['bio'])); ?></p>
                        <?php else: ?>
                            <p class="text-muted">Nessuna biografia disponibile</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <h5><i class="fas fa-map-marker-alt"></i> Posizione</h5>
                        <?php if (!empty($profileUser['location'])): ?>
                            <p><?php echo htmlspecialchars($profileUser['location']); ?></p>
                        <?php else: ?>
                            <p class="text-muted">Nessuna posizione specificata</p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="mb-4">
                        <h5><i class="fas fa-calendar-alt"></i> Membro dal</h5>
                        <p><?php echo formatDate($profileUser['registration_date'], 'd F Y'); ?></p>
                    </div>
                    
                    <div>
                        <h5><i class="fas fa-clock"></i> Ultima attività</h5>
                        <?php if (!empty($profileUser['last_login'])): ?>
                            <p>Ultimo accesso: <?php echo formatDate($profileUser['last_login']); ?></p>
                        <?php else: ?>
                            <p class="text-muted">Nessuna attività recente</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab Foto -->
        <div class="tab-pane fade" id="photos" role="tabpanel" aria-labelledby="photos-tab">
            <?php
            // Otteniamo i post con immagini
            $photosQuery = "SELECT id, media, created_at FROM posts WHERE user_id = {$profileId} AND media IS NOT NULL ORDER BY created_at DESC";
            $photosResult = $conn->query($photosQuery);
            ?>
            
            <?php if ($photosResult && $photosResult->num_rows > 0): ?>
                <div class="row">
                    <?php while ($photo = $photosResult->fetch_assoc()): ?>
                        <div class="col-md-4 col-sm-6 mb-4">
                            <div class="card h-100">
                                <a href="<?php echo SITE_URL; ?>/post.php?id=<?php echo $photo['id']; ?>" class="text-decoration-none">
                                    <img src="<?php echo UPLOADS_URL; ?>/posts/<?php echo $photo['media']; ?>" alt="Photo" class="card-img-top" style="height: 200px; object-fit: cover;">
                                </a>
                                <div class="card-footer text-muted small">
                                    <?php echo formatDate($photo['created_at']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <p class="mb-0">Nessuna foto da visualizzare.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// JavaScript aggiuntivo per questa pagina
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
    
    // Caricamento più post dell'utente
    $('#loadMoreUserPosts').click(function() {
        var button = $(this);
        var page = button.data('page');
        var userId = button.data('user-id');
        
        button.html('<i class="fas fa-spinner fa-spin"></i> Caricamento...');
        
        $.ajax({
            url: SITE_URL + '/api/load_user_posts.php',
            type: 'GET',
            data: {
                user_id: userId,
                page: page
            },
            success: function(response) {
                if (response.trim() !== '') {
                    // Aggiungiamo i nuovi post
                    $('#userPosts').append(response);
                    button.data('page', page + 1);
                    button.html('<i class="fas fa-sync"></i> Carica altri post');
                } else {
                    // Non ci sono più post
                    button.html('Non ci sono altri post').prop('disabled', true);
                }
            }
        });
    });
    
    // Eliminazione post
    $(document).on('click', '.delete-post', function(e) {
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
                    } else {
                        alert(response.message);
                    }
                }
            });
        }
    });
});
</script>
EOT;

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
