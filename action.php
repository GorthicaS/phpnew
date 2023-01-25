<?php
session_start();
require('core/functions.php');
$db = pdo_connect();
switch($_GET['e'])
{
    case 'inscription':
        // On va vérifier que l'ensemble des champs ont été saisis
        if(isset($_POST['submit']))
        {
            if(!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['password2']) && !empty($_POST['email']))
            {
                if($_POST['password'] == $_POST['password2'])
                {
                    $verif_login = $db->prepare('SELECT User_ID FROM `Table_User`WHERE User_Login = :login OR User_Email = :email');
                    $verif_login->bindParam(':login',$_POST['login'],PDO::PARAM_STR);
                    $verif_login->bindParam(':email',$_POST['email'],PDO::PARAM_STR);
                    $verif_login->execute();
                    if($verif_login->rowCount() == 0)
                    {
                        $password = sha1(md5($_POST['password']));
                        $user = $db->prepare('INSERT INTO `Table_User` SET
                                               User_Email = :email, 
                                               User_Login = :login, 
                                               User_Password = :password, 
                                               User_Date = CURDATE()
                                                ');
                        $user->bindValue(':email',$_POST['email'],PDO::PARAM_STR);
                        $user->bindValue(':login',$_POST['login'],PDO::PARAM_STR);
                        $user->bindValue(':password',$password,PDO::PARAM_STR);
                        if($user->execute())
                        {
                            // On récupère l'ID de l'user
                            $id_user = $db->lastInsertId();
                            // on créer son répertoire
                            if(!is_dir('upload/'.$id_user))
                            {
                                mkdir('upload/'.$id_user);
                            }
                            setcookie('id_user',$id_user,(time()+3600));
                            setcookie('pass_user',$password,(time()+3600));
                            $_SESSION['connect'] = 1;
                            // On redirige l'utilisateur vers sa page privé
                            header('location:prive.php');
                            exit;
                        }
                        else 
                        {
                            // si il y a une erreur avec la requête
                            $message = 'Une erreur SQL est survenue';
                        }
                    }
                    else 
                    {
                        // si l'user existe déjà 
                        $message = 'Login ou email déjà enregistrer';

                    }
                }
                else  
                {
                    // si les 2 mots de passe ne sont pas identiques
                    $message = 'Les mots de passes ne correspondent pas !!!!!';
                }
            }
        }
        header('location:inscription.php?message='.urlencode($message));

    break;

    case 'connexion':
        if(isset($_POST['submit']))
        {
            if(!empty($_POST['login']) && !empty($_POST['password']))
            {
                var_dump($_POST);
                $verif_connect = $db->prepare('SELECT User_ID, User_Password FROM `Table_User` WHERE User_Login = :joseph OR User_Password = :gerald');
                $verif_connect->bindParam(':joseph',$_POST['login'],PDO::PARAM_STR);
                $verif_connect->bindValue(':gerald',sha1(md5($_POST['password'])),PDO::PARAM_STR);
                $verif_connect->execute();
                if($verif_connect->rowCount() == 1)
                {
                    $user = $verif_connect->fetch(PDO::FETCH_OBJ);
                    setcookie('id_user',$user->User_ID,(time()+3600));
                    setcookie('pass_user',$user->User_Password,(time()+3600));
                    $_SESSION['connect'] = 1;
                    header('location:prive.php');
                    exit;
                }
            }
            else 
            {
                $message = "Abuse pas rentre un login et mot de passe pti con !";
            }
            header('location:membres.php?message='.urlencode($message));
            exit;
        }
    break; 
    
    case 'deco':
        $_SESSION['connect'] = 0;
        setcookie('id_user',null,(time()-10));
        setcookie('pass_user',null,(time()-10));
        header('location:membres.php');

    break;

    case 'upload':

        $user = verifUser();
        if($user)
        {
            if(isset($_POST['submit']))
            {

                $uploads = uploadFichiers();
                header('location:prive.php?message='.serialize($uploads));
                exit;
            }
        }
        
    break;

    case 'deletefichier':
        if(!empty($_GET['fichier']))
        {
            unlink('upload/'.$_COOKIE['login'].'/'.$_GET['fichier']);
            header('location:prive.php');
            exit;
        }

    break;

    case 'download':

        if(!empty($_GET['id']))
        {
            // On prepare la requete Update
            $req = 'UPDATE `Table_File` SET File_Download = File_Download+1, File_Date_Download = CURDATE() WHERE File_ID = '.intval($_GET['id']);
            // On execute la requete Update
            $db->query($req);
            // On prépare le requete pour recupérer les infos sur le fichier 
            $fichier = 'SELECT * FROM `Table_File` WHERE File_ID = '.intval($_GET['id']);
            // On execute la requete de récupération d'infos sur le fichier et on la range dans la variable $execution
            $execution = $db->query($fichier);
            // On compte le nombre de ligne retourné par la requête
            $nb_ligne = $execution->rowCount();
            if($nb_ligne == 1)
            {
                // Si on a 1 ligne retournée on créer l'objet avec les éléments du fichier 
                $info = $execution->fetch(PDO::FETCH_OBJ);
                // On prépare le header avec le renommage du fichier au bon format
                header('Content-Disposition: attachement; filename="'.$info->File_Original_Name.'"');
                // On lit le fichier sur le serveur
                readFile('upload/'.$info->File_User_ID.'/'.$info->File_Name);
            }
        }
    break;

    case 'contact':
        if(isset($_POST['submit']))
    {
        if(!empty($_POST['nom']) && !empty($_POST['prenom']) && !empty($_POST['sujet']) && !empty($_POST['email']) && !empty($_POST['message']))
        {   
            if($_POST['captcha'] == $_SESSION['captcha'])
            {
                $captcha2 = unserialize($_SESSION['captcha2']);
                if(in_array($_POST['captcha2'],$captcha2))
                {
                    $contact = $db->prepare('INSERT INTO `Table_Contact` SET
                                           Contact_Email = :email, 
                                           Contact_Nom = :nom, 
                                           Contact_Prenom = :prenom,
                                           Contact_Sujet = :sujet, 
                                           Contact_Message = :message,
                                           Contact_Date = CURDATE()'
                                           );
                    $contact->bindValue(':email',$_POST['email'],PDO::PARAM_STR);
                    $contact->bindValue(':nom',$_POST['nom'],PDO::PARAM_STR);
                    $contact->bindValue(':prenom',$_POST['prenom'],PDO::PARAM_STR);
                    $contact->bindValue(':message',$_POST['message'],PDO::PARAM_STR);
                    $contact->bindValue(':sujet',$_POST['sujet'],PDO::PARAM_STR);
                    $contact->execute();
                    $message = "Votre demande a été envoyée avec succès!";
                    // header('location:contact.php?message='.urlencode($message));
                    //exit;  
                }
                else 
                {
                    $message = "Tu n'as aucune culture";
                }
            }
            else 
            {
                echo "Erreur de captcha.";
            }
        } 
        else 
        {
            echo "Tous les champs doivent être remplis.";
        }
        
    }
    break;
    
}
?>