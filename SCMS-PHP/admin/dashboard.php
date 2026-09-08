<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require admin login
requireLogin('admin');

// Get overall statistics
$stats = getComplaintStats();

// Get category-wise statistics
$category_query = "SELECT category, COUNT(*) as count FROM complaints GROUP BY category ORDER BY count DESC";
$category_result = mysqli_query($conn, $category_query);
$categories = [];
while ($row = mysqli_fetch_assoc($category_result)) {
    $categories[] = $row;
}

// Get recent complaints
$recent_query = "SELECT c.*, u.full_name, u.student_id 
                 FROM complaints c 
                 JOIN users u ON c.user_id = u.id 
                 ORDER BY c.created_at DESC 
                 LIMIT 5";
$recent_complaints = mysqli_query($conn, $recent_query);

// Get pending complaints count
$pending_query = "SELECT COUNT(*) as count FROM complaints WHERE status = 'pending'";
$pending_result = mysqli_query($conn, $pending_query);
$pending_count = mysqli_fetch_assoc($pending_result)['count'];

// Get today's complaints
$today_query = "SELECT COUNT(*) as count FROM complaints WHERE DATE(created_at) = CURDATE()";
$today_result = mysqli_query($conn, $today_query);
$today_count = mysqli_fetch_assoc($today_result)['count'];

// Get average resolution time (in days)
$resolution_query = "SELECT AVG(DATEDIFF(resolved_at, created_at)) as avg_days 
                     FROM complaints 
                     WHERE status = 'resolved' AND resolved_at IS NOT NULL";
$resolution_result = mysqli_query($conn, $resolution_query);
$avg_resolution = mysqli_fetch_assoc($resolution_result)['avg_days'];
$avg_resolution = $avg_resolution ? round($avg_resolution, 1) : 0;

$page_title = "Admin Dashboard";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Admin Dashboard</h1>
            <p class="text-muted">Overview of all complaints and system statistics</p>
        </div>
        <div class="header-actions">
            <a href="users.php" class="btn btn-secondary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Manage Users
            </a>
            <a href="complaints.php" class="btn btn-primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 11l3 3L22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
                All Complaints
            </a>
        </div>
    </div>

    <!-- Alert for pending complaints -->
    <?php if ($pending_count > 0): ?>
        <div class="alert alert-warning">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            You have <strong><?php echo $pending_count; ?></strong> pending complaint<?php echo $pending_count > 1 ? 's' : ''; ?> awaiting review.
            <a href="complaints.php?status=pending" class="btn-link">Review Now →</a>
        </div>
    <?php endif; ?>

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
                <p class="stat-change">All time</p>
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
                <p class="stat-change">Needs attention</p>
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
                <p class="stat-change">Being processed</p>
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
                <p class="stat-change">
                    <?php echo $stats['total'] > 0 ? round(($stats['resolved'] / $stats['total']) * 100, 1) : 0; ?>% completion
                </p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Today's Complaints</p>
                <h2 class="stat-value"><?php echo $today_count; ?></h2>
                <p class="stat-change">Submitted today</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Avg. Resolution Time</p>
                <h2 class="stat-value"><?php echo $avg_resolution; ?></h2>
                <p class="stat-change">Days</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">
        <!-- Category Statistics -->
        <div class="chart-section">
            <div class="section-header">
                <h2>Complaints by Category</h2>
            </div>
            <div class="category-chart">
                <?php foreach ($categories as $category): ?>
                    <?php 
                    $percentage = $stats['total'] > 0 ? ($category['count'] / $stats['total']) * 100 : 0;
                    ?>
                    <div class="category-item">
                        <div class="category-info">
                            <span class="category-name"><?php echo htmlspecialchars($category['category']); ?></span>
                            <span class="category-count"><?php echo $category['count']; ?> complaints</span>
                        </div>
                        <div class="category-bar">
                            <div class="category-fill" style="width: <?php echo $percentage; ?>%"></div>
                        </div>
                        <span class="category-percentage"><?php echo round($percentage, 1); ?>%</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Complaints -->
        <div class="recent-section">
            <div class="section-header">
                <h2>Recent Complaints</h2>
                <a href="complaints.php" class="btn-link">View All →</a>
            </div>

            <?php if (mysqli_num_rows($recent_complaints) > 0): ?>
                <div class="complaints-list">
                    <?php while ($complaint = mysqli_fetch_assoc($recent_complaints)): ?>
                        <div class="complaint-card compact">
                            <div class="complaint-header">
                                <div>
                                    <h3><?php echo htmlspecialchars($complaint['title']); ?></h3>
                                    <p class="text-muted">
                                        <?php echo htmlspecialchars($complaint['full_name']); ?> 
                                        (<?php echo htmlspecialchars($complaint['student_id']); ?>)
                                    </p>
                                </div>
                                <span class="badge badge-<?php echo $complaint['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                </span>
                            </div>
                            <div class="complaint-footer">
                                <div class="complaint-meta">
                                    <span class="badge badge-category"><?php echo htmlspecialchars($complaint['category']); ?></span>
                                    <span class="badge badge-priority badge-priority-<?php echo $complaint['priority']; ?>">
                                        <?php echo ucfirst($complaint['priority']); ?>
                                    </span>
                                </div>
                                <a href="complaint-details.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn-link">
                                    View →
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state compact">
                    <p>No complaints found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>
