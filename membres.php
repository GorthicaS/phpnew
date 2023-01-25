<?php
session_start();
require('core/functions.php');
$db = pdo_connect();
if(verifUser())
{
    header('location:prive.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espaces membre</title>
</head>
<body>
    <?php include ('inc/header.php'); ?>
    <h1>Connectez-vous à l'espace membre</h1>
    <?php
    if(isset($_GET['message']))
    {
        $message = urldecode($_GET['message']);
        echo '<div style="background:red;width:100%;border-radius:30px;color:white;">'.$message.'</div>';
    }
    ?>
    <form name="membre" method="post" action="action.php?e=connexion">
        <label for="login">Login :</label>
        <input type="text" name="login" />
        <br />
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" />
        <br />
        <button type="submit" name="submit">Se Connecter</button>
    </form>
</body>
</html>