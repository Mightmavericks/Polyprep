    </div><!-- End of .container -->
    
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-title">About Polyprep</h3>
                    <p>Polyprep is an educational platform designed to help students access and share course materials.</p>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="<?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'admin_dashboard.php' : 'user_dashboard.php'; ?>">Dashboard</a></li>
                            <li><a href="upload.php">Upload Notes</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                            <li><a href="register.php">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3 class="footer-title">Contact</h3>
                    <p>Email: info@polyprep.edu</p>
                    <p>Phone: +1 (123) 456-7890</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Polyprep. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="assets/js/main.js"></script>
</body>
</html>
