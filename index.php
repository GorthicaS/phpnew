<?php

session_start();
require('core/functions.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Salut connard</title>
</head>
<body>
    <?php
    include('inc/header.php');
    ?>
    <h1>Page d'accueil</h1>
    <p>Mon premier site PHP avec des includes donc tu critiques pas</p>
    <?php
    include('inc/footer.php');
    ?>
</body>
</html>