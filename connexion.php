<?php require_once "inc/header.inc.php"?>

<?php

//DECONNEXION : le script de la deconnexion se position AVANT la redirection/restriction, sinon elle ne SERA PAS interpetée par l'intercepteur php à cause du "exit;" dans la redirection donc nous aurions déjà quitté le fichier

if( isset( $_GET['action']) && $_GET['action'] == "deconnexion"){//S'il existe une 'action' dans l'URL ET que cette a'action' est égale à déconnexion alors on détruit le fichier de session

    session_destroy(); //détruit le fichier de session
}

//--------------------------
// restriction d'accès à la page si on EST connecté

if( userConnect()){
    
    header('location:profil.php');
    exit;
}


if( $_POST ){
    // debug($_POST);
    //Comparaison du pseudo posté et celui en BDD
    $r = execute_requete("SELECT * FROM membre WHERE pseudo = '$_POST[pseudo]'");
        //Ici, on récupère TOUTES les informations provenant de la table membre à condition que dans la colonne pseuso, ce soit égal à la saisie de l'internaute
        // debug($r);

    if( $r->rowCount() >=1){ //S'il y a une correspondance dans la table 'membre', '$r' renverra une ligne de résultat et donc c'est que le pseudo est valide (il exist en BDD)

        //Récupération des données pour les exploiter:
        $membre = $r->fetch( PDO::FETCH_ASSOC);
        // debug($membre);

        //Vérificaiton du mdp:
        if(password_verify($_POST['mdp'], $membre['mdp']) ){
            //password_verify(arg1, arg2); retourne true ou false et permet de comparer une chaine à une chaine cryptée
                //arg1 : le mot de passe saisi par l'utilisateur
                //arg2 la chaine cryptée par la fonction password_hash(), ici le mdp en bdd

                echo "<div class='alert alert-success'>Ho ! Ho ! Ho ! Salut à toi " . $membre['prenom'] ."!</div>";

                //insertion des infos ($membre) de la personne qui se connecte dans le fichier de session

                $_SESSION['membre'] = $membre;
                    debug($_SESSION);

                //------------------------
                //Autre méthode 'manuelle' pour insérer les infos dans le fichier session:
                    // $_SESSION['membre']['id_membre'] = $membre['id_membre'];
                    // $_SESSION['membre']['pseudo'] = $membre['pseudo'];
                    // $_SESSION['membre']['mdp'] = $membre['mdp'];
                    // $_SESSION['membre']['prenom'] = $membre['prenom'];
                    // $_SESSION['membre']['nom'] = $membre['nom'];
                    // $_SESSION['membre']['email'] = $membre['email'];
                    // $_SESSION['membre']['adresse'] = $membre['adresse'];
                    // $_SESSION['membre']['ville'] = $membre['ville'];
                    // $_SESSION['membre']['cp'] = $membre['cp'];
                    // $_SESSION['membre']['statut'] = $membre['statut'];
                //------------------------
                //Boucle foreach pour insérer les infos dans le fichier de session

                // foreach ($membre as $indice => $valeur){

                //     $_SESSION['membre'][$indice] = $valeur;
                // }


                //redirection vers la page profil:
                header('location:profil.php');
                exit; //permet de quitter à cet endroit précis le script courant et donc de ne pas interpréter le code qui suit cette instruction.

        }else{//Sinon, c'est que le mdp n'est pas valide
            $error .= "<div class='alert alert-danger'> Mot de passe incorrect </div>";
        }


    }else{//Sinon, c'est que le pseudo n'est pas valide
        $error .= "<div class='alert alert-danger'> Pseudo incorrect </div>";
    }
}

?>

    <h1>Connexion</h1><br><br>


    <br><?php echo $error; //affichage de la variable $error?><br>
    <form style="display: flex;
    flex-direction: column;
    align-items: center;" action="" method="POST">
        <div class="conteneur2">
            <label for="">Pseudo:</label><br>
            <input type="text" name="pseudo" placeholder="Votre pseudo">
        </div><br>
        <div class="conteneur2">
            <label for="">Mot de passe:</label><br>
            <input type="text" name="mdp" placeholder="Votre mot de passe">
        </div>

        <input style="margin-top: 20px;
    padding: 7px;
    background-color: lightgray;" type="submit" value="Se connecter" class="btn btn-secondaty">
    </form>

<?php require_once "inc/footer.inc.php"?>