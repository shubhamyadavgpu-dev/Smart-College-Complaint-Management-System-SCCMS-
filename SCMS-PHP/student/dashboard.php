<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require student login
requireLogin('student');

$user_id = $_SESSION['user_id'];

// Get student statistics
$stats = [
    'total' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'resolved' => 0
];

$query = "SELECT status, COUNT(*) as count FROM complaints WHERE user_id = ? GROUP BY status";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $stats[$row['status']] = $row['count'];
    $stats['total'] += $row['count'];
}

// Get recent complaints
$query = "SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$recent_complaints = mysqli_stmt_get_result($stmt);

$page_title = "Student Dashboard";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></h1>
            <p class="text-muted">Student ID: <?php echo htmlspecialchars($_SESSION['student_id']); ?></p>
        </div>
        <a href="submit-complaint.php" class="btn btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Submit New Complaint
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Complaints</p>
                <h2 class="stat-value"><?php echo $stats['total']; ?></h2>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Pending</p>
                <h2 class="stat-value"><?php echo $stats['pending']; ?></h2>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2v20M2 12h20"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">In Progress</p>
                <h2 class="stat-value"><?php echo $stats['in_progress']; ?></h2>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Resolved</p>
                <h2 class="stat-value"><?php echo $stats['resolved']; ?></h2>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-cards">
            <a href="submit-complaint.php" class="action-card">
                <div class="action-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                </div>
                <h3>Submit Complaint</h3>
                <p>Report a new issue or complaint</p>
            </a>

            <a href="my-complaints.php" class="action-card">
                <div class="action-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <h3>My Complaints</h3>
                <p>View all your submitted complaints</p>
            </a>

            <a href="track-complaint.php" class="action-card">
                <div class="action-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>
                <h3>Track Complaint</h3>
                <p>Track status of your complaints</p>
            </a>
        </div>
    </div>

    <!-- Recent Complaints -->
    <div class="recent-section">
        <div class="section-header">
            <h2>Recent Complaints</h2>
            <a href="my-complaints.php" class="btn btn-secondary">View All</a>
        </div>

        <?php if (mysqli_num_rows($recent_complaints) > 0): ?>
            <div class="complaints-list">
                <?php while ($complaint = mysqli_fetch_assoc($recent_complaints)): ?>
                    <div class="complaint-card">
                        <div class="complaint-header">
                            <div>
                                <h3><?php echo htmlspecialchars($complaint['title']); ?></h3>
                                <p class="complaint-id">ID: <?php echo htmlspecialchars($complaint['complaint_id']); ?></p>
                            </div>
                            <span class="badge badge-<?php echo $complaint['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                            </span>
                        </div>
                        <div class="complaint-body">
                            <p><?php echo nl2br(htmlspecialchars(substr($complaint['description'], 0, 150))); ?>
                                <?php echo strlen($complaint['description']) > 150 ? '...' : ''; ?>
                            </p>
                        </div>
                        <div class="complaint-footer">
                            <div class="complaint-meta">
                                <span class="badge badge-category"><?php echo htmlspecialchars($complaint['category']); ?></span>
                                <span class="badge badge-priority badge-priority-<?php echo $complaint['priority']; ?>">
                                    <?php echo ucfirst($complaint['priority']); ?> Priority
                                </span>
                            </div>
                            <div class="complaint-actions">
                                <span class="text-muted"><?php echo timeAgo($complaint['created_at']); ?></span>
                                <a href="track-complaint.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn-link">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
                <h3>No Complaints Yet</h3>
                <p>You haven't submitted any complaints. Click the button below to submit your first complaint.</p>
                <a href="submit-complaint.php" class="btn btn-primary">Submit Complaint</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
include '../includes/footer.php';
?>
