<?php
// Prevent direct script access
if (!defined('DB_CONNECT_INCLUDED')) {
    define('DB_CONNECT_INCLUDED', true);

    // Database configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'expense_management');

    // Error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Establish database connection
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Select the database
    if (!mysqli_select_db($con, DB_NAME)) {
        die("Database selection failed: " . mysqli_error($con));
    }

    // Set charset
    if (!mysqli_set_charset($con, "utf8")) {
        die("Error setting charset: " . mysqli_error($con));
    }

    // Function to verify active connection
    if (!function_exists('verify_connection')) {
        function verify_connection() {
            global $con;
            if (!$con || !mysqli_ping($con)) {
                $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                if (!$con) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                mysqli_set_charset($con, "utf8");
            }
            return $con;
        }
    }
}

// Session is already started in index.php
?>