<?php require_once "inc/header.inc.php"?>

<?php
//Restriction d'accès à la page : si l'utilisateur n'est pas connecté
if( !userConnect()){

    //redirection vers la page connexion.php
    header('location:connexion.php');
    exit; 
}

//------------------------------
//Si l'admin est connecté, on affiche un titre pour le préciser

if (adminConnect()){
    $content .= "<h2 style='color:tomato;'> ADMINISTRATEUR </h2>";
}


?>
<?php

    // debug($_SESSION);

    //Ici, on récup_re le pseudo de la personne connectée grâce au fichier de session que l'on a rempli lors de la connexion et on l'affiche dans la balise <h2>


    $pseudo = $_SESSION['membre']['pseudo'];

    $content .= "<h3>Vos informations personnelles</h3><br>";

    $content .= "<p>Votre prénom: " . $_SESSION['membre']['prenom'] . "</p>";
    //Obligation de faire de la concaténation lrosque l'on souhaite afficher des valeurs d'un talbeau multidimentionnel (m^me si l'on est entre guillemets)
    $content .= "<p>Votre nom: " . $_SESSION['membre']['nom'] . "</p>";
    $content .= "<p>Votre email: " . $_SESSION['membre']['email'] . "</p>";
    $content .= "<p>Votre adresse: " . $_SESSION['membre']['adresse'] . " ". $_SESSION['membre']['cp'] . " " . $_SESSION['membre']['ville'] ."</p>";
?>

    <h1>Profil</h1><br><br>

    <h2 style="font-weight: bold;">Bonjour <?= $pseudo ?></h2><br><br>

    <?= $content; //affichage du contenu ?>

<?php require_once "inc/footer.inc.php"?>