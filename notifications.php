<?php
/**
 * Pagina delle notifiche
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per visualizzare le notifiche', 'error');
    redirect(SITE_URL . '/login.php');
}

$userId = $_SESSION['user_id'];

// Segniamo tutte le notifiche come lette
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = {$userId} AND is_read = 0");

// Otteniamo le notifiche dell'utente
$notificationsQuery = "SELECT n.*, u.username, u.profile_pic 
                      FROM notifications n 
                      JOIN users u ON n.sender_id = u.id 
                      WHERE n.user_id = {$userId} 
                      ORDER BY n.created_at DESC 
                      LIMIT 50";

$notificationsResult = $conn->query($notificationsQuery);
$notifications = [];

if ($notificationsResult && $notificationsResult->num_rows > 0) {
    while ($notification = $notificationsResult->fetch_assoc()) {
        $notifications[] = $notification;
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell"></i> Notifiche
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (count($notifications) > 0): ?>
                        <ul class="list-group list-group-flush notifications-list">
                            <?php foreach ($notifications as $notification): ?>
                                <?php
                                // Determiniamo l'URL di destinazione in base al tipo di notifica
                                $url = SITE_URL;
                                
                                switch ($notification['type']) {
                                    case 'like':
                                    case 'comment':
                                    case 'mention':
                                        if (!empty($notification['reference_id'])) {
                                            $url = SITE_URL . '/post.php?id=' . $notification['reference_id'];
                                        }
                                        break;
                                    case 'follow':
                                        $url = SITE_URL . '/profile.php?id=' . $notification['sender_id'];
                                        break;
                                    case 'system':
                                        // Per notifiche di messaggi, reindirizzamento alla chat
                                        if (strpos($notification['content'], 'messaggio') !== false) {
                                            $url = SITE_URL . '/messages.php?user=' . $notification['sender_id'];
                                        }
                                        break;
                                }
                                
                                // Calcoliamo il tempo trascorso
                                $timestamp = strtotime($notification['created_at']);
                                $timeAgo = timeAgo($timestamp);
                                ?>
                                
                                <a href="<?php echo $url; ?>" class="list-group-item list-group-item-action">
                                    <div class="d-flex align-items-center">
                                        <img src="<?php echo UPLOADS_URL; ?>/profile_pics/<?php echo $notification['profile_pic']; ?>" alt="<?php echo $notification['username']; ?>" class="rounded-circle me-3" width="50" height="50">
                                        <div class="flex-grow-1">
                                            <p class="mb-1"><?php echo $notification['content']; ?></p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted"><?php echo $timeAgo; ?></small>
                                                <?php if ($notification['is_read'] == 0): ?>
                                                    <span class="badge bg-primary">Nuova</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="p-4 text-center">
                            <i class="far fa-bell-slash fa-4x text-muted mb-3"></i>
                            <h5>Nessuna notifica</h5>
                            <p class="text-muted">Non hai ancora notifiche. Inizia a interagire con la community!</p>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (count($notifications) > 0): ?>
                    <div class="card-footer text-center">
                        <form action="<?php echo SITE_URL; ?>/actions/clear_notifications.php" method="POST">
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash"></i> Cancella tutte le notifiche
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
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
?>
