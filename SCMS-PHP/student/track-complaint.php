<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require student login
requireLogin('student');

$user_id = $_SESSION['user_id'];
$complaint = null;
$updates = [];
$error = '';

if (isset($_GET['id'])) {
    $complaint_id = sanitizeInput($_GET['id']);
    
    // Get complaint details (ensure it belongs to the logged-in student)
    $query = "SELECT c.*, u.full_name, u.email, u.student_id 
              FROM complaints c 
              JOIN users u ON c.user_id = u.id 
              WHERE c.complaint_id = ? AND c.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $complaint_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $complaint = mysqli_fetch_assoc($result);
        
        // Get complaint updates
        $updates_query = "SELECT * FROM complaint_updates WHERE complaint_id = ? ORDER BY created_at ASC";
        $updates_stmt = mysqli_prepare($conn, $updates_query);
        mysqli_stmt_bind_param($updates_stmt, "s", $complaint_id);
        mysqli_stmt_execute($updates_stmt);
        $updates_result = mysqli_stmt_get_result($updates_stmt);
        
        while ($update = mysqli_fetch_assoc($updates_result)) {
            $updates[] = $update;
        }
    } else {
        $error = "Complaint not found or you don't have permission to view it.";
    }
}

$page_title = "Track Complaint";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Track Complaint</h1>
            <p class="text-muted">View complaint details and track its progress</p>
        </div>
        <a href="my-complaints.php" class="btn btn-secondary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Back to Complaints
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
        
        <div class="search-complaint">
            <form method="GET" class="search-form">
                <input type="text" name="id" placeholder="Enter Complaint ID (e.g., CMP-2024-1234)" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
    <?php elseif ($complaint): ?>
        <!-- Complaint Details -->
        <div class="complaint-details-card">
            <div class="complaint-details-header">
                <div>
                    <h2><?php echo htmlspecialchars($complaint['title']); ?></h2>
                    <p class="complaint-id">Complaint ID: <strong><?php echo htmlspecialchars($complaint['complaint_id']); ?></strong></p>
                </div>
                <span class="badge badge-<?php echo $complaint['status']; ?> badge-large">
                    <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                </span>
            </div>

            <div class="complaint-details-body">
                <div class="detail-section">
                    <h3>Complaint Information</h3>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="detail-label">Category</span>
                            <span class="badge badge-category"><?php echo htmlspecialchars($complaint['category']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Priority</span>
                            <span class="badge badge-priority badge-priority-<?php echo $complaint['priority']; ?>">
                                <?php echo ucfirst($complaint['priority']); ?>
                            </span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="badge badge-<?php echo $complaint['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                            </span>
                        </div>
                        <?php if ($complaint['department']): ?>
                            <div class="detail-item">
                                <span class="detail-label">Assigned Department</span>
                                <span class="badge badge-department"><?php echo htmlspecialchars($complaint['department']); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($complaint['location']): ?>
                            <div class="detail-item">
                                <span class="detail-label">Location</span>
                                <span><?php echo htmlspecialchars($complaint['location']); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <span class="detail-label">Submitted On</span>
                            <span><?php echo date('F j, Y g:i A', strtotime($complaint['created_at'])); ?></span>
                        </div>
                        <?php if ($complaint['resolved_at']): ?>
                            <div class="detail-item">
                                <span class="detail-label">Resolved On</span>
                                <span><?php echo date('F j, Y g:i A', strtotime($complaint['resolved_at'])); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="detail-section">
                    <h3>Description</h3>
                    <div class="description-box">
                        <p><?php echo nl2br(htmlspecialchars($complaint['description'])); ?></p>
                    </div>
                </div>

                <?php if ($complaint['attachment']): ?>
                    <div class="detail-section">
                        <h3>Attachment</h3>
                        <div class="attachment-box">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                            </svg>
                            <a href="../uploads/<?php echo htmlspecialchars($complaint['attachment']); ?>" target="_blank" class="btn-link">
                                View Attachment
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($complaint['admin_remarks']): ?>
                    <div class="detail-section">
                        <h3>Admin Remarks</h3>
                        <div class="remarks-box">
                            <p><?php echo nl2br(htmlspecialchars($complaint['admin_remarks'])); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Timeline -->
        <div class="timeline-section">
            <h2>Complaint Timeline</h2>
            <div class="timeline">
                <!-- Initial Submission -->
                <div class="timeline-item">
                    <div class="timeline-marker marker-pending"></div>
                    <div class="timeline-content">
                        <div class="timeline-header">
                            <h3>Complaint Submitted</h3>
                            <span class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($complaint['created_at'])); ?></span>
                        </div>
                        <p>Your complaint has been submitted and is waiting for review by the administration.</p>
                    </div>
                </div>

                <!-- Updates -->
                <?php foreach ($updates as $update): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker marker-<?php echo $update['status']; ?>"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h3>
                                    <?php 
                                    if ($update['status'] === 'in_progress') {
                                        echo 'Complaint In Progress';
                                    } elseif ($update['status'] === 'resolved') {
                                        echo 'Complaint Resolved';
                                    } elseif ($update['status'] === 'rejected') {
                                        echo 'Complaint Rejected';
                                    } else {
                                        echo 'Status Updated';
                                    }
                                    ?>
                                </h3>
                                <span class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($update['created_at'])); ?></span>
                            </div>
                            <?php if ($update['remarks']): ?>
                                <p><?php echo nl2br(htmlspecialchars($update['remarks'])); ?></p>
                            <?php endif; ?>
                            <span class="badge badge-<?php echo $update['status']; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $update['status'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Current Status -->
                <?php if ($complaint['status'] === 'resolved'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker marker-resolved marker-current"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h3>✓ Complaint Closed</h3>
                                <span class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($complaint['resolved_at'])); ?></span>
                            </div>
                            <p>Your complaint has been successfully resolved. Thank you for your patience.</p>
                        </div>
                    </div>
                <?php elseif ($complaint['status'] === 'rejected'): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker marker-rejected marker-current"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h3>✗ Complaint Rejected</h3>
                                <span class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($complaint['updated_at'])); ?></span>
                            </div>
                            <p>Your complaint has been reviewed and rejected. Please check admin remarks for more information.</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="timeline-item">
                        <div class="timeline-marker marker-<?php echo $complaint['status']; ?> marker-current"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <h3>Current Status</h3>
                                <span class="badge badge-<?php echo $complaint['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                                </span>
                            </div>
                            <p>
                                <?php 
                                if ($complaint['status'] === 'pending') {
                                    echo "Your complaint is pending review. The administration will process it soon.";
                                } elseif ($complaint['status'] === 'in_progress') {
                                    echo "Your complaint is being actively worked on by the " . ($complaint['department'] ? htmlspecialchars($complaint['department']) . " department" : "administration") . ".";
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="search-complaint">
            <h2>Search Complaint</h2>
            <p>Enter your complaint ID to track its status</p>
            <form method="GET" class="search-form">
                <input type="text" name="id" placeholder="Enter Complaint ID (e.g., CMP-2024-1234)" required>
                <button type="submit" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="M21 21l-4.35-4.35"/>
                    </svg>
                    Search
                </button>
            </form>
        </div>
    <?php endif; ?>
</main>

<?php
include '../includes/footer.php';
?>
