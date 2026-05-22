<?php
require 'db.php';
$h = mysqli_query($conn, 'SELECT * FROM hebergement WHERE image IS NOT NULL OR photo_principale IS NOT NULL LIMIT 5');
if ($h) {
    print_r(mysqli_fetch_all($h, MYSQLI_ASSOC));
}
