<?php
session_start();

// Ensure user is logged in as HR
if ($_SESSION['role'] !== 'hr') {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Validate and sanitize input
if (isset($_GET['application_id']) && isset($_GET['action'])) {
    $application_id = (int) $_GET['application_id']; // Ensuring application_id is an integer
    $action = $_GET['action'];

    
    if (!in_array($action, ['accept', 'reject'])) {
        $_SESSION['message'] = "Invalid action.";
        $_SESSION['message_type'] = "danger";
        header("Location: hr-dashboard.php");
        exit;
    }

    
    $status = ($action === 'accept') ? 'accepted' : 'rejected';

    try {
        
        $stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
        $stmt->execute([$status, $application_id]);

        // Set success message
        $_SESSION['message'] = "Application has been " . $status . ".";
        $_SESSION['message_type'] = "success";

    } catch (PDOException $e) {
        // Handle error during database operation
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['message_type'] = "danger";
    }

    // Redirect to the previous page or a fallback page
    header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'hr-dashboard.php'));
    exit;
} else {
    // Handle case where parameters are not set
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = "danger";
    header("Location: hr-dashboard.php");
    exit;
}
