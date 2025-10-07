<?php
// Database configuration constants
define('DB_HOST', 'localhost');      // Database server
define('DB_USER', 'root');           // Database username
define('DB_PASS', '');               // Database password
define('DB_NAME', 'university_registration'); // Database name

/**
 * Establishes connection to MySQL database
 * @return mysqli|false Returns connection object or false on failure
 */
function getDBConnection() {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    return $conn;
}

/**
 * Closes database connection
 * @param mysqli $conn Database connection object
 */
function closeDBConnection($conn) {
    mysqli_close($conn);
}

/**
 * Redirects to specified URL
 * @param string $url The URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit();
}
?>