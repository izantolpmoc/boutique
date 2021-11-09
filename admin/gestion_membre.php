<?php require_once "../inc/header.inc.php"; //inclusion du fichier header?> 
<?php

$success = "";

if(!adminConnect()){
    header('location: index1.php');
    exit;
}

// debug($_GET);
if(isset($_GET['action']) && $_GET['action'] == 'suppression'){

    execute_requete("DELETE FROM membre WHERE id_membre = '$_GET[id_membre]'");
}

if(isset( $_GET['id_membre'] ) ){//S'il existe 'id_memnbre' dans l'url c'est que l'on est dans le cadre d'une modification

    //récupération des infos à afficher pour pré-remplir le formulaire:
    $r = execute_requete("SELECT * FROM membre WHERE id_membre = '$_GET[id_membre]'");

    //exploitation des données:
    $membre_actuel = $r->fetch(PDO::FETCH_ASSOC);
    // debug($article_actuel);

    if( isset($membre_actuel['pseudo'])){

        $pseudo = $membre_actuel['pseudo'];
    }else{
        $pseudo ='';
    }

    $nom = (isset($membre_actuel['nom'])) ? "$membre_actuel[nom]" : "";

    $prenom = (isset($membre_actuel['prenom'])) ? "$membre_actuel[prenom]" : "";

    $email = (isset($membre_actuel['email'])) ? "$membre_actuel[email]" : "";

    $sexe_f = (isset($membre_actuel['sexe']) && $membre_actuel['sexe'] == 'f') ? "checked" : "";

    $sexe_m = (isset($membre_actuel['sexe']) && $membre_actuel['sexe'] == 'm') ? "checked" : "";

    $ville = (isset($membre_actuel['ville'])) ? "$membre_actuel[ville]" : "";

    $cp = (isset($membre_actuel['cp'])) ? "$membre_actuel[cp]" : "";

    $adresse = (isset($membre_actuel['adresse'])) ? "$membre_actuel[adresse]" : "";

    $admin = (isset($membre_actuel['statut']) && $membre_actuel['statut'] == '1') ? "checked" : "";
    $lambda = (isset($membre_actuel['statut']) && $membre_actuel['statut'] == '0') ? "checked" : "";
}

if($_POST){

    // debug($_POST);

    //Conditions de vérifications comme à l'insertion
    if( strlen($_POST['pseudo']) <= 3 || strlen($_POST['pseudo']) > 15){


        $error .= "<div class='alert alert-danger'>Votre pseudo doit contenir entre 4 et 15 caractères.</div>";

    }else{
        $error .= '';
    }

    //-------------------------------------------

    // Boucle sur toutes les saisies de l'internaute afin de les passer dans les fonctions htmlentities() et addslashes():

    foreach($_POST as $indice => $valeur){

        $_POST[$indice] = htmlentities( addslashes($valeur));
    }

    
    if(empty($error)){ // c sa la le p^roblaim

        
        //Modification
        execute_requete("UPDATE membre SET pseudo = '$_POST[pseudo]',
                                            nom = '$_POST[nom]',
                                            prenom = '$_POST[prenom]',
                                            email = '$_POST[email]',
                                            sexe = '$_POST[sexe]',
                                            ville = '$_POST[ville]',
                                            cp = '$_POST[cp]',
                                            adresse = '$_POST[adresse]',
                                            statut = '$_POST[statut]'
                                
                                WHERE id_membre = '$_GET[id_membre]'
        
        
        ");

        $success= "<div class='alert alert-success' id='temporary'>Modification effectuée ✅.</div>";

        redirige('gestion_membre.php');
    }
}

    $r = execute_requete("SELECT * FROM membre ORDER BY id_membre DESC");

    $content .= "<p>Nombre de personnes incrites : ". $r->rowCount() ."</p>";

    $content .= "<table class='table table-bordered'>";
        $content .= "<thead>";
            $content .= "<tr>";

            $nombre_colonne = $r->columnCount();

            for( $i = 0; $i < $nombre_colonne; $i++){

                $titre= $r->getColumnMeta($i);

                if($titre['name'] == 'mdp'){
                    $content .= '';
                }else{
                $content .= "<th scope='col' data-label='$titre[name]'> $titre[name]</th>";            
                // debug($titre);
                }
            }

            $content .= "<th scope='col' data-label='Suppression'>Suppression</th>"; 
            $content .= "<th scope='col' data-label='Modification'>Modification</th>"; 
            
            $content .= "</tr>";
        $content .= "</thead>";

        while( $ligne = $r->fetch(PDO::FETCH_ASSOC)){

        $content .= "<tbody>";
            $content .= "<tr>";

                foreach( $ligne as $indice => $valeur){

                    if($indice == 'mdp'){
                        $content .= '';
                    }else{
                        $content .= "<td scope='row' data-label='$indice'>$valeur</td>";
                    }

                    
                }
            
                $content .= '<td scope ="row" data-label="Supprimer"><a href="?action=suppression&id_membre= '. $ligne['id_membre'] .'" onclick="return( confirm(\'Voulez-vous supprimer le membre:  '. $ligne['pseudo'] .'\') )"  >❌</a></td>';

                // $content .= "<td scope='row' data-label='Supprimer'><a href='?action=suppression&id_membre=$ligne[id_membre]' onclick='return( confirm('Voulez-vous supprimer le membre : $ligne[pseudo]') )  >❌</a></td>";
                $content .= "<td scope='row' data-label='Editer'><a href='?action=modification&id_membre=". $ligne['id_membre'] ."'><i class = 'far fa-edit'></a></td>";
        

            $content .= "</tr>";
        $content .= "</tbody>";

        }

    $content .= "</table>";

    



?>

<h1>Gestion des membres</h1>


<?php if(isset($_GET['action']) && $_GET['action'] == 'modification'): ?>

<?= $error?>
<div style="display: flex; flex-direction:column; align-items:center;">
        <form action="" method="POST">

            <label for="">Pseudo</label><br>
            <input type="text" name="pseudo" value="<?= $pseudo?>"><br>

            <label for="">Nom</label><br>
            <input type="text" name="nom" value="<?= $nom?>"><br>

            <label for="">Prénom</label><br>
            <input type="text" name="prenom" value="<?= $prenom?>"><br>

            <label for="">Email</label><br>
            <input type="text" name="email"value="<?= $email?>"><br><br>

            <label for="">Civilité</label><br>
            <input type="radio" name="sexe" value="m" <?= $sexe_m ?>> Homme
            <input type="radio" name="sexe" value="f"  <?= $sexe_f ?>> Femme <br><br>

            <label for="">Ville</label><br>
            <input type="text" name="ville" value="<?= $ville?>"><br><br>

            <label for="">Code postal</label><br>
            <input type="text" name="cp" value="<?= $cp?>"><br><br>

            <label for="">Adresse</label><br>
            <input type="text" name="adresse" value="<?= $adresse?>"><br><br>

            <label for="">Statut</label><br>
            <div class="flex column">
                <div class="flex">Administrateur <input type="radio" name="statut" value="1" <?= $admin ?>> </div><br>
                <div class="flex">Lambda <input type="radio" name="statut" value="0"  <?= $lambda?>> </div>
            </div><br><br>

            <div class="flex">
                <a class="btn btn-secondary" href="gestion_membre.php">Annuler</a>
                <input type="submit" class="btn btn-secondary" value="Modifier">
            </div>
            </form><br><br>
    </div>

<?php else: ?>
    <?= $success ?>
    <?= $content ?>

<?php endif; ?>

<?php require_once "".$admin."inc/footer.inc.php"; //inclusion du fichier footer ?>