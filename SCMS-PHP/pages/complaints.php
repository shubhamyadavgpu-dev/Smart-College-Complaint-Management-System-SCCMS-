<?php
$pageTitle = 'Browse Complaints';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Get filter parameters
$search = sanitize($_GET['search'] ?? '');
$category = sanitize($_GET['category'] ?? '');
$status = sanitize($_GET['status'] ?? '');
$priority = sanitize($_GET['priority'] ?? '');

// Build query
$query = "SELECT c.*, u.name as user_name FROM complaints c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.is_anonymous = 0";

$params = [];
$types = '';

if ($search) {
    $query .= " AND (c.title LIKE ? OR c.description LIKE ? OR c.complaint_id LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= 'sss';
}

if ($category) {
    $query .= " AND c.category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($status) {
    $query .= " AND c.status = ?";
    $params[] = $status;
    $types .= 's';
}

if ($priority) {
    $query .= " AND c.priority = ?";
    $params[] = $priority;
    $types .= 's';
}

$query .= " ORDER BY c.created_at DESC LIMIT 50";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
.page-header {
    background: var(--white);
    padding: var(--space-6) 0;
    border-bottom: 1px solid var(--gray-200);
}

.complaints-list {
    display: grid;
    gap: var(--space-3);
}
</style>

<div class="page-header">
    <div class="container">
        <h1 class="dashboard-title">Browse Complaints</h1>
        <p class="dashboard-subtitle">View and track reported campus issues</p>
    </div>
</div>

<div class="container py-6">
    <!-- Filters -->
    <div class="filters">
        <form method="GET" action="">
            <div class="filters-grid">
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" name="search" class="form-control" placeholder="Search complaints..." value="<?php echo e($search); ?>">
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <option value="Academics" <?php echo $category === 'Academics' ? 'selected' : ''; ?>>Academics</option>
                        <option value="Infrastructure" <?php echo $category === 'Infrastructure' ? 'selected' : ''; ?>>Infrastructure</option>
                        <option value="Hostel" <?php echo $category === 'Hostel' ? 'selected' : ''; ?>>Hostel</option>
                        <option value="Library" <?php echo $category === 'Library' ? 'selected' : ''; ?>>Library</option>
                        <option value="Transport" <?php echo $category === 'Transport' ? 'selected' : ''; ?>>Transport</option>
                        <option value="Cleanliness" <?php echo $category === 'Cleanliness' ? 'selected' : ''; ?>>Cleanliness</option>
                        <option value="IT" <?php echo $category === 'IT' ? 'selected' : ''; ?>>IT</option>
                        <option value="Administration" <?php echo $category === 'Administration' ? 'selected' : ''; ?>>Administration</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="Pending" <?php echo $status === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="Under Review" <?php echo $status === 'Under Review' ? 'selected' : ''; ?>>Under Review</option>
                        <option value="In Progress" <?php echo $status === 'In Progress' ? 'selected' : ''; ?>>In Progress</option>
                        <option value="Resolved" <?php echo $status === 'Resolved' ? 'selected' : ''; ?>>Resolved</option>
                    </select>
                </div>
                
                <div class="form-group" style="margin-bottom: 0;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Complaints List -->
    <div class="complaints-list">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($complaint = $result->fetch_assoc()): ?>
                <div class="complaint-card">
                    <div class="complaint-header">
                        <span class="complaint-id"><?php echo e($complaint['complaint_id']); ?></span>
                        <span class="badge <?php echo getStatusBadgeClass($complaint['status']); ?>">
                            <?php echo e($complaint['status']); ?>
                        </span>
                    </div>
                    
                    <h3 class="complaint-title"><?php echo e($complaint['title']); ?></h3>
                    
                    <div class="complaint-meta">
                        <span class="badge"><?php echo e($complaint['category']); ?></span>
                        <span class="badge <?php echo getPriorityBadgeClass($complaint['priority']); ?>">
                            <?php echo e($complaint['priority']); ?>
                        </span>
                        <span style="color: var(--gray-600); font-size: 0.875rem;">
                            Submitted <?php echo timeAgo($complaint['created_at']); ?>
                        </span>
                    </div>
                    
                    <div class="complaint-footer">
                        <span style="color: var(--gray-600); font-size: 0.875rem;">
                            by <?php echo e($complaint['user_name']); ?>
                        </span>
                        <a href="complaint-details.php?id=<?php echo $complaint['id']; ?>" class="btn btn-ghost btn-sm">
                            View Details →
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <h3 class="empty-state-title">No Complaints Found</h3>
                <p class="empty-state-text">Try adjusting your filters or search criteria</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
