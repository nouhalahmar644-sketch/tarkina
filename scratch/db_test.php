<?php
require_once __DIR__ . '/../db.php';
$createTableQuery = "CREATE TABLE IF NOT EXISTS `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `sujet` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
if (mysqli_query($conn, $createTableQuery)) {
    echo "Table 'messages' ensured successfully!\n";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "\n";
}

$insertQuery = "INSERT INTO `messages` (`nom`, `email`, `sujet`, `message`) VALUES ('Test User', 'test@user.com', 'Test Subject', 'This is a test message.')";
if (mysqli_query($conn, $insertQuery)) {
    echo "Test message inserted successfully!\n";
} else {
    echo "Error inserting test message: " . mysqli_error($conn) . "\n";
}

$res = mysqli_query($conn, "SELECT * FROM `messages` ORDER BY `id` DESC LIMIT 1");
if ($res && $row = mysqli_fetch_assoc($res)) {
    echo "Successfully retrieved last message:\n";
    print_r($row);
} else {
    echo "Error retrieving messages.\n";
}
