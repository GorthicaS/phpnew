<?php
session_start();
require('core/functions.php');
$db = pdo_connect();
if(!verifUser())
{
    $message = 'Veuillez vous reconnecter';
    header('location:membres.php?message'.urlencode($message));
    exit;
}
$verif_user = $db->prepare('SELECT * FROM `Table_User` WHERE User_ID = :user AND User_Password = :pass LIMIT 1');
$verif_user->bindParam(':user',$_COOKIE['id_user'],PDO::PARAM_STR);
$verif_user->bindParam(':pass',$_COOKIE['pass_user'],PDO::PARAM_STR);
$verif_user->execute();
if($verif_user->rowCount() == 1)
{
    $user = $verif_user->fetch(PDO::FETCH_OBJ);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur l'espace membre (connard) </title>
</head>
<body>
    <?php include ('inc/header.php'); ?>
    <h1>Bonjour <?php echo $user->User_Login; ?></h1>
    <?php
    // On récupère l'ensemble des fichiers
    $req_fichier = $db->prepare('SELECT * FROM `Table_File` WHERE File_User_ID = :id_user ORDER BY File_Date_Add ASC');
    $req_fichier->bindParam(':id_user',$user->User_ID,PDO::PARAM_INT);
    $req_fichier->execute();
    $nb_fichier = $req_fichier->rowCount();
    // On verifie si il y a bien des fichiers liés à l'user
    if($nb_fichier >= 1)
    {
        echo '<ul>';
        $fichiers = $req_fichier->fetchAll();
        foreach($fichiers as $fichier)
        {
            echo '<li><a href="action.php?e=download&id='.$fichier['File_ID'].'">'.$fichier['File_Original_Name'].'</a></li>';
        }
        echo '</ul>';
    }
    else 
    {
        echo " Vous n'avez aucun fichier";
    }
    // $liste_fichiers = scandir('upload/'.$user->User_ID);
    // if($liste_fichiers)
    // {
    //     echo '<ul>';
    //     $i=0;
    //     foreach($liste_fichiers as $fichier)
    //     {
    //         if($i>1)
    //         {
    //             echo '<li><a href="upload/'.$user->User_ID.'/'.$fichier.'"target="_blank">'.$fichier.'</a></li><a href="action.php?e=deletefichier&fichier='.$fichier.'"><img src="assets/img/Lidl_.png"></a>';
    //         }
    //         $i++;
    //     }
    //     echo '</ul>'; 
    // }
    // ?>
    <form  method ="post" action="action.php?e=upload" enctype="multipart/form-data">
        <label for="fichier" name="fichier"></label>
        <input type="file" name="fichier[]" multiple />
        <br />
        <label for="fichier" name="fichier"></label>
        <input type="file" name="fichier[]" multiple/>
        <br />
        <button type="submit" name="submit">Envoyer</button>
    </form>
    <a href="action.php?e=deco">Se deconnecter</a>
    <?php include('inc/footer.php'); ?>
</body>
</html>