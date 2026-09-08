<?php
// Utility functions for SCMS

// Sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Generate unique complaint ID
function generateComplaintId($conn) {
    $query = "SELECT MAX(id) as max_id FROM complaints";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    $nextId = ($row['max_id'] ?? 0) + 1;
    return 'SCMS-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
}

// Get status badge class
function getStatusBadgeClass($status) {
    $classes = [
        'Pending' => 'status-pending',
        'Under Review' => 'status-review',
        'In Progress' => 'status-progress',
        'Resolved' => 'status-resolved',
        'Rejected' => 'status-rejected'
    ];
    return $classes[$status] ?? 'status-pending';
}

// Get priority badge class
function getPriorityBadgeClass($priority) {
    $classes = [
        'Low' => 'priority-low',
        'Medium' => 'priority-medium',
        'High' => 'priority-high',
        'Critical' => 'priority-critical'
    ];
    return $classes[$priority] ?? 'priority-medium';
}

// Format date
function formatDate($date) {
    return date('d M Y', strtotime($date));
}

// Format datetime
function formatDateTime($datetime) {
    return date('d M Y, h:i A', strtotime($datetime));
}

// Time ago function
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($datetime);
    }
}

// Upload file
function uploadFile($file, $uploadDir = '../uploads/') {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds 5MB'];
    }
    
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('complaint_') . '.' . $extension;
    $destination = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

// Get complaint statistics
function getComplaintStats($conn, $userId = null) {
    $where = $userId ? "WHERE user_id = " . intval($userId) : "";
    
    $query = "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status IN ('Under Review', 'In Progress') THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN status = 'Resolved' THEN 1 ELSE 0 END) as resolved,
        SUM(CASE WHEN priority = 'Critical' THEN 1 ELSE 0 END) as critical
        FROM complaints $where";
    
    $result = $conn->query($query);
    return $result->fetch_assoc();
}

// Escape output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Set flash message
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Get and clear flash message
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Add complaint update/timeline entry
function addComplaintUpdate($conn, $complaintId, $status, $message, $updatedBy) {
    $stmt = $conn->prepare("INSERT INTO complaint_updates (complaint_id, status, message, updated_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $complaintId, $status, $message, $updatedBy);
    return $stmt->execute();
}
?>
