<?php
require 'db.php';
$res = mysqli_query($conn, 'DESCRIBE reservations');
while ($row = mysqli_fetch_assoc($res)) {
    print_r($row);
}

