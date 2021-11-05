<?php require_once "inc/header.inc.php"?>

<?php

if(userConnect()){ //restriction si on est connecté

    header('location:profil.php'); //redirection vers la page profil
    exit;
}

?>

<?php
if($_POST){
    debug($_POST);
    //Contrôle des saisies de l'internaute (il faudrait faire des controles pour TOUS les champs du formulaire)

    //Contrôle la taille du pseudo (3 et 15 caractères):
    if( strlen($_POST['pseudo']) <= 3 || strlen($_POST['pseudo']) > 15){

        //si la taille du pseudo est inférieure ou égale à 3 OU QUE sa taille est strictement supérieure à 15

        //strlen($arg) retourne la taille d'un string

        $error .= "<div class='alert alert-danger'>Votre pseudo doit contenir entre 4 et 15 caractères.</div>";

    }

    //teste IF le pseudo est disponible: on ne peut pas avoir 2 fois le même pseudo car nous avons indiqué une clé UNIQUE lors de la création de la BDD pour le champ 'pseudo'.
    $r = execute_requete("SELECT pseudo FROM membre WHERE pseudo = '$_POST[pseudo]'");
    //Sélectionne moi le pseudo dans la table membre à condition que dans la colonne pseudo, ce soit égal à la saisie de l'internaute

    //debug($r); // représente le jeu de résultat retourné par la requête sous forme d'objet PDOstatement

    if( $r->rowCount() >= 1){ //Si le résultat est supérieur ou égal à 1, c'est que le pseudo est déjà attribué car il aura trouvé une correspondance dans la table 'membre' et renverra donc une ligne de résultat

        $error.= "<div class='alert alert-danger'>Pseudo indisponible.</div>";
    }

    //-------------------------------------------

    // Boucle sur toutes les saisies de l'internaute afin de les passer dans les fonctions htmlentities() et addslashes():

    foreach($_POST as $indice => $valeur){

        $_POST[$indice] = htmlentities( addslashes($valeur));
    }

     //-------------------------------------------
    //cryptage du mdp:
    $_POST['mdp'] = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
        //password_hash(): fonction interne à php qui permet de créer une clé de hachage

        debug($_POST['mdp']);

    //----------------------------------------------
    //INSERTION
    if( empty( $error ) ){ // si la variable $error est vide (et donc que le formulaire a été rempli correctement), on fait l'insertion en bdd
        execute_requete("INSERT INTO membre(pseudo, mdp, nom, prenom, email, sexe, adresse, ville, cp) 
                        VALUES (
                            '$_POST[pseudo]',
                            '$_POST[mdp]',
                            '$_POST[nom]',
                            '$_POST[prenom]',
                            '$_POST[email]',
                            '$_POST[sexe]',
                            '$_POST[adresse]',
                            '$_POST[ville]',
                            '$_POST[cp]'
                            )
            ");

        $content .= "<div class='alert alert-success'> Inscription validée <a href='". URL . "connexion.php'> Cliquez ici pour vous connecter </a></div>";

    }


}
?>

    <h1>Inscription</h1>


    <br><?php echo $error; //affichage de la variable $error ?>
    <?php echo $content; ?><br>
    <div style="display: flex; flex-direction:column; align-items:center;">
        <form action="" method="POST">

            <label for="">Pseudo</label><br>
            <input type="text" name="pseudo"><br>

            <label for="">Mot de passe</label><br>
            <input type="text" name="mdp"><br>

            <label for="">Nom</label><br>
            <input type="text" name="nom"><br>

            <label for="">Prénom</label><br>
            <input type="text" name="prenom"><br>

            <label for="">Email</label><br>
            <input type="text" name="email"><br><br>

            <label for="">Civilité</label><br>
            <input type="radio" name="sexe" value="m" checked> Homme
            <input type="radio" name="sexe" value="f"  > Femme <br><br>

            <label for="">Ville</label><br>
            <input type="text" name="ville"><br><br>

            <label for="">Code postal</label><br>
            <input type="text" name="cp"><br><br>

            <label for="">Adresse</label><br>
            <input type="text" name="adresse"><br><br>

            <input type="submit" class="btn btn-secondary" value="S'inscrire">
        </form>
    </div>

<?php require_once "inc/footer.inc.php"?>