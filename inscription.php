<?php
session_start();
require('core/functions.php');
if($_SESSION['connect'] == 1  && (!empty($_COOKIE['login']) || !empty($_COOKIE['password'])))
{
    // Si c'est le cas redirection vers la page prive.php
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
    <title>S'incrire</title>
</head>
<body>
    <?php include('inc/header.php'); ?>
    <h1>Inscription</h1>
    <form class="inscription-form" name="inscription" action="action.php?e=inscription" method="post">
        <label class="form-label" for="login">Login :</label>
        <input class="form-input" type="text" name="login" id="login" required>
        <br>
        <label class="form-label" for="password">Mot de passe :</label>
        <input class="form-input" type="password" name="password" id="password" required>
        <br>
        <label class="form-label" for="password2">Confirmation mot de passe :</label>
        <input class="form-input" type="password" name="password2" id="password2" required>
        <br>
        <label class="form-label" for="email">Email :</label>
        <input class="form-input" type="email" name="email" id="email" required>
        <br>
        <input class="form-submit" type="submit" name="submit" value="S'inscrire">
    </form>
    <?php include('inc/footer.php'); ?> 
</body>
</html>