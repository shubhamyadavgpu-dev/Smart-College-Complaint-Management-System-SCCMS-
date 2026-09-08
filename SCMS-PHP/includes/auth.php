<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Check if user is student
function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

// Require login - redirect to login page if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /SCMS-PHP/pages/login.php');
        exit();
    }
}

// Require admin - redirect if not admin
function requireAdmin() {
    if (!isLoggedIn()) {
        header('Location: /SCMS-PHP/pages/login.php');
        exit();
    }
    if (!isAdmin()) {
        header('Location: /SCMS-PHP/pages/home.php');
        exit();
    }
}

// Require student - redirect if not student
function requireStudent() {
    if (!isLoggedIn()) {
        header('Location: /SCMS-PHP/pages/login.php');
        exit();
    }
    if (!isStudent()) {
        header('Location: /SCMS-PHP/pages/home.php');
        exit();
    }
}

// Login user
function loginUser($userId, $name, $email, $role, $studentId = null) {
    $_SESSION['user_id'] = $userId;
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $role;
    $_SESSION['student_id'] = $studentId;
    
    // Regenerate session ID for security
    session_regenerate_id(true);
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
    header('Location: /SCMS-PHP/pages/login.php');
    exit();
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Get current user name
function getCurrentUserName() {
    return $_SESSION['name'] ?? 'Guest';
}

// Get current user role
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

// Prevent access to login/signup if already logged in
function preventAuthPages() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header('Location: /SCMS-PHP/admin/dashboard.php');
        } else {
            header('Location: /SCMS-PHP/student/dashboard.php');
        }
        exit();
    }
}
?>
