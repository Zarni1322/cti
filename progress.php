<?php
session_start(); // Start the session to access the session variables

header('Content-Type: application/json');

// Check if progress is set in the session and return it
if (isset($_SESSION['progress'])) {
    echo json_encode([
        'success' => true,
        'progress' => $_SESSION['progress'],
        'message' => $_SESSION['progress'] < 100 ? "Processing..." : "All files processed"
    ]);
} else {
    // If no progress is found, return an error
    echo json_encode(['success' => false, 'error' => 'No progress data available']);
}
