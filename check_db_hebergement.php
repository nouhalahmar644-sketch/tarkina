<?php
require 'db.php';
$res = mysqli_query($conn, 'SELECT id, photo_principale FROM hebergement');
while ($row = mysqli_fetch_assoc($res)) print_r($row);
