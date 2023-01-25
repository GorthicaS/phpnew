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
    <title>Nous contacter</title>
</head>
<body>
    <?php include('inc/header.php'); ?>
    <h1>Nous contacter</h1>
    <form class="contact_form" name="contact" action="action.php?e=contact" method="post">
        <label class="form-label" for="nom">Nom :</label>
        <input class="form-input" type="text" name="nom" id="nom" required>
        <br/>
        <label class="form-label" for="prenom">Prenom :</label>
        <input class="form-input" type="prenom" name="prenom" id="prenom" required>
        <br/>
        <label class="form-label" for="email">Email :</label>
        <input class="form-input" type="email" name="email" id="email" required>
        <br/>
        <label class="form-label" for="sujet">Sujet :</label>
        <input class="form-input" type="sujet" name="sujet" id="sujet" required>
        <br/>
        <label class="form-label" for="message">Message :</label>
        <input class="form-input" type="message" name="message" id="message" required>
        <br />
        <label for="captcha">Calculer <?php echo captcha1(); ?> :</label>
        <input type="text" name="captcha" />
        <br />
        <label for="captcha2"><?php echo captcha2(); ?> :</label>
        <input type="text" name="captcha2" />
        <br />
        <button type="submit" name="submit">Envoyer</button>
        <br/>
    </form>
    <?php include('inc/footer.php'); ?> 
</body>
</html>