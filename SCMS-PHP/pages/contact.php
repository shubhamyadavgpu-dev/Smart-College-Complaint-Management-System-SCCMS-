<?php
$pageTitle = 'Contact Us';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<style>
.page-header {
    background: var(--white);
    padding: var(--space-6) 0;
    border-bottom: 1px solid var(--gray-200);
}

.contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: var(--space-6);
    margin-top: var(--space-6);
}

.contact-info-card {
    padding: var(--space-4);
    background: var(--gray-50);
    border-radius: var(--radius-md);
}

.contact-info-item {
    display: flex;
    gap: var(--space-3);
    margin-bottom: var(--space-4);
}

.contact-icon {
    width: 48px;
    height: 48px;
    background: var(--primary);
    color: var(--white);
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="page-header">
    <div class="container">
        <h1 class="dashboard-title">Contact Us</h1>
        <p class="dashboard-subtitle">Get in touch with our support team</p>
    </div>
</div>

<div class="container py-6">
    <div class="contact-grid">
        <div>
            <div class="card">
                <div class="card-body">
                    <h3 style="margin-bottom: var(--space-4);">Send us a Message</h3>
                    <form>
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Subject</label>
                            <input type="text" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Message</label>
                            <textarea class="form-textarea" rows="5" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div>
            <div class="contact-info-card">
                <h3 style="margin-bottom: var(--space-4);">Contact Information</h3>
                
                <div class="contact-info-item">
                    <div class="contact-icon">📧</div>
                    <div>
                        <strong style="display: block; margin-bottom: 0.25rem;">Email</strong>
                        <a href="mailto:support@college.edu">support@college.edu</a>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-icon">📞</div>
                    <div>
                        <strong style="display: block; margin-bottom: 0.25rem;">Phone</strong>
                        <a href="tel:+911234567890">+91 1234567890</a>
                    </div>
                </div>
                
                <div class="contact-info-item">
                    <div class="contact-icon">📍</div>
                    <div>
                        <strong style="display: block; margin-bottom: 0.25rem;">Address</strong>
                        <p style="margin: 0; color: var(--gray-700);">
                            College Campus<br>
                            Main Building, Room 101<br>
                            City, State - 123456
                        </p>
                    </div>
                </div>
                
                <div class="contact-info-item" style="margin-bottom: 0;">
                    <div class="contact-icon">⏰</div>
                    <div>
                        <strong style="display: block; margin-bottom: 0.25rem;">Office Hours</strong>
                        <p style="margin: 0; color: var(--gray-700);">
                            Monday - Friday: 9:00 AM - 5:00 PM<br>
                            Saturday: 9:00 AM - 1:00 PM<br>
                            Sunday: Closed
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
