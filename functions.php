<?php
function pdo_connect() 
{
    $DATABASE_HOST = 'localhost:3306'; 
    $DATABASE_LOGIN = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'db_cloud';
    try{
        return new PDO('mysql:host='.$DATABASE_HOST.';dbname='.$DATABASE_NAME.';charset=utf8',$DATABASE_LOGIN,$DATABASE_PASS);
    } catch(PDOException $exception) {
        exit('erreur de connection à la BDD');
    }
}
function verifUser()
{
    global $db;
    global $_COOKIE;
    if($_COOKIE['id_user'] && $_COOKIE['pass_user'])
    {
        $verif_user = $db->prepare('SELECT * FROM `Table_User` WHERE User_ID = :user AND User_Password = :pass LIMIT 1');
        $verif_user->bindParam(':user',$_COOKIE['id_user'],PDO::PARAM_STR);
        $verif_user->bindParam(':pass',$_COOKIE['pass_user'],PDO::PARAM_STR);
        $verif_user->execute();
        if($verif_user->rowCount() == 1)
        {
            return $verif_user->fetch(PDO::FETCH_OBJ);
        }
        else
        {
            return false;
        }
    }
    else
    {
        return false;
    }
}
$menu_footer = array(
    'mention.php' => 'Mentions Legales',
    'contact.php' => 'Nous Contacter'
);
function afficheMenu($emplacement='header')
{
    $str = '<ul>';
    if($emplacement=='header')
    {
        $menu_header = array(
            'index.php' => 'Accueil',
            'inscription.php' => 'Inscription',
            'membres.php' => 'Espace Membres',
            'contact.php' => 'Nous Contacter'
        );
        foreach($menu_header as $lien => $titre)
        {
            $str.= '<li><a href="'.$lien.'">'.$titre.'</a></li>';
        }
    }
    elseif($emplacement=='footer')
    {
        global $menu_footer;
        foreach($menu_footer as $lien => $titre)
        {
            $str.= '<li><a href="'.$lien.'">'.$titre.'</a></li>';
        }    
    }
    $str.= '</ul>';
    return $str;
}
$identifiants = array('admin','buzz','woody','kevledev');
$motdepasses = array('bite','bitenmousse','bitenbois');
function verifConnect($login,$password){
    global $identifiants;
    global $motdepasses;
    if(in_array($login,$identifiants) && in_array($password,$motdepasses))
    {
        return true;
    }
    else 
    {
        return false;
    }
}
// Autre methode de modofication de nom de fichier 
function renome_image($name)
{
   $date = date('d-m-Y-h-i-s');
    return $date.$name;
}
$extensions = array('.pdf','.png','.jpg','.mp4','.gif');
function uploadFichier($fichier)
{
    global $extensions;
    $verif_extension = strchr($fichier['name'],'.');
    $random_name = rand().$verif_extension;
    if(in_array($verif_extension,$extensions))
    {
        // On verifie si le dossier de l'user existe
        if(!is_dir('upload/'.$_COOKIE['login']))
        {
            // Si il existe pas on le créer
            mkdir('upload/'.$_COOKIE['login']);
        }
        // On renomme le fichier 
        //$nom_ficher = renome_image($fchier['name']);
        // On transmet notre fichier dans son dossier
        if(move_uploaded_file($fichier['tmp_name'],'upload/'.$_COOKIE['login'].'/'.$random_name))
        {
            return true;
        }
    }
}
function uploadFichiers()
{
    global $extensions;
    global $_FILES;
    global $db;
    global $user;
    $message = array();
    for($i=0;$i<count($_FILES['fichier']['name']);$i++)
    {
        // On verifie l'extension
        $verif_extension = strrchr($_FILES['fichier']['name'][$i],'.');
        if(in_array($verif_extension,$extensions))
        {
            $nom_fichier = renome_image($_FILES['fichier']['name'][$i]);
            if(move_uploaded_file($_FILES['fichier']['tmp_name'][$i],'upload/'.$user->User_ID.'/'.$nom_fichier))
            {
                // on affiche un message de succes 
                $message[$i] = 'Fichier'.$_FILES['fichier']['name'][$i].'envoyé';
                // on insère le fichier a la bdd
                $file = $db->prepare('INSERT INTO `Table_File` SET
                                      File_User_ID = :user_id,
                                      File_Name = :nom_fichier,
                                      File_Original_Name = :original_name,
                                      File_Date_Add = CURDATE(),
                                      File_Download = 0,
                                      File_Date_Download = CURDATE()
                                        ');
                $file->bindValue(':user_id',$user->User_ID,PDO::PARAM_STR);
                $file->bindValue(':nom_fichier',$nom_fichier,PDO::PARAM_STR);
                $file->bindValue(':original_name',$_FILES['fichier']['name'][$i],PDO::PARAM_STR);
                $file->execute();

            }
            else
            {
                $message[$i] = 'Erreur avec le fichier'.$_FILES['ficher']['name'][$i];
            }
        }
        else
        {
            $message[$i] = 'Extension non autorisé pour '.$_FILES['ficher']['name'][$i];

        }
    }
    return $message;
}
function captcha1()
{
    // On génère deux nombre aléatoire
    $nb1 = rand(1,100);
    $nb2 = rand(1,100);
    // On fait l'opération
    $result = $nb1+$nb2;
    // On enregistre le résultat dans la session
    $_SESSION['captcha'] = $result;
    // On retourne l'opération
    return $nb1.'+'.$nb2.'=';
}
function captcha2()
{
    $question = array(
        "Ingrid est-ce-que tu b?",
        "Qui est le meilleur ami de Woody ?",
        "Que fumes Geoffrey ?",
        "Combien de cheveux gerald a sur la tete ?",
        "A cause de qui maxime n'a plus de voiture",
        "Si je dit bleu,jaune,rouge tu dit quoi ?"
    );
    $reponse = array(
    array("Non"),
    array("Buzz"),
    array("De la weed","du shit","les toilettes"),
    array("10","11","très peu"),
    array("geoffrey","le moteur de merde","lui-meme"),
    array("lidl")
    );
    // On sélectionne une clé aléatoire comprise entre 0 et la taille du tableau
    $aleatoire = rand(0,(count($question)-1));
    // On enregistre en session reponse 
    $_SESSION['captcha2'] = serialize($reponse[$aleatoire]);
    return $question[$aleatoire];


}
?>