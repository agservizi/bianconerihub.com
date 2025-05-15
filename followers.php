<?php
/**
 * Pagina per visualizzare i follower di un utente
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

// Otteniamo i dettagli dell'utente
$userQuery = "SELECT id, username, full_name, profile_pic FROM users WHERE id = {$profileId}";
$userResult = $conn->query($userQuery);

// Se l'utente non esiste, reindirizzamento alla home
if (!$userResult || $userResult->num_rows === 0) {
    setFlashMessage('Utente non trovato', 'error');
    redirect(SITE_URL);
}

$user = $userResult->fetch_assoc();

// Otteniamo i follower dell'utente
$followersQuery = "SELECT u.id, u.username, u.full_name, u.profile_pic, u.bio, f.created_at 
                  FROM users u 
                  JOIN friendships f ON u.id = f.follower_id 
                  WHERE f.following_id = {$profileId} AND f.status = 'accepted' 
                  ORDER BY f.created_at DESC";

$followersResult = $conn->query($followersQuery);
$followers = [];

if ($followersResult && $followersResult->num_rows > 0) {
    while ($follower = $followersResult->fetch_assoc()) {
        $followers[] = $follower;
    }
}

// Otteniamo il conteggio totale
$totalFollowers = count($followers);

// Verifichiamo le relazioni se l'utente è loggato
$currentUserId = isLoggedIn() ? $_SESSION['user_id'] : 0;
$relationships = [];

if ($currentUserId > 0 && $currentUserId != $profileId) {
    $ids = array_column($followers, 'id');
    $ids = array_filter($ids, function($id) use ($currentUserId) {
        return $id != $currentUserId;
    });
    
    if (!empty($ids)) {
        $idsString = implode(',', $ids);
        $relationshipQuery = "SELECT following_id FROM friendships 
                             WHERE follower_id = {$currentUserId} 
                             AND following_id IN ({$idsString}) 
                             AND status = 'accepted'";
        
        $relationshipResult = $conn->query($relationshipQuery);
        
        if ($relationshipResult && $relationshipResult->num_rows > 0) {
            while ($relationship = $relationshipResult->fetch_assoc()) {
                $relationships[$relationship['following_id']] = true;
            }
        }
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users"></i> Follower di <?php echo $user['full_name']; ?>
                        <?php if ($totalFollowers > 0): ?>
                            <span class="badge bg-light text-primary float-end"><?php echo $totalFollowers; ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if ($totalFollowers > 0): ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($followers as $follower): ?>
                                <?php
                                // Verifichiamo se l'utente corrente segue già questo follower
                                $isFollowing = false;
                                if ($currentUserId > 0) {
                                    if ($currentUserId == $follower['id']) {
                                        $isFollowing = true; // Sei tu stesso
                                    } else {
                                        $isFollowing = isset($relationships[$follower['id']]);
                                    }
                                }
                                
                                // Calcoliamo il tempo da cui segue
                                $followingSince = timeAgo(strtotime($follower['created_at']));
                                ?>
                                <li class="list-group-item">
                                    <div class="d-flex align-items-center">
                                        <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $follower['id']; ?>">
                                            <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $follower['profile_pic']; ?>" alt="<?php echo $follower['username']; ?>" class="rounded-circle me-3" width="60" height="60">
                                        </a>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $follower['id']; ?>" class="text-decoration-none">
                                                        <h5 class="mb-0"><?php echo $follower['full_name']; ?></h5>
                                                    </a>
                                                    <div class="text-muted">@<?php echo $follower['username']; ?></div>
                                                    <small class="text-muted">Segue da <?php echo $followingSince; ?></small>
                                                </div>
                                                <?php if ($currentUserId > 0 && $currentUserId != $follower['id']): ?>
                                                    <div>
                                                        <button class="btn btn-sm <?php echo $isFollowing ? 'btn-secondary' : 'btn-primary'; ?> follow-button" data-user-id="<?php echo $follower['id']; ?>">
                                                            <?php if ($isFollowing): ?>
                                                                <i class="fas fa-user-check"></i> Segui già
                                                            <?php else: ?>
                                                                <i class="fas fa-user-plus"></i> Segui
                                                            <?php endif; ?>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($follower['bio'])): ?>
                                                <p class="mb-0 mt-2"><?php echo truncateText(htmlspecialchars($follower['bio']), 100); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="p-4 text-center">
                            <i class="far fa-sad-tear fa-4x text-muted mb-3"></i>
                            <h5>Nessun follower</h5>
                            <p class="text-muted mb-0">
                                <?php if ($profileId == $currentUserId): ?>
                                    Non hai ancora follower. Interagisci con altri utenti per farti conoscere!
                                <?php else: ?>
                                    <?php echo $user['full_name']; ?> non ha ancora follower.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white text-center">
                    <a href="<?php echo SITE_URL; ?>/profile.php?id=<?php echo $profileId; ?>" class="btn btn-primary">
                        <i class="fas fa-user"></i> Torna al profilo
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script per la gestione dei follow/unfollow -->
<script>
document.addEventListener('DOMContentLoaded', function() {
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
?>
