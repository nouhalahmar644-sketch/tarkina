<?php
require 'db.php';
$res = mysqli_query($conn, 'SELECT * FROM region WHERE nom = "Chenini"');
print_r(mysqli_fetch_assoc($res));
