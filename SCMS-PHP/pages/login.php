<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Prevent access if already logged in
preventAuthPages();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role, student_id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                loginUser($user['id'], $user['name'], $user['email'], $user['role'], $user['student_id']);
                
                if ($user['role'] === 'admin') {
                    header('Location: /SCMS-PHP/admin/dashboard.php');
                } else {
                    header('Location: /SCMS-PHP/student/dashboard.php');
                }
                exit();
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Invalid email or password';
        }
    }
}

$pageTitle = 'Login';
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
    max-width: 480px;
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

.demo-credentials {
    background: var(--gray-50);
    padding: var(--space-3);
    border-radius: var(--radius);
    margin-top: var(--space-3);
    font-size: 0.875rem;
}

.demo-credentials strong {
    display: block;
    margin-bottom: var(--space-1);
    color: var(--gray-900);
}
</style>

<div class="auth-container">
    <div class="container container-sm">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon">🔐</div>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Login to manage your complaints</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo e($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label" for="email">College Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="your.name@college.edu" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">Login</button>
                </div>
            </form>
            
            <div class="form-footer">
                <p>Don't have an account? <a href="signup.php" style="font-weight: 600;">Create one</a></p>
            </div>
            
            <div class="demo-credentials">
                <strong>Demo Credentials:</strong>
                <div style="margin-bottom: 0.5rem;">
                    <strong style="font-size: 0.8125rem; font-weight: 500;">Student:</strong> john.doe@college.edu / student123
                </div>
                <div>
                    <strong style="font-size: 0.8125rem; font-weight: 500;">Admin:</strong> admin@college.edu / admin123
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
