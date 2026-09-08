<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/database.php';
require_once '../includes/functions.php';

// Require student login
requireLogin('student');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title']);
    $category = sanitizeInput($_POST['category']);
    $priority = sanitizeInput($_POST['priority']);
    $description = sanitizeInput($_POST['description']);
    $location = sanitizeInput($_POST['location']);
    $user_id = $_SESSION['user_id'];
    
    // Validation
    if (empty($title) || empty($category) || empty($priority) || empty($description)) {
        $error = "Please fill in all required fields.";
    } else {
        // Handle file upload
        $attachment = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $upload_result = handleFileUpload($_FILES['attachment']);
            if ($upload_result['success']) {
                $attachment = $upload_result['filename'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        if (empty($error)) {
            // Generate unique complaint ID
            $complaint_id = 'CMP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            // Check if complaint_id already exists
            $check_query = "SELECT id FROM complaints WHERE complaint_id = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "s", $complaint_id);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            // Regenerate if exists
            while (mysqli_num_rows($check_result) > 0) {
                $complaint_id = 'CMP-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                mysqli_stmt_bind_param($check_stmt, "s", $complaint_id);
                mysqli_stmt_execute($check_stmt);
                $check_result = mysqli_stmt_get_result($check_stmt);
            }
            
            // Insert complaint
            $query = "INSERT INTO complaints (complaint_id, user_id, title, category, priority, description, location, attachment, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "sissssss", $complaint_id, $user_id, $title, $category, $priority, $description, $location, $attachment);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Complaint submitted successfully! Your complaint ID is: <strong>$complaint_id</strong>";
                
                // Clear form
                $_POST = array();
            } else {
                $error = "Failed to submit complaint. Please try again.";
            }
        }
    }
}

$page_title = "Submit Complaint";
include '../includes/header.php';
include '../includes/navbar.php';
?>

<main class="dashboard-container">
    <div class="dashboard-header">
        <div>
            <h1>Submit New Complaint</h1>
            <p class="text-muted">Report an issue or complaint to the administration</p>
        </div>
        <a href="my-complaints.php" class="btn btn-secondary">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
            </svg>
            My Complaints
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

    <div class="form-container">
        <form method="POST" enctype="multipart/form-data" class="complaint-form" id="complaintForm">
            <div class="form-section">
                <h2>Complaint Details</h2>
                
                <div class="form-group">
                    <label for="title">Complaint Title <span class="required">*</span></label>
                    <input type="text" id="title" name="title" required
                           placeholder="Brief description of the issue"
                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                    <small>Provide a clear and concise title for your complaint</small>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Category <span class="required">*</span></label>
                        <select id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Infrastructure" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Infrastructure') ? 'selected' : ''; ?>>Infrastructure</option>
                            <option value="Academics" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Academics') ? 'selected' : ''; ?>>Academics</option>
                            <option value="Facilities" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Facilities') ? 'selected' : ''; ?>>Facilities</option>
                            <option value="Transport" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Transport') ? 'selected' : ''; ?>>Transport</option>
                            <option value="Hostel" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Hostel') ? 'selected' : ''; ?>>Hostel</option>
                            <option value="Library" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Library') ? 'selected' : ''; ?>>Library</option>
                            <option value="Canteen" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Canteen') ? 'selected' : ''; ?>>Canteen</option>
                            <option value="Sports" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Sports') ? 'selected' : ''; ?>>Sports</option>
                            <option value="Other" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="priority">Priority <span class="required">*</span></label>
                        <select id="priority" name="priority" required>
                            <option value="">Select Priority</option>
                            <option value="low" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'low') ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo (isset($_POST['priority']) && $_POST['priority'] === 'high') ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location"
                           placeholder="e.g., Main Building, Room 305"
                           value="<?php echo isset($_POST['location']) ? htmlspecialchars($_POST['location']) : ''; ?>">
                    <small>Specify the location where the issue occurred (if applicable)</small>
                </div>

                <div class="form-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <textarea id="description" name="description" rows="6" required
                              placeholder="Provide detailed information about the complaint..."><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    <small>Include all relevant details, such as when it happened, who was involved, and what action you expect</small>
                </div>

                <div class="form-group">
                    <label for="attachment">Attachment (Optional)</label>
                    <input type="file" id="attachment" name="attachment" accept="image/*,.pdf,.doc,.docx">
                    <small>Upload supporting documents or images (Max 5MB, formats: JPG, PNG, PDF, DOC)</small>
                    <div id="filePreview" class="file-preview" style="display: none;">
                        <img id="previewImage" src="" alt="Preview" style="display: none;">
                        <p id="fileName"></p>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
                        <path d="M22 4L12 14.01l-3-3"/>
                    </svg>
                    Submit Complaint
                </button>
                <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <div class="info-box">
            <h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4M12 8h.01"/>
                </svg>
                Important Information
            </h3>
            <ul>
                <li>All fields marked with <span class="required">*</span> are mandatory</li>
                <li>You will receive a unique Complaint ID after submission</li>
                <li>You can track your complaint status using the Complaint ID</li>
                <li>The administration will review and respond within 48 hours</li>
                <li>Attach relevant documents or images to support your complaint</li>
                <li>Provide accurate information for faster resolution</li>
            </ul>
        </div>
    </div>
</main>

<?php
include '../includes/footer.php';
?>
