<?php
// Include authentication functions
require_once __DIR__ . '/auth.php';

// Get current page for active state
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
    <div class="navbar-container">
        <!-- Brand -->
        <a href="/SCMS-PHP/pages/home.php" class="navbar-brand">
            <div class="navbar-brand-text">
                <div>SCMS</div>
                <div class="navbar-brand-subtitle">Smart Complaint Management</div>
            </div>
        </a>
        
        <!-- Navigation Menu -->
        <ul class="navbar-menu" id="navbarMenu">
            <?php if (isLoggedIn()): ?>
                <?php if (isStudent()): ?>
                    <li><a href="/SCMS-PHP/student/dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="/SCMS-PHP/student/my-complaints.php" class="<?php echo $currentPage === 'my-complaints.php' ? 'active' : ''; ?>">My Complaints</a></li>
                    <li><a href="/SCMS-PHP/student/submit-complaint.php" class="<?php echo $currentPage === 'submit-complaint.php' ? 'active' : ''; ?>">Submit Complaint</a></li>
                    <li><a href="/SCMS-PHP/student/track-complaint.php" class="<?php echo $currentPage === 'track-complaint.php' ? 'active' : ''; ?>">Track</a></li>
                <?php elseif (isAdmin()): ?>
                    <li><a href="/SCMS-PHP/admin/dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                    <li><a href="/SCMS-PHP/admin/complaints.php" class="<?php echo $currentPage === 'complaints.php' ? 'active' : ''; ?>">Complaints</a></li>
                    <li><a href="/SCMS-PHP/admin/users.php" class="<?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">Users</a></li>
                <?php endif; ?>
            <?php else: ?>
                <li><a href="/SCMS-PHP/pages/home.php" class="<?php echo $currentPage === 'home.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="/SCMS-PHP/pages/complaints.php" class="<?php echo $currentPage === 'complaints.php' ? 'active' : ''; ?>">Complaints</a></li>
                <li><a href="/SCMS-PHP/pages/about.php" class="<?php echo $currentPage === 'about.php' ? 'active' : ''; ?>">About</a></li>
                <li><a href="/SCMS-PHP/pages/contact.php" class="<?php echo $currentPage === 'contact.php' ? 'active' : ''; ?>">Contact</a></li>
            <?php endif; ?>
        </ul>
        
        <!-- Actions -->
        <div class="navbar-actions">
            <?php if (isLoggedIn()): ?>
                <div class="navbar-user">
                    <div class="navbar-avatar">
                        <?php echo strtoupper(substr(getCurrentUserName(), 0, 1)); ?>
                    </div>
                    <div class="navbar-user-name">
                        <div style="font-weight: 600; font-size: 0.875rem; color: var(--gray-900);">
                            <?php echo htmlspecialchars(getCurrentUserName()); ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--gray-600);">
                            <?php echo ucfirst(getCurrentUserRole()); ?>
                        </div>
                    </div>
                </div>
                <a href="/SCMS-PHP/pages/logout.php" class="btn btn-ghost btn-sm">Logout</a>
            <?php else: ?>
                <a href="/SCMS-PHP/pages/login.php" class="btn btn-ghost btn-sm">Login</a>
                <a href="/SCMS-PHP/pages/signup.php" class="btn btn-primary btn-sm">Sign Up</a>
            <?php endif; ?>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggle" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('navbarMenu');
    menu.classList.toggle('active');
}
</script>
