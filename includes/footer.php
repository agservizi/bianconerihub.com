    </div><!-- Fine del contenitore principale -->
    
    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>BiancoNeriHub</h5>
                    <p>Il social network dedicato a tutti i tifosi della Juventus. Condividi la tua passione per i colori bianconeri!</p>
                </div>
                <div class="col-md-4">
                    <h5>Link utili</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-white">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/news.php" class="text-white">News</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/events.php" class="text-white">Eventi</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php" class="text-white">Chi siamo</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php" class="text-white">Contatti</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Seguici</h5>
                    <div class="social-icons">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube fa-2x"></i></a>
                    </div>
                    <p class="mt-3">
                        <a href="<?php echo SITE_URL; ?>/terms.php" class="text-white">Termini di servizio</a> |
                        <a href="<?php echo SITE_URL; ?>/privacy.php" class="text-white">Privacy Policy</a>
                    </p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> BiancoNeriHub. Tutti i diritti riservati.</p>
                <p class="small">Questo sito non è affiliato alla Juventus Football Club S.p.A.</p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript di Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- JavaScript personalizzato -->
    <script src="<?php echo SITE_URL; ?>/js/main.js"></script>
    
    <?php if (isset($extraJs)): ?>
        <!-- JavaScript aggiuntivo specifico per la pagina -->
        <?php echo $extraJs; ?>
    <?php endif; ?>
</body>
</html>
