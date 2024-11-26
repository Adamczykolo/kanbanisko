<?php
session_start();

include 'nav.php';

echo "Jesteś zalogowany";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<h1>
Cześć
    <?php
echo ($login);
    ?>
!
</h1>
</body>
</html>