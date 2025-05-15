/**
 * JavaScript principale
 * BiancoNeriHub - Social network per tifosi della Juventus
 */

// Definizione dell'URL del sito (globale)
var SITE_URL = window.location.protocol + '//' + window.location.host + '/bianconerihub';
var UPLOADS_URL = SITE_URL + '/uploads';
var ASSETS_URL = SITE_URL + '/assets';

// Documento pronto
$(document).ready(function() {
    // Inizializzazione dei tooltip di Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inizializzazione dei popover di Bootstrap
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // Auto-nascondere messaggi flash dopo 5 secondi
    setTimeout(function() {
        $('.alert-dismissible').alert('close');
    }, 5000);
    
    // Funzione per confermare azioni pericolose
    $('.confirm-action').click(function(e) {
        var message = $(this).data('confirm-message') || 'Sei sicuro di voler procedere?';
        
        if (!confirm(message)) {
            e.preventDefault();
            return false;
        }
    });
    
    // Invio commenti tramite AJAX
    $('.comment-form').submit(function(e) {
        e.preventDefault();
        
        var form = $(this);
        var url = form.attr('action');
        var formData = form.serialize();
        
        $.ajax({
            type: 'POST',
            url: url,
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Reset del form
                    form.find('input[name="content"]').val('');
                    
                    // Aggiornamento del contatore dei commenti
                    var postId = form.find('input[name="post_id"]').val();
                    var commentsCount = $('.post[data-post-id="' + postId + '"] .comments-count');
                    var newCount = parseInt(commentsCount.text()) + 1;
                    commentsCount.text(newCount);
                    
                    // Aggiunta del nuovo commento
                    var commentHtml = 
                        '<div class="d-flex mb-3 comment fade-in">' +
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
                    $(commentHtml).insertBefore(commentsSection.find('form').closest('.d-flex'));
                } else {
                    alert('Errore: ' + response.message);
                }
            },
            error: function() {
                alert('Si è verificato un errore durante l\'invio del commento.');
            }
        });
    });
    
    // Toggle Mi piace tramite AJAX
    $('.like-button').click(function() {
        var button = $(this);
        var postId = button.data('post-id');
        
        $.ajax({
            type: 'POST',
            url: SITE_URL + '/api/toggle_like.php',
            data: { post_id: postId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Aggiornamento contatore like
                    button.find('.likes-count').text(response.likes_count);
                    
                    // Aggiornamento stile pulsante
                    if (response.has_liked) {
                        button.removeClass('btn-outline-primary').addClass('btn-primary');
                    } else {
                        button.removeClass('btn-primary').addClass('btn-outline-primary');
                    }
                } else {
                    alert('Errore: ' + response.message);
                }
            },
            error: function() {
                alert('Si è verificato un errore durante l\'operazione.');
            }
        });
    });
    
    // Follow/Unfollow utente tramite AJAX
    $('.follow-button').click(function() {
        var button = $(this);
        var userId = button.data('user-id');
        
        $.ajax({
            type: 'POST',
            url: SITE_URL + '/api/toggle_follow.php',
            data: { user_id: userId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    if (response.is_following) {
                        button.html('<i class="fas fa-user-check"></i> Seguo');
                        button.removeClass('btn-primary').addClass('btn-secondary');
                    } else {
                        button.html('<i class="fas fa-user-plus"></i> Segui');
                        button.removeClass('btn-secondary').addClass('btn-primary');
                    }
                } else {
                    alert('Errore: ' + response.message);
                }
            },
            error: function() {
                alert('Si è verificato un errore durante l\'operazione.');
            }
        });
    });
    
    // Caricamento infinito post nel feed
    var loading = false;
    var page = 1;
    
    $(window).scroll(function() {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200 && !loading) {
            if ($('#loadMorePosts').length) {
                loading = true;
                page++;
                
                $('#loadMorePosts').html('<i class="fas fa-spinner fa-spin"></i> Caricamento...');
                
                $.ajax({
                    url: SITE_URL + '/api/load_more_posts.php',
                    type: 'GET',
                    data: { page: page },
                    dataType: 'html',
                    success: function(response) {
                        loading = false;
                        
                        if (response.trim() !== '') {
                            // Inseriamo i nuovi post
                            $(response).insertBefore($('#loadMorePosts').parent());
                            $('#loadMorePosts').html('<i class="fas fa-sync"></i> Carica altri post');
                        } else {
                            // Non ci sono più post
                            $('#loadMorePosts').html('Non ci sono altri post da caricare').prop('disabled', true);
                        }
                    }
                });
            }
        }
    });
    
    // Contatore caratteri per testo del post
    $('textarea[name="content"]').on('input', function() {
        var maxLength = 1000;
        var currentLength = $(this).val().length;
        var remaining = maxLength - currentLength;
        
        if ($(this).next('.char-counter').length === 0) {
            $(this).after('<div class="char-counter text-muted small text-end mt-1">Caratteri rimanenti: <span>' + remaining + '</span>/' + maxLength + '</div>');
        } else {
            $(this).next('.char-counter').find('span').text(remaining);
        }
        
        if (remaining < 0) {
            $(this).next('.char-counter').addClass('text-danger');
        } else {
            $(this).next('.char-counter').removeClass('text-danger');
        }
    });
    
    // Preview immagine caricata
    $('.input-file-image').change(function() {
        var input = this;
        var previewContainer = $(input).closest('.form-group').find('.image-preview');
        
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                previewContainer.html('<img src="' + e.target.result + '" class="img-fluid rounded mt-2">');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    // Toggle dark mode
    $('#darkModeToggle').click(function() {
        $('body').toggleClass('dark-mode');
        
        // Salva la preferenza in localStorage
        if ($('body').hasClass('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
        } else {
            localStorage.setItem('darkMode', 'disabled');
        }
    });
    
    // Carica l'impostazione dark mode dal localStorage
    if (localStorage.getItem('darkMode') === 'enabled') {
        $('body').addClass('dark-mode');
    }
    
    // Selezione Emoji per i post
    $('#btnAddEmoji').click(function() {
        var button = $(this);
        var emojiPicker = $('#emojiPicker');
        
        if (emojiPicker.length === 0) {
            // Implementazione base di un selettore emoji
            var emojis = ['😀', '😁', '😂', '🤣', '😃', '😄', '😅', '😆', '😉', '😊', '😋', '😎', '😍', '😘', '😗', '😙', '😚', '🙂', '🤗', '🤔', '😐', '😑', '😶', '🙄', '😏', '😣', '😥', '😮', '🤐', '😯', '😪', '😫', '😴', '😌', '🤓', '😛', '😜', '😝', '🤤', '😒', '😓', '😔', '😕', '🙃', '🤑', '😲', '🙁', '😖', '😞', '😟', '😤', '😢', '😭', '😦', '😧', '😨', '😩', '😬', '😰', '😱', '😳', '😵', '😡', '😠', '😇', '🤠', '🤡', '🤥', '🤧', '😷', '🤒', '🤕', '🤢', '👍', '👎', '👏', '🙌', '👐', '🤝', '👊', '✊', '🤛', '🤜', '🤞', '✌️', '🤘', '👌', '👈', '👉', '👆', '👇', '👋', '🤙', '👃', '👀', '👂', '👄', '💋', '🖤', '💙', '💚', '💛', '💜', '❤️', '💓', '💔', '💕', '💖', '💗', '💘', '💝', '💞', '🧡', '⚽', '🏀', '🏈', '⚾', '🎾', '🏐', '🏉', '🎱', '🏆', '🏅', '🥇', '🥈', '🥉', '🥅', '⚠️', '⚡', '💯', '✅', '❎', '❌', '⭕', '💢'];
            
            var emojiPickerHtml = '<div id="emojiPicker" class="emoji-picker p-2 rounded shadow" style="position: absolute; background: white; border: 1px solid #ddd; z-index: 1000; max-width: 280px;">';
            
            emojis.forEach(function(emoji) {
                emojiPickerHtml += '<span class="emoji-item" style="font-size: 1.5rem; cursor: pointer; display: inline-block; margin: 5px; width: 30px; text-align: center;">' + emoji + '</span>';
            });
            
            emojiPickerHtml += '</div>';
            
            button.after(emojiPickerHtml);
            
            // Gestione click sulle emoji
            $('.emoji-item').click(function() {
                var emoji = $(this).text();
                var textarea = button.closest('form').find('textarea');
                textarea.val(textarea.val() + emoji);
                $('#emojiPicker').remove();
                
                // Trigger dell'evento input per aggiornare il contatore
                textarea.trigger('input');
            });
            
            // Chiusura quando si clicca fuori
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#emojiPicker').length && !$(e.target).is(button)) {
                    $('#emojiPicker').remove();
                }
            });
        } else {
            emojiPicker.remove();
        }
    });
    
    // Animazione per gli elementi quando diventano visibili
    function animateOnScroll() {
        $('.animate-on-scroll').each(function() {
            var position = $(this).offset().top;
            var windowHeight = $(window).height();
            var scrollPosition = $(window).scrollTop();
            
            if (position < scrollPosition + windowHeight - 100) {
                $(this).addClass('fade-in');
            }
        });
    }
    
    // Esegui l'animazione all'avvio e durante lo scroll
    animateOnScroll();
    $(window).scroll(animateOnScroll);
    
    // Funzione per condivisione post sui social
    $('.share-button').click(function() {
        var postId = $(this).data('post-id');
        var shareUrl = SITE_URL + '/post.php?id=' + postId;
        
        // Popup per la condivisione sociale
        var sharePopup = 
            '<div class="share-popup p-3 rounded shadow" style="position: absolute; background: white; border: 1px solid #ddd; z-index: 1000;">' +
                '<div class="mb-2">Condividi questo post:</div>' +
                '<div class="share-buttons d-flex">' +
                    '<a href="https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl) + '" target="_blank" class="btn btn-sm btn-primary me-2"><i class="fab fa-facebook-f"></i></a>' +
                    '<a href="https://twitter.com/intent/tweet?url=' + encodeURIComponent(shareUrl) + '" target="_blank" class="btn btn-sm btn-info me-2"><i class="fab fa-twitter"></i></a>' +
                    '<a href="https://api.whatsapp.com/send?text=' + encodeURIComponent('Dai un\'occhiata a questo post su BiancoNeriHub: ' + shareUrl) + '" target="_blank" class="btn btn-sm btn-success me-2"><i class="fab fa-whatsapp"></i></a>' +
                    '<a href="https://telegram.me/share/url?url=' + encodeURIComponent(shareUrl) + '" target="_blank" class="btn btn-sm btn-info"><i class="fab fa-telegram"></i></a>' +
                '</div>' +
                '<div class="mt-2">' +
                    '<div class="input-group input-group-sm">' +
                        '<input type="text" class="form-control" value="' + shareUrl + '" readonly>' +
                        '<button class="btn btn-outline-secondary copy-link-btn" type="button" data-url="' + shareUrl + '"><i class="fas fa-copy"></i></button>' +
                    '</div>' +
                '</div>' +
                '<div class="text-end mt-2">' +
                    '<button class="btn btn-sm btn-secondary close-share-popup">Chiudi</button>' +
                '</div>' +
            '</div>';
        
        // Rimuovi popup esistenti
        $('.share-popup').remove();
        
        // Aggiungi il nuovo popup
        $(this).after(sharePopup);
        
        // Gestione pulsante copia
        $('.copy-link-btn').click(function() {
            var url = $(this).data('url');
            var tempInput = $('<input>');
            $('body').append(tempInput);
            tempInput.val(url).select();
            document.execCommand('copy');
            tempInput.remove();
            
            $(this).html('<i class="fas fa-check"></i>');
            setTimeout(function() {
                $('.copy-link-btn').html('<i class="fas fa-copy"></i>');
            }, 2000);
        });
        
        // Chiusura popup
        $('.close-share-popup').click(function() {
            $('.share-popup').remove();
        });
        
        // Chiusura quando si clicca fuori
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.share-popup').length && !$(e.target).hasClass('share-button')) {
                $('.share-popup').remove();
            }
        });
    });
    
    // Notifiche in tempo reale (simulazione)
    function checkNotifications() {
        if ($('.notifications-badge').length) {
            $.ajax({
                url: SITE_URL + '/api/check_notifications.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.count > 0) {
                        $('.notifications-badge').text(response.count).removeClass('d-none');
                    } else {
                        $('.notifications-badge').addClass('d-none');
                    }
                }
            });
        }
    }
    
    // Verifica notifiche ogni 60 secondi
    if (document.cookie.indexOf('user_id') !== -1) {
        setInterval(checkNotifications, 60000);
    }
});
