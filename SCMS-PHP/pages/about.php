<?php
$pageTitle = 'About Us';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<style>
.page-header {
    background: var(--white);
    padding: var(--space-6) 0;
    border-bottom: 1px solid var(--gray-200);
}

.content-section {
    padding: var(--space-6) 0;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--space-4);
    margin-top: var(--space-6);
}

.feature-card {
    text-align: center;
    padding: var(--space-4);
}

.feature-icon {
    width: 64px;
    height: 64px;
    background: var(--primary);
    color: var(--white);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    margin: 0 auto var(--space-3);
}

@media (max-width: 768px) {
    .features-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header">
    <div class="container">
        <h1 class="dashboard-title">About SCMS</h1>
        <p class="dashboard-subtitle">Your trusted platform for transparent complaint management</p>
    </div>
</div>

<div class="content-section">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto;">
            <h2>Our Mission</h2>
            <p style="font-size: 1.125rem; line-height: 1.8; color: var(--gray-700);">
                The Smart College Complaint Management System (SCMS) is dedicated to providing a transparent, efficient, and user-friendly platform for students to voice their concerns and for administrators to address them promptly.
            </p>
            
            <h2 style="margin-top: var(--space-6);">Why SCMS?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">✓</div>
                    <h3>Easy Submission</h3>
                    <p style="font-size: 0.875rem; color: var(--gray-600);">
                        Submit complaints with just a few clicks, anytime, anywhere
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Transparent Tracking</h3>
                    <p style="font-size: 0.875rem; color: var(--gray-600);">
                        Track your complaint status in real-time with complete visibility
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">⚡</div>
                    <h3>Faster Resolution</h3>
                    <p style="font-size: 0.875rem; color: var(--gray-600);">
                        Streamlined process ensures quick action and resolution
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Centralized Management</h3>
                    <p style="font-size: 0.875rem; color: var(--gray-600);">
                        All complaints in one place for efficient handling
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">👥</div>
                    <h3>Student-Friendly</h3>
                    <p style="font-size: 0.875rem; color: var(--gray-600);">
                        Designed with students in mind for ease of use
                    </p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📈</div>
                    <h3>Data-Driven Insights</h3>
                    <p style="font-size: 0.875rem; color: var(--gray-600);">
                        Analytics help improve campus services over time
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
