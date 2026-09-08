<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

preventAuthPages();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $student_id = sanitize($_POST['student_id'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $department = sanitize($_POST['department'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($name) || empty($student_id) || empty($email) || empty($department) || empty($password)) {
        $error = 'All fields are required';
    } elseif (!isValidEmail($email)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email or student ID already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR student_id = ?");
        $stmt->bind_param("ss", $email, $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = 'Email or Student ID already registered';
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'student';
            
            $stmt = $conn->prepare("INSERT INTO users (name, student_id, email, password, department, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $name, $student_id, $email, $hashed_password, $department, $role);
            
            if ($stmt->execute()) {
                $success = 'Account created successfully! You can now login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

$pageTitle = 'Sign Up';
require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<style>
.auth-container {
    min-height: calc(100vh - 72px - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-6) 0;
}

.auth-card {
    max-width: 600px;
    width: 100%;
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    padding: var(--space-6);
}

.auth-header {
    text-align: center;
    margin-bottom: var(--space-4);
}

.auth-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, var(--primary), var(--primary-light));
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto var(--space-3);
    color: var(--white);
    font-size: 2rem;
}

.auth-title {
    font-size: 1.875rem;
    margin-bottom: var(--space-1);
}

.auth-subtitle {
    color: var(--gray-600);
    font-size: 0.9375rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--space-3);
}

.form-actions {
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
    margin-top: var(--space-4);
}

.form-footer {
    text-align: center;
    margin-top: var(--space-4);
    padding-top: var(--space-4);
    border-top: 1px solid var(--gray-200);
}

@media (max-width: 640px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="auth-container">
    <div class="container container-sm">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">👤</div>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join SCMS to manage complaints efficiently</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo e($success); ?>
                    <a href="login.php" style="font-weight: 600; text-decoration: underline;">Click here to login</a>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="signupForm">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name *</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="John Doe" required value="<?php echo isset($name) ? e($name) : ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="student_id">Student ID *</label>
                        <input type="text" id="student_id" name="student_id" class="form-control" placeholder="STU2024001" required value="<?php echo isset($student_id) ? e($student_id) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="department">Department *</label>
                        <input type="text" id="department" name="department" class="form-control" placeholder="Computer Science" required value="<?php echo isset($department) ? e($department) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">College Email *</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="john.doe@college.edu" required value="<?php echo isset($email) ? e($email) : ''; ?>">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="password">Password *</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
                        <span class="form-hint">At least 6 characters</span>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter password" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                </div>
            </form>
            
            <div class="form-footer">
                <p>Already have an account? <a href="login.php" style="font-weight: 600;">Login here</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
