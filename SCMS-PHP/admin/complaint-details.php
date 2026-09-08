<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require admin login
requireLogin('admin');

$complaint = null;
$student = null;
$updates = [];
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint_id = sanitizeInput($_POST['complaint_id']);
    $action = sanitizeInput($_POST['action']);
    
    if ($action === 'assign_department') {
        $department = sanitizeInput($_POST['department']);
        $query = "UPDATE complaints SET department = ? WHERE complaint_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $department, $complaint_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Department assigned successfully!";
        } else {
            $error = "Failed to assign department.";
        }
    } elseif ($action === 'update_status') {
        $new_status = sanitizeInput($_POST['status']);
        $remarks = sanitizeInput($_POST['remarks']);
        
        // Update complaint status
        $query = "UPDATE complaints SET status = ?, admin_remarks = ?, updated_at = NOW()";
        if ($new_status === 'resolved') {
            $query .= ", resolved_at = NOW()";
        }
        $query .= " WHERE complaint_id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sss", $new_status, $remarks, $complaint_id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Add update entry
            $update_query = "INSERT INTO complaint_updates (complaint_id, status, remarks) VALUES (?, ?, ?)";
            $update_stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($update_stmt, "sss", $complaint_id, $new_status, $remarks);
            mysqli_stmt_execute($update_stmt);
            
            $success = "Complaint status updated successfully!";
        } else {
            $error = "Failed to update status.";
        }
    } elseif ($action === 'update_priority') {
        $priority = sanitizeInput($_POST['priority']);
        $query = "UPDATE complaints SET priority = ? WHERE complaint_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ss", $priority, $complaint_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Priority updated successfully!";
        } else {
            $error = "Failed to update priority.";
        }
    }
}

// Get complaint details
if (isset($_GET['id'])) {
    $complaint_id = sanitizeInput($_GET['id']);
    
    $query = "SELECT c.*, u.full_name, u.email, u.student_id, u.phone 
              FROM complaints c 
              JOIN users u ON c.user_id = u.id 
              WHERE c.complaint_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $complaint_id);
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
        $error = "Complaint not found.";
    }
}

$page_title = "Complaint Details";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Complaint Details</h1>
            <p class="text-muted">View and manage complaint information</p>
        </div>
        <a href="complaints.php" class="btn btn-secondary">
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
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if ($complaint): ?>
        <div class="details-layout">
            <!-- Left Column: Complaint Details -->
            <div class="details-main">
                <div class="complaint-details-card">
                    <div class="complaint-details-header">
                        <div>
                            <h2><?php echo htmlspecialchars($complaint['title']); ?></h2>
                            <p class="complaint-id">ID: <strong><?php echo htmlspecialchars($complaint['complaint_id']); ?></strong></p>
                        </div>
                        <span class="badge badge-<?php echo $complaint['status']; ?> badge-large">
                            <?php echo ucfirst(str_replace('_', ' ', $complaint['status'])); ?>
                        </span>
                    </div>

                    <div class="complaint-details-body">
                        <div class="detail-section">
                            <h3>Student Information</h3>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <span class="detail-label">Full Name</span>
                                    <span><?php echo htmlspecialchars($complaint['full_name']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Student ID</span>
                                    <span><?php echo htmlspecialchars($complaint['student_id']); ?></span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Email</span>
                                    <span><?php echo htmlspecialchars($complaint['email']); ?></span>
                                </div>
                                <?php if ($complaint['phone']): ?>
                                    <div class="detail-item">
                                        <span class="detail-label">Phone</span>
                                        <span><?php echo htmlspecialchars($complaint['phone']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

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
                                <div class="detail-item">
                                    <span class="detail-label">Department</span>
                                    <?php if ($complaint['department']): ?>
                                        <span class="badge badge-department"><?php echo htmlspecialchars($complaint['department']); ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Not Assigned</span>
                                    <?php endif; ?>
                                </div>
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
                                <div class="detail-item">
                                    <span class="detail-label">Last Updated</span>
                                    <span><?php echo timeAgo($complaint['updated_at']); ?></span>
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
                        <div class="timeline-item">
                            <div class="timeline-marker marker-pending"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <h3>Complaint Submitted</h3>
                                    <span class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($complaint['created_at'])); ?></span>
                                </div>
                                <p>Complaint submitted by <?php echo htmlspecialchars($complaint['full_name']); ?></p>
                            </div>
                        </div>

                        <?php foreach ($updates as $update): ?>
                            <div class="timeline-item">
                                <div class="timeline-marker marker-<?php echo $update['status']; ?>"></div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h3><?php echo ucfirst(str_replace('_', ' ', $update['status'])); ?></h3>
                                        <span class="timeline-date"><?php echo date('M j, Y g:i A', strtotime($update['created_at'])); ?></span>
                                    </div>
                                    <?php if ($update['remarks']): ?>
                                        <p><?php echo nl2br(htmlspecialchars($update['remarks'])); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column: Actions -->
            <div class="details-sidebar">
                <!-- Assign Department -->
                <div class="action-card">
                    <h3>Assign Department</h3>
                    <form method="POST" class="action-form">
                        <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($complaint['complaint_id']); ?>">
                        <input type="hidden" name="action" value="assign_department">
                        <div class="form-group">
                            <label for="department">Department</label>
                            <select id="department" name="department" required>
                                <option value="">Select Department</option>
                                <option value="Maintenance" <?php echo $complaint['department'] === 'Maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                                <option value="Academic Affairs" <?php echo $complaint['department'] === 'Academic Affairs' ? 'selected' : ''; ?>>Academic Affairs</option>
                                <option value="Student Affairs" <?php echo $complaint['department'] === 'Student Affairs' ? 'selected' : ''; ?>>Student Affairs</option>
                                <option value="IT Department" <?php echo $complaint['department'] === 'IT Department' ? 'selected' : ''; ?>>IT Department</option>
                                <option value="Security" <?php echo $complaint['department'] === 'Security' ? 'selected' : ''; ?>>Security</option>
                                <option value="Administration" <?php echo $complaint['department'] === 'Administration' ? 'selected' : ''; ?>>Administration</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Assign</button>
                    </form>
                </div>

                <!-- Update Priority -->
                <div class="action-card">
                    <h3>Update Priority</h3>
                    <form method="POST" class="action-form">
                        <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($complaint['complaint_id']); ?>">
                        <input type="hidden" name="action" value="update_priority">
                        <div class="form-group">
                            <label for="priority">Priority Level</label>
                            <select id="priority" name="priority" required>
                                <option value="low" <?php echo $complaint['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                                <option value="medium" <?php echo $complaint['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="high" <?php echo $complaint['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Priority</button>
                    </form>
                </div>

                <!-- Update Status -->
                <div class="action-card">
                    <h3>Update Status</h3>
                    <form method="POST" class="action-form">
                        <input type="hidden" name="complaint_id" value="<?php echo htmlspecialchars($complaint['complaint_id']); ?>">
                        <input type="hidden" name="action" value="update_status">
                        <div class="form-group">
                            <label for="status">New Status</label>
                            <select id="status" name="status" required>
                                <option value="pending" <?php echo $complaint['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo $complaint['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="resolved" <?php echo $complaint['status'] === 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                <option value="rejected" <?php echo $complaint['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea id="remarks" name="remarks" rows="4" placeholder="Add remarks about this status update..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php
include '../includes/footer.php';
?>
