<?php
require 'db.php';
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body>
<?php
echo "<h3>Charset test:</h3>";
$r = mysqli_query($conn, "SHOW VARIABLES LIKE 'character_set%'");
while ($row = mysqli_fetch_row($r)) {
    echo $row[0] . " = " . $row[1] . "<br>";
}

echo "<h3>Raw data from DB:</h3>";
$r2 = mysqli_query($conn, "SELECT id, titre, localisation FROM hebergement LIMIT 3");
while ($row = mysqli_fetch_assoc($r2)) {
    echo $row['id'] . " | " . $row['titre'] . " | " . $row['localisation'] . "<br>";
}

echo "<h3>HEX of first titre:</h3>";
$r3 = mysqli_query($conn, "SELECT HEX(titre) as h, titre FROM hebergement LIMIT 1");
$row = mysqli_fetch_assoc($r3);
echo "HEX: " . $row['h'] . "<br>";
echo "TEXT: " . $row['titre'] . "<br>";
?>
</body>
</html>
