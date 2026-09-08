<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require student login
requireLogin('student');

$user_id = $_SESSION['user_id'];

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
$category_filter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : 'all';
$priority_filter = isset($_GET['priority']) ? sanitizeInput($_GET['priority']) : 'all';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Build query
$query = "SELECT * FROM complaints WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($status_filter !== 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($category_filter !== 'all') {
    $query .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

if ($priority_filter !== 'all') {
    $query .= " AND priority = ?";
    $params[] = $priority_filter;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND (title LIKE ? OR description LIKE ? OR complaint_id LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

$query .= " ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$complaints = mysqli_stmt_get_result($stmt);

$page_title = "My Complaints";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>My Complaints</h1>
            <p class="text-muted">View and manage all your submitted complaints</p>
        </div>
        <a href="submit-complaint.php" class="btn btn-primary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Submit New Complaint
        </a>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" class="filters-form" id="filtersForm">
            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" placeholder="Search by title, ID or description..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $status_filter === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="resolved" <?php echo $status_filter === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="rejected" <?php echo $status_filter === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="all" <?php echo $category_filter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <option value="Infrastructure" <?php echo $category_filter === 'Infrastructure' ? 'selected' : ''; ?>>Infrastructure</option>
                    <option value="Academics" <?php echo $category_filter === 'Academics' ? 'selected' : ''; ?>>Academics</option>
                    <option value="Facilities" <?php echo $category_filter === 'Facilities' ? 'selected' : ''; ?>>Facilities</option>
                    <option value="Transport" <?php echo $category_filter === 'Transport' ? 'selected' : ''; ?>>Transport</option>
                    <option value="Hostel" <?php echo $category_filter === 'Hostel' ? 'selected' : ''; ?>>Hostel</option>
                    <option value="Library" <?php echo $category_filter === 'Library' ? 'selected' : ''; ?>>Library</option>
                    <option value="Canteen" <?php echo $category_filter === 'Canteen' ? 'selected' : ''; ?>>Canteen</option>
                    <option value="Sports" <?php echo $category_filter === 'Sports' ? 'selected' : ''; ?>>Sports</option>
                    <option value="Other" <?php echo $category_filter === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="all" <?php echo $priority_filter === 'all' ? 'selected' : ''; ?>>All Priorities</option>
                    <option value="low" <?php echo $priority_filter === 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo $priority_filter === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo $priority_filter === 'high' ? 'selected' : ''; ?>>High</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="my-complaints.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Complaints List -->
    <div class="complaints-section">
        <div class="section-header">
            <h2>All Complaints (<?php echo mysqli_num_rows($complaints); ?>)</h2>
        </div>

        <?php if (mysqli_num_rows($complaints) > 0): ?>
            <div class="complaints-list">
                <?php while ($complaint = mysqli_fetch_assoc($complaints)): ?>
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
                            <p><?php echo nl2br(htmlspecialchars(substr($complaint['description'], 0, 200))); ?>
                                <?php echo strlen($complaint['description']) > 200 ? '...' : ''; ?>
                            </p>
                        </div>
                        <div class="complaint-footer">
                            <div class="complaint-meta">
                                <span class="badge badge-category"><?php echo htmlspecialchars($complaint['category']); ?></span>
                                <span class="badge badge-priority badge-priority-<?php echo $complaint['priority']; ?>">
                                    <?php echo ucfirst($complaint['priority']); ?> Priority
                                </span>
                                <?php if ($complaint['department']): ?>
                                    <span class="badge badge-department">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                                        </svg>
                                        <?php echo htmlspecialchars($complaint['department']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="complaint-actions">
                                <span class="text-muted">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <path d="M12 6v6l4 2"/>
                                    </svg>
                                    <?php echo timeAgo($complaint['created_at']); ?>
                                </span>
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
                    <circle cx="11" cy="11" r="8"/>
                    <path d="M21 21l-4.35-4.35"/>
                </svg>
                <h3>No Complaints Found</h3>
                <p>No complaints match your current filters. Try adjusting your search criteria.</p>
                <a href="my-complaints.php" class="btn btn-secondary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
include '../includes/footer.php';
?>
