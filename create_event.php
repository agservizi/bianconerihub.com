<?php
/**
 * Pagina per creare un nuovo evento
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Includiamo l'header
require_once 'includes/header.php';

// Verifichiamo se l'utente è autenticato
if (!isLoggedIn()) {
    setFlashMessage('Devi effettuare l\'accesso per creare un evento', 'error');
    redirect(SITE_URL . '/login.php');
}

// Definiamo le categorie disponibili
$categories = [
    'match' => 'Partita',
    'meeting' => 'Incontro',
    'fan_event' => 'Evento dei tifosi',
    'watch_party' => 'Watch Party',
    'other' => 'Altro'
];

// Inizializziamo le variabili
$errors = [];
$success = false;

// Gestiamo il form di creazione
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Otteniamo i dati dal form
    $title = isset($_POST['title']) ? sanitizeInput($_POST['title']) : '';
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    $location = isset($_POST['location']) ? sanitizeInput($_POST['location']) : '';
    $startDate = isset($_POST['start_date']) ? sanitizeInput($_POST['start_date']) : '';
    $endDate = isset($_POST['end_date']) ? sanitizeInput($_POST['end_date']) : '';
    $category = isset($_POST['category']) ? sanitizeInput($_POST['category']) : '';
    $visibility = isset($_POST['visibility']) ? sanitizeInput($_POST['visibility']) : '';
    $capacity = isset($_POST['capacity']) ? intval($_POST['capacity']) : null;
    
    // Validazioni
    if (empty($title)) {
        $errors[] = 'Il titolo è obbligatorio';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Il titolo non può superare i 255 caratteri';
    }
    
    if (empty($description)) {
        $errors[] = 'La descrizione è obbligatoria';
    }
    
    if (empty($location)) {
        $errors[] = 'La località è obbligatoria';
    } elseif (strlen($location) > 255) {
        $errors[] = 'La località non può superare i 255 caratteri';
    }
    
    if (empty($startDate)) {
        $errors[] = 'La data di inizio è obbligatoria';
    } else {
        $startTimestamp = strtotime($startDate);
        if ($startTimestamp === false) {
            $errors[] = 'Data di inizio non valida';
        } elseif ($startTimestamp < time()) {
            $errors[] = 'La data di inizio non può essere nel passato';
        }
    }
    
    if (!empty($endDate)) {
        $endTimestamp = strtotime($endDate);
        if ($endTimestamp === false) {
            $errors[] = 'Data di fine non valida';
        } elseif ($endTimestamp <= $startTimestamp) {
            $errors[] = 'La data di fine deve essere successiva alla data di inizio';
        }
    }
    
    if (!empty($category) && !array_key_exists($category, $categories)) {
        $errors[] = 'Categoria non valida';
    }
    
    if (!empty($visibility) && !in_array($visibility, ['public', 'followers', 'private'])) {
        $errors[] = 'Visibilità non valida';
    }
    
    if ($capacity !== null && $capacity <= 0) {
        $errors[] = 'La capacità deve essere un numero positivo';
    }
    
    // Gestiamo l'upload dell'immagine dell'evento
    $imageName = 'event_default.jpg';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($_FILES['image']['type'], $allowedTypes)) {
            $errors[] = 'Formato immagine non supportato. Sono accettati solo JPEG, PNG e GIF';
        } elseif ($_FILES['image']['size'] > $maxFileSize) {
            $errors[] = 'L\'immagine è troppo grande. La dimensione massima è 5MB';
        } else {
            // Creiamo la cartella uploads/events se non esiste
            $uploadsDir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/events';
            if (!file_exists($uploadsDir)) {
                mkdir($uploadsDir, 0777, true);
            }
            
            // Generiamo un nome univoco per l'immagine
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = 'event_' . time() . '_' . uniqid() . '.' . $extension;
            $uploadPath = $uploadsDir . '/' . $imageName;
            
            // Spostiamo il file nella cartella di destinazione
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $errors[] = 'Errore nel caricamento dell\'immagine';
                $imageName = 'event_default.jpg';
            }
        }
    }
    
    // Se non ci sono errori, inseriamo l'evento nel database
    if (empty($errors)) {
        // Formattiamo le date per il database
        $startDateFormatted = date('Y-m-d H:i:s', $startTimestamp);
        $endDateFormatted = !empty($endDate) ? date('Y-m-d H:i:s', $endTimestamp) : null;
        
        // Prepariamo la query di inserimento
        $insertEventQuery = "INSERT INTO events (
            title, description, location, image, start_date, end_date, 
            created_by, category, visibility, capacity, created_at, status
        ) VALUES (
            '{$title}', '{$description}', '{$location}', '{$imageName}', 
            '{$startDateFormatted}', " . ($endDateFormatted ? "'{$endDateFormatted}'" : "NULL") . ", 
            {$_SESSION['user_id']}, '{$category}', '{$visibility}', " . ($capacity ? "{$capacity}" : "NULL") . ", 
            NOW(), 'upcoming'
        )";
        
        if ($conn->query($insertEventQuery)) {
            $eventId = $conn->insert_id;
            
            // Aggiungiamo automaticamente il creatore come partecipante
            $insertParticipationQuery = "INSERT INTO event_participants (
                event_id, user_id, status, created_at
            ) VALUES (
                {$eventId}, {$_SESSION['user_id']}, 'going', NOW()
            )";
            
            $conn->query($insertParticipationQuery);
            
            // Notifichiamo i follower dell'utente (solo se l'evento è pubblico o per follower)
            if ($visibility != 'private') {
                $followersQuery = "SELECT follower_id FROM friendships 
                                  WHERE following_id = {$_SESSION['user_id']} AND status = 'accepted'";
                
                $followersResult = $conn->query($followersQuery);
                
                if ($followersResult->num_rows > 0) {
                    // Prepariamo l'inserimento in batch delle notifiche
                    $notificationValues = [];
                    
                    while ($follower = $followersResult->fetch_assoc()) {
                        $notificationValues[] = "({$follower['follower_id']}, {$_SESSION['user_id']}, 'new_event', 'ha creato un nuovo evento: \"{$title}\"', {$eventId}, NOW())";
                    }
                    
                    if (!empty($notificationValues)) {
                        $insertNotificationsQuery = "INSERT INTO notifications (user_id, sender_id, type, content, reference_id, created_at) VALUES " . implode(', ', $notificationValues);
                        $conn->query($insertNotificationsQuery);
                    }
                }
            }
            
            $success = true;
            setFlashMessage('Evento creato con successo', 'success');
            redirect(SITE_URL . '/event.php?id=' . $eventId);
        } else {
            $errors[] = 'Errore nella creazione dell\'evento: ' . $conn->error;
        }
    }
}
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h1 class="h3 mb-0">
                        <i class="fas fa-calendar-plus"></i> Crea nuovo evento
                    </h1>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Titolo dell'evento <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required maxlength="255">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category" class="form-label">Categoria <span class="text-danger">*</span></label>
                                    <select class="form-select" id="category" name="category" required>
                                        <?php foreach ($categories as $key => $value): ?>
                                            <option value="<?php echo $key; ?>" <?php echo (isset($_POST['category']) && $_POST['category'] == $key) ? 'selected' : ''; ?>>
                                                <?php echo $value; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Immagine dell'evento</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/jpeg, image/png, image/gif">
                                    <div class="form-text">
                                        Immagine opzionale. Max 5MB. Formati supportati: JPEG, PNG, GIF
                                    </div>
                                </div>
                                
                                <div class="mt-3 text-center">
                                    <img id="imagePreview" src="<?php echo UPLOADS_URL; ?>/events/event_default.jpg" class="img-fluid border rounded" alt="Anteprima immagine" style="max-height: 150px;">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrizione <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="5" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Luogo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="location" name="location" value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>" required maxlength="255">
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label">Data e ora di inizio <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_date" class="form-label">Data e ora di fine</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>">
                                <div class="form-text">Opzionale. Lasciare vuoto se è un evento di un solo giorno</div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="visibility" class="form-label">Visibilità <span class="text-danger">*</span></label>
                                <select class="form-select" id="visibility" name="visibility" required>
                                    <option value="public" <?php echo (isset($_POST['visibility']) && $_POST['visibility'] == 'public') ? 'selected' : ''; ?>>
                                        Pubblico (visibile a tutti)
                                    </option>
                                    <option value="followers" <?php echo (isset($_POST['visibility']) && $_POST['visibility'] == 'followers') ? 'selected' : ''; ?>>
                                        Solo follower (visibile solo ai tuoi follower)
                                    </option>
                                    <option value="private" <?php echo (isset($_POST['visibility']) && $_POST['visibility'] == 'private') ? 'selected' : ''; ?>>
                                        Privato (visibile solo a te)
                                    </option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="capacity" class="form-label">Capacità massima</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" min="1" value="<?php echo isset($_POST['capacity']) ? intval($_POST['capacity']) : ''; ?>">
                                <div class="form-text">Opzionale. Numero massimo di partecipanti</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="<?php echo SITE_URL; ?>/events.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Annulla
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Crea evento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Anteprima dell'immagine
    $('#image').change(function() {
        var input = this;
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    // Validazione della data di fine
    $('#start_date, #end_date').change(function() {
        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        
        if (startDate && endDate && new Date(endDate) <= new Date(startDate)) {
            alert('La data di fine deve essere successiva alla data di inizio');
            $('#end_date').val('');
        }
    });
    
    // Validazione della data di inizio (non può essere nel passato)
    $('#start_date').change(function() {
        var startDate = new Date($(this).val());
        var now = new Date();
        
        if (startDate < now) {
            alert('La data di inizio non può essere nel passato');
            $(this).val('');
        }
    });
});
</script>

<?php
// Includiamo il footer
require_once 'includes/footer.php';
?>
