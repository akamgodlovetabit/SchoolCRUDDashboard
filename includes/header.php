<?php
// Start session for storing messages across page redirects
session_start();

/**
 * Displays and clears session messages
 * @return string HTML message div or empty string
 */
function displayMessage() {
    // Check if message exists in session
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'success';
        
        // Clear message after displaying
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        
        // Return formatted message
        $class = $type == 'error' ? 'error' : 'success';
        return "<div class='message $class'>" . htmlspecialchars($message) . "</div>";
    }
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Common HTML head content -->
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>University Student Registration System</h1>
        </div>

        <?php echo displayMessage(); ?>

        <div class="nav">
            <!-- Common navigation -->
        </div>