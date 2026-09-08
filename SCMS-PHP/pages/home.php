<?php
$pageTitle = 'Home';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Get statistics
$stats = getComplaintStats($conn);
?>

<style>
.hero {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: var(--white);
    padding: var(--space-8) 0;
    text-align: center;
}

.hero-content {
    max-width: 800px;
    margin: 0 auto;
}

.hero h1 {
    font-size: 3rem;
    margin-bottom: var(--space-3);
    color: var(--white);
}

.hero p {
    font-size: 1.25rem;
    margin-bottom: var(--space-4);
    color: rgba(255, 255, 255, 0.9);
}

.hero-actions {
    display: flex;
    gap: var(--space-2);
    justify-content: center;
    flex-wrap: wrap;
}

.hero-actions .btn {
    padding: 0.875rem 2rem;
    font-size: 1rem;
}

.hero-actions .btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.3);
    color: var(--white);
}

.hero-actions .btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
}

.section {
    padding: var(--space-8) 0;
}

.section-header {
    text-align: center;
    margin-bottom: var(--space-6);
}

.section-title {
    font-size: 2rem;
    margin-bottom: var(--space-2);
}

.section-subtitle {
    color: var(--gray-600);
    font-size: 1.125rem;
}

.stats-grid {
    max-width: 1200px;
    margin: 0 auto;
}

.stat-card {
    text-align: center;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-3);
    max-width: 1200px;
    margin: 0 auto;
}

.category-card {
    background: var(--white);
    padding: var(--space-4);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow);
    text-align: center;
    transition: var(--transition);
    cursor: pointer;
    border: 1px solid transparent;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: var(--primary);
}

.category-icon {
    font-size: 2.5rem;
    margin-bottom: var(--space-2);
}

.category-name {
    font-weight: var(--font-weight-semibold);
    color: var(--gray-900);
}

.steps-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: var(--space-4);
    max-width: 1200px;
    margin: 0 auto;
}

.step-card {
    text-align: center;
    position: relative;
}

.step-number {
    width: 60px;
    height: 60px;
    background: var(--primary);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: var(--font-weight-bold);
    margin: 0 auto var(--space-3);
}

.step-title {
    font-weight: var(--font-weight-semibold);
    margin-bottom: var(--space-1);
}

.step-description {
    font-size: 0.875rem;
    color: var(--gray-600);
}

.cta-section {
    background: var(--gray-900);
    color: var(--white);
    text-align: center;
    padding: var(--space-8) 0;
}

.cta-section h2 {
    color: var(--white);
    margin-bottom: var(--space-4);
}

@media (max-width: 768px) {
    .categories-grid, .steps-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .hero h1 {
        font-size: 2rem;
    }
}
</style>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Your Voice. Our Responsibility.</h1>
            <p>Submit, track and manage college complaints through one transparent and efficient platform.</p>
            <div class="hero-actions">
                <a href="<?php echo isLoggedIn() ? '/SCMS-PHP/student/submit-complaint.php' : '/SCMS-PHP/pages/signup.php'; ?>" class="btn btn-primary btn-lg">
                    Submit a Complaint
                </a>
                <a href="<?php echo isLoggedIn() ? '/SCMS-PHP/student/track-complaint.php' : '/SCMS-PHP/pages/login.php'; ?>" class="btn btn-secondary btn-lg">
                    Track Complaint
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="section" style="background: var(--white);">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon icon-primary">📊</div>
                <div class="stat-value"><?php echo $stats['total'] ?? 0; ?>+</div>
                <div class="stat-label">Complaints Submitted</div>
            </div>
            
            <div class="stat-card stat-success">
                <div class="stat-icon icon-success">✓</div>
                <div class="stat-value"><?php echo $stats['resolved'] ?? 0; ?>+</div>
                <div class="stat-label">Issues Resolved</div>
            </div>
            
            <div class="stat-card stat-info">
                <div class="stat-icon icon-info">⚡</div>
                <div class="stat-value">24h</div>
                <div class="stat-label">Average Response</div>
            </div>
            
            <div class="stat-card stat-warning">
                <div class="stat-icon icon-warning">⭐</div>
                <div class="stat-value">95%</div>
                <div class="stat-label">Resolution Rate</div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">How It Works</h2>
            <p class="section-subtitle">Simple and transparent complaint resolution process</p>
        </div>
        
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">01</div>
                <h3 class="step-title">Submit Complaint</h3>
                <p class="step-description">Register your complaint with detailed information and attachments</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">02</div>
                <h3 class="step-title">Review Process</h3>
                <p class="step-description">Admin reviews and assigns to appropriate department</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">03</div>
                <h3 class="step-title">Action Taken</h3>
                <p class="step-description">Department takes necessary action to resolve the issue</p>
            </div>
            
            <div class="step-card">
                <div class="step-number">04</div>
                <h3 class="step-title">Issue Resolved</h3>
                <p class="step-description">Track real-time updates until complete resolution</p>
            </div>
        </div>
    </div>
</section>

<!-- Complaint Categories -->
<section class="section" style="background: var(--white);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Complaint Categories</h2>
            <p class="section-subtitle">Select the category that best fits your concern</p>
        </div>
        
        <div class="categories-grid">
            <a href="complaints.php?category=Academics" class="category-card">
                <div class="category-icon">📚</div>
                <div class="category-name">Academics</div>
            </a>
            
            <a href="complaints.php?category=Hostel" class="category-card">
                <div class="category-icon">🏠</div>
                <div class="category-name">Hostel</div>
            </a>
            
            <a href="complaints.php?category=Infrastructure" class="category-card">
                <div class="category-icon">🏗️</div>
                <div class="category-name">Infrastructure</div>
            </a>
            
            <a href="complaints.php?category=Library" class="category-card">
                <div class="category-icon">📖</div>
                <div class="category-name">Library</div>
            </a>
            
            <a href="complaints.php?category=Transport" class="category-card">
                <div class="category-icon">🚌</div>
                <div class="category-name">Transport</div>
            </a>
            
            <a href="complaints.php?category=Cleanliness" class="category-card">
                <div class="category-icon">🧹</div>
                <div class="category-name">Cleanliness</div>
            </a>
            
            <a href="complaints.php?category=IT" class="category-card">
                <div class="category-icon">💻</div>
                <div class="category-name">IT & Internet</div>
            </a>
            
            <a href="complaints.php?category=Administration" class="category-card">
                <div class="category-icon">🏛️</div>
                <div class="category-name">Administration</div>
            </a>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h2>Have an issue on campus?</h2>
        <a href="<?php echo isLoggedIn() ? '/SCMS-PHP/student/submit-complaint.php' : '/SCMS-PHP/pages/signup.php'; ?>" class="btn btn-primary btn-lg">
            Submit Your Complaint
        </a>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
