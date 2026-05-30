<?php
/**
 * Simple script to import the 'tarkina.sql' database dump into MySQL.
 * Ensure that the MySQL server is running and the credentials in db.php are correct.
 */

// Load DB connection parameters without selecting a database first
require_once __DIR__ . '/db.php'; // defines $host, $db_user, $db_pass, $db_name

// Establish a connection without specifying database to create it if missing
$initialConn = mysqli_connect($host, $db_user, $db_pass);
if (!$initialConn) {
    die('Initial connection failed: ' . mysqli_connect_error() . "\n");
}
// Create database if it does not exist
if (!mysqli_query($initialConn, "CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
    die('Failed to create database: ' . mysqli_error($initialConn) . "\n");
}
mysqli_close($initialConn);

// Now include original connection (which will connect to the newly ensured database)
require_once __DIR__ . '/db.php'; // $conn is defined here

// Ensure the import file exists
$sqlFile = __DIR__ . '/database/tarkina.sql';
if (!file_exists($sqlFile)) {
    die("SQL dump file not found at $sqlFile\n");
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    die("Failed to read SQL dump file.\n");
}

// Split the file into individual statements. Using multi_query to handle multiple statements.
if (mysqli_multi_query($conn, $sql)) {
    do {
        // Store result to free up memory
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    echo "Database import completed successfully.\n";
} else {
    echo "Error during import: " . mysqli_error($conn) . "\n";
}
?>
