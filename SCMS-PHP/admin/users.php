<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require admin login
requireLogin('admin');

$error = '';
$success = '';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $action = sanitizeInput($_POST['action']);
    
    if ($action === 'toggle_status') {
        $new_status = sanitizeInput($_POST['new_status']);
        $query = "UPDATE users SET status = ? WHERE id = ? AND role = 'student'";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "User status updated successfully!";
        } else {
            $error = "Failed to update user status.";
        }
    }
}

// Get filter parameters
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : 'all';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';

// Build query for students only
$query = "SELECT u.*, 
          COUNT(DISTINCT c.id) as total_complaints,
          SUM(CASE WHEN c.status = 'pending' THEN 1 ELSE 0 END) as pending_complaints,
          SUM(CASE WHEN c.status = 'resolved' THEN 1 ELSE 0 END) as resolved_complaints
          FROM users u
          LEFT JOIN complaints c ON u.id = c.user_id
          WHERE u.role = 'student'";

$params = [];
$types = "";

if ($status_filter !== 'all') {
    $query .= " AND u.status = ?";
    $params[] = $status_filter;
    $types .= "s";
}

if (!empty($search)) {
    $query .= " AND (u.full_name LIKE ? OR u.email LIKE ? OR u.student_id LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "sss";
}

$query .= " GROUP BY u.id";

// Sorting
switch ($sort) {
    case 'name':
        $query .= " ORDER BY u.full_name ASC";
        break;
    case 'complaints':
        $query .= " ORDER BY total_complaints DESC";
        break;
    case 'oldest':
        $query .= " ORDER BY u.created_at ASC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY u.created_at DESC";
        break;
}

if (!empty($params)) {
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $users = mysqli_stmt_get_result($stmt);
} else {
    $users = mysqli_query($conn, $query);
}

// Get total user statistics
$stats_query = "SELECT 
                COUNT(*) as total_students,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_students,
                SUM(CASE WHEN status = 'suspended' THEN 1 ELSE 0 END) as suspended_students
                FROM users WHERE role = 'student'";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

$page_title = "Manage Users";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Manage Users</h1>
            <p class="text-muted">View and manage student accounts</p>
        </div>
        <a href="dashboard.php" class="btn btn-secondary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                <path d="M9 22V12h6v10"/>
            </svg>
            Dashboard
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <!-- User Statistics -->
    <div class="stats-grid stats-grid-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Total Students</p>
                <h2 class="stat-value"><?php echo $stats['total_students']; ?></h2>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Active Students</p>
                <h2 class="stat-value"><?php echo $stats['active_students']; ?></h2>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                </svg>
            </div>
            <div class="stat-content">
                <p class="stat-label">Suspended Students</p>
                <h2 class="stat-value"><?php echo $stats['suspended_students']; ?></h2>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" class="filters-form" id="filtersForm">
            <div class="filter-group">
                <label for="search">Search</label>
                <input type="text" id="search" name="search" 
                       placeholder="Search by name, email, or student ID..." 
                       value="<?php echo htmlspecialchars($search); ?>">
            </div>

            <div class="filter-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="suspended" <?php echo $status_filter === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                </select>
            </div>

            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name (A-Z)</option>
                    <option value="complaints" <?php echo $sort === 'complaints' ? 'selected' : ''; ?>>Most Complaints</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
                <a href="users.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Users List -->
    <div class="users-section">
        <div class="section-header">
            <h2>All Students (<?php echo mysqli_num_rows($users); ?>)</h2>
        </div>

        <?php if (mysqli_num_rows($users) > 0): ?>
            <div class="users-table-container">
                <table class="complaints-table">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Complaints</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($user['student_id']); ?></strong></td>
                                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['phone'] ? htmlspecialchars($user['phone']) : '<span class="text-muted">N/A</span>'; ?></td>
                                <td>
                                    <div class="complaint-stats">
                                        <span class="badge badge-secondary"><?php echo $user['total_complaints']; ?> Total</span>
                                        <?php if ($user['pending_complaints'] > 0): ?>
                                            <span class="badge badge-pending"><?php echo $user['pending_complaints']; ?> Pending</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-<?php echo $user['status']; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted"><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="new_status" value="<?php echo $user['status'] === 'active' ? 'suspended' : 'active'; ?>">
                                            <button type="submit" class="btn btn-sm <?php echo $user['status'] === 'active' ? 'btn-danger' : 'btn-success'; ?>">
                                                <?php echo $user['status'] === 'active' ? 'Suspend' : 'Activate'; ?>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile View Cards -->
            <div class="users-list mobile-only">
                <?php mysqli_data_seek($users, 0); ?>
                <?php while ($user = mysqli_fetch_assoc($users)): ?>
                    <div class="user-card">
                        <div class="user-header">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($user['full_name'], 0, 2)); ?>
                            </div>
                            <div class="user-info">
                                <h3><?php echo htmlspecialchars($user['full_name']); ?></h3>
                                <p class="text-muted"><?php echo htmlspecialchars($user['student_id']); ?></p>
                            </div>
                            <span class="badge badge-<?php echo $user['status']; ?>">
                                <?php echo ucfirst($user['status']); ?>
                            </span>
                        </div>
                        <div class="user-details">
                            <div class="user-detail-item">
                                <span class="detail-label">Email:</span>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <?php if ($user['phone']): ?>
                                <div class="user-detail-item">
                                    <span class="detail-label">Phone:</span>
                                    <span><?php echo htmlspecialchars($user['phone']); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="user-detail-item">
                                <span class="detail-label">Complaints:</span>
                                <span>
                                    <span class="badge badge-secondary"><?php echo $user['total_complaints']; ?> Total</span>
                                    <?php if ($user['pending_complaints'] > 0): ?>
                                        <span class="badge badge-pending"><?php echo $user['pending_complaints']; ?> Pending</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                            <div class="user-detail-item">
                                <span class="detail-label">Joined:</span>
                                <span><?php echo date('M j, Y', strtotime($user['created_at'])); ?></span>
                            </div>
                        </div>
                        <div class="user-actions">
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="action" value="toggle_status">
                                <input type="hidden" name="new_status" value="<?php echo $user['status'] === 'active' ? 'suspended' : 'active'; ?>">
                                <button type="submit" class="btn btn-sm btn-block <?php echo $user['status'] === 'active' ? 'btn-danger' : 'btn-success'; ?>">
                                    <?php echo $user['status'] === 'active' ? 'Suspend User' : 'Activate User'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                </svg>
                <h3>No Users Found</h3>
                <p>No users match your current filters. Try adjusting your search criteria.</p>
                <a href="users.php" class="btn btn-secondary">Clear Filters</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php
include '../includes/footer.php';
?>
