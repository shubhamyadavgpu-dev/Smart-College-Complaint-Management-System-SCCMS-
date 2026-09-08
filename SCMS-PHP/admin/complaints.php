<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require admin login
requireLogin('admin');

// Get filter parameters
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
$category_filter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : 'all';
$priority_filter = isset($_GET['priority']) ? sanitizeInput($_GET['priority']) : 'all';
$department_filter = isset($_GET['department']) ? sanitizeInput($_GET['department']) : 'all';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';

// Build query
$query = "SELECT c.*, u.full_name, u.email, u.student_id 
          FROM complaints c 
          JOIN users u ON c.user_id = u.id 
          WHERE 1=1";
$params = [];
$types = "";

if ($status_filter !== 'all') {
    $query .= " AND c.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if ($category_filter !== 'all') {
    $query .= " AND c.category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

if ($priority_filter !== 'all') {
    $query .= " AND c.priority = ?";
    $params[] = $priority_filter;
    $types .= "s";
}

if ($department_filter !== 'all') {
    $query .= " AND c.department = ?";
    $params[] = $department_filter;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND (c.title LIKE ? OR c.description LIKE ? OR c.complaint_id LIKE ? OR u.full_name LIKE ? OR u.student_id LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sssss";
}

// Sorting
switch ($sort) {
    case 'oldest':
        $query .= " ORDER BY c.created_at ASC";
        break;
    case 'priority':
        $query .= " ORDER BY FIELD(c.priority, 'high', 'medium', 'low'), c.created_at DESC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY c.created_at DESC";
        break;
}

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $complaints = mysqli_stmt_get_result($stmt);
} else {
    $complaints = mysqli_query($conn, $query);
}

$page_title = "Manage Complaints";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Manage Complaints</h1>
            <p class="text-muted">View, filter, and manage all student complaints</p>
        </div>
        <a href="dashboard.php" class="btn btn-secondary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" class="filters-form" id="filtersForm">
            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" 
                       placeholder="Search by title, ID, student name..." 
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

            <div class="filter-group">
                <label for="department">Department</label>
                <select id="department" name="department">
                    <option value="all" <?php echo $department_filter === 'all' ? 'selected' : ''; ?>>All Departments</option>
                    <option value="Maintenance" <?php echo $department_filter === 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="Academic Affairs" <?php echo $department_filter === 'Academic Affairs' ? 'selected' : ''; ?>>Academic Affairs</option>
                    <option value="Student Affairs" <?php echo $department_filter === 'Student Affairs' ? 'selected' : ''; ?>>Student Affairs</option>
                    <option value="IT Department" <?php echo $department_filter === 'IT Department' ? 'selected' : ''; ?>>IT Department</option>
                    <option value="Security" <?php echo $department_filter === 'Security' ? 'selected' : ''; ?>>Security</option>
                    <option value="Administration" <?php echo $department_filter === 'Administration' ? 'selected' : ''; ?>>Administration</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="priority" <?php echo $sort === 'priority' ? 'selected' : ''; ?>>Priority</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="complaints.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Complaints List -->
    <div class="complaints-section">
        <div class="section-header">
            <h2>All Complaints (<?php echo mysqli_num_rows($complaints); ?>)</h2>
        </div>

        <?php if (mysqli_num_rows($complaints) > 0): ?>
            <div class="complaints-table-container">
                <table class="complaints-table">
                    <thead>
                        <tr>
                            <th>Complaint ID</th>
                            <th>Title</th>
                            <th>Student</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Department</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($complaint = mysqli_fetch_assoc($complaints)): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($complaint['complaint_id']); ?></strong>
                                </td>
                                <td>
                                    <div class="table-title">
                                        <?php echo htmlspecialchars(substr($complaint['title'], 0, 50)); ?>
                                        <?php echo strlen($complaint['title']) > 50 ? '...' : ''; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="student-info">
                                        <span class="student-name"><?php echo htmlspecialchars($complaint['full_name']); ?></span>
                                        <span class="student-id"><?php echo htmlspecialchars($complaint['student_id']); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-category">
                                        <?php echo htmlspecialchars($complaint['category']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-priority badge-priority-<?php echo $complaint['priority']; ?>">
                                        <?php echo ucfirst($complaint['priority']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $complaint['status']; ?>">
                                        <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($complaint['department']): ?>
                                        <span class="badge badge-department">
                                            <?php echo htmlspecialchars($complaint['department']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">Not Assigned</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-muted"><?php echo timeAgo($complaint['created_at']); ?></span>
                                </td>
                                <td>
                                    <a href="complaint-details.php?id=<?php echo $complaint['complaint_id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View Cards -->
            <div class="complaints-list mobile-only">
                <?php mysqli_data_seek($complaints, 0); ?>
                <?php while ($complaint = mysqli_fetch_assoc($complaints)): ?>
                    <div class="complaint-card">
                        <div class="complaint-header">
                            <div>
                                <h3><?php echo htmlspecialchars($complaint['title']); ?></h3>
                                <p class="complaint-id">ID: <?php echo htmlspecialchars($complaint['complaint_id']); ?></p>
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
                                <?php if ($complaint['department']): ?>
                                    <span class="badge badge-department"><?php echo htmlspecialchars($complaint['department']); ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="complaint-details.php?id=<?php echo $complaint['complaint_id']; ?>" class="btn-link">
                                View Details →
                            </a>
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
                <a href="complaints.php" class="btn btn-secondary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
include '../includes/footer.php';
?>
