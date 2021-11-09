<?php require_once "../inc/header.inc.php"?>

<!-- <style>
<?php require_once "../assets/css/style.css"; ?>
</style> -->
<?php

//restriction de l'accès 

if( !adminConnect() ){

    header('location:../connexion.php');
    exit;
}

// debug($_GET);

if( isset($_GET['action']) && $_GET['action'] == 'suppression'){ //S'il y a une action dans l'URL et que cette action est égale à suppression


    //Suppression de la photo:
    //1- Récupération de la colonne 'photo' en BDD:
    $r = execute_requete("SELECT photo FROM produit WHERE id_produit = '$_GET[id_produit]'");

    $photo_a_supprimer = $r->fetch(PDO::FETCH_ASSOC);
        // debug($photo_a_supprimer);

    $chemin_photo_a_supprimer = str_replace('http://localhost', $_SERVER['DOCUMENT_ROOT'], $photo_a_supprimer['photo']);
        // debug($chemin_photo_a_supprimer);
        //str_replace(arg1, arg2, arg3) = fonction de php qui permet de remplacer des occurences dans une chaîne
            // arg 1 la chaine que l'on souhaite remplacer
            // arg 2 la chaine de remplacement
            //arg 3 la chaine sur laquelle on veut effectuer les changements
        /*Ici, je remplace : 'http:localhost'
                        par: $_SERVER['DOCUMENT_ROOT'] <=> "C:/xampp/htdocs
                        dans: $photo_a_supprimer['photo'] (l'adresse de la photo)
        */

    if(!empty($chemin_photo_a_supprimer) && file_exists($chemin_photo_a_supprimer)){
        unlink($chemin_photo_a_supprimer);
        //unlink($arg) permet de supprimer un fichier (ici, $arg corresponf au chemin du fichier)

        //la portion de code ci-dessous (la suppression) DOIT IMPERATIVEMENT se trouver après la gestion de la suppression du fichier physique car si on supprime avant le produit en bdd on ne pourrait palus récupérer l'adresse de la photo en base
    }

    execute_requete("DELETE FROM produit WHERE id_produit = '$_GET[id_produit]'"); //Suppression dans la table produit a conditon que dans la colonne id_produit soit égale à l'id_produit que l'on récupère dans l'URL celle passée lorsque l'on clique sur la corbeille.
}
//-------------------------------
//Gestion de produits :INSERTION

if( !empty( $_POST ) ){ //Si le formaulaire a été validé et qu'il n'est pas vide

    // debug( $_POST ); // Contrles sur les saisies (il faudrait en faire pour chaque <input>)

    //EXERCICE: Faites en sorte d'afficher un message d'erreur si la référence postée existe déjà

    $r = execute_requete("SELECT reference FROM produit WHERE reference = '$_POST[reference]'");

        if ($r->rowCount()>=1){

            $error.= "<div class='alert alert-danger'>Référence déjà utilisée.</div>";
        }

    //-------------------------------------
    //Ici, je passe toutes les infos postées par l'admin dans les fonctions addslashes() et htmlentities():

    foreach( $_POST as $indice => $value){

        $_POST[$indice] = htmlentities(addslashes($value));
    }

    //-------------------------------
    //GESTION DE LA PHOTO

    // debug($_FILES);
    // debug($_SERVER);

    //------------------------------

    if( isset($_GET['action']) && $_GET['action'] == 'modification'){ //Si je suis dans le cadre d'une modification, je récupère le chemin en bdd de la photo du produit à modifier (grâce à la value de l'input type 'hidden') et je le stocke dans la variable $photo_bdd

        $photo_bdd = $_POST['photo_actuelle'];
    }

    //-------------------------------

    if( !empty( $_FILES['photo']['name'] )){ //Si le nom de la photo dans $_FILES n'est pas vide, c'est que l'on a téléchargé un fichier

        $nom_photo = $_POST['reference'] . '_' . $_FILES['photo']['name'];

        // debug($nom_photo);

        //Chemin pour accéder à la photo (à insérer en bdd)
        $photo_bdd = URL . "photo/" . $nom_photo;
            //rappel: la constate url= http://localhost/PHP/boutique
            // debug($photo_bdd);

        //Chemin où l'on souhaite enregistrer le ficher "physique" de la photo
        $photo_dossier = $_SERVER['DOCUMENT_ROOT'] . "/PHP/boutique/photo/" . $nom_photo;

        // debug($photo_dossier);

        //Enregistrement (le fichier physique) de la photo dans le dossier 'photo' de notre serveur 
        copy($_FILES['photo']['tmp_name'], $photo_dossier);

            //copy(arg1, arg2);
                //arg 1 : chemin du fichier source
                //arg 2 : chemin de destination

    }else{ //Sinon, c'est que l'on a pas téléchargé de fichier et donc on affiche un message d'erreur

        $error.= "<div class='alert alert-danger'>Vous n'avez pas uploadé de photo.</div>";
    }

    //-------------------------------
    //INSERTION

    if( isset( $_GET['action']) && $_GET['action'] == 'modification'){ //S'il y a une action dans l'URL et que cette action est également égale à modification, alors on effecture une requête UPDATE

        execute_requete("UPDATE produit SET reference = '$_POST[reference]',
                                            categorie = '$_POST[categorie]',
                                            titre = '$_POST[titre]',
                                            description = '$_POST[description]',
                                            couleur = '$_POST[couleur]',
                                            taille = '$_POST[taille]',
                                            sexe = '$_POST[sexe]',
                                            photo = '$photo_bdd',
                                            prix = '$_POST[prix]',
                                            stock = '$_POST[stock]'

        
                            WHERE id_produit = $_GET[id_produit]
        
        
        ");

        //redirection vers l'affichage
        redirige('?action=affichage');
        // header('location:http://localhost/PHP/boutique/admin/gestion_produit.php?action=affichage');


        
        
    }
    elseif( empty($error)){

        echo "<div class='alert alert-danger'>Vous n'avez pas uploadé de photo.</div>";

        execute_requete("INSERT INTO produit(reference, categorie, titre, description, couleur, taille, sexe, photo, prix, stock) 
        
                            VALUES (
                                '$_POST[reference]',
                                '$_POST[categorie]',
                                '$_POST[titre]',
                                '$_POST[description]',
                                '$_POST[couleur]',
                                '$_POST[taille]',
                                '$_POST[sexe]',
                                '$photo_bdd',
                                '$_POST[prix]',
                                '$_POST[stock]'
                                )
                            ");
    }
}

//-------------------------------
//AFFICHAGE des produits: toujours après l'insertion pour pouvoir voir le dernier produit inséré

    if( isset( $_GET['action']) && $_GET['action'] == 'affichage'){
        //S'il existe une action dans l'URL et que cette action est égale à affichage alors on affiche la liste des produits

        //Affichage sous forme de tableau de tous les élements

        
        $a = execute_requete("SELECT * FROM produit ORDER BY id_produit DESC");

        $nombre_colonne = $a->columnCount();

        $content1 .= "<h2>Liste des produits</h2>";
        $content1 .= "<p>Nombre de produits dans la boutique: " . $nombre_colonne ."</p>";

        $content1 .= "<table class='table table-bordered' >";

        $content1 .= '<thead>';
        $content1 .= '<tr>';
    
            

            // debug($nombre_colonne);

            for($i =0; $i < $nombre_colonne; $i++){
                $champ = $a->getColumnMeta( $i );

                $content1 .= "<th scope='col'>$champ[name]</th>";

                // debug($champ);
            }

            $content1 .= "<th scope='col' data-label='Supprimer'>Supression</th>";
            $content1 .= "<th scope='col' data-label='Editer'>Modification</th>";
            $content1 .='</tr>';

            $content1 .= '</thead>';

            while($ligne = $a->fetch(PDO::FETCH_ASSOC)){
            
                $content1 .= '<tbody>';
                $content1 .= '<tr>';

                foreach($ligne as $indice => $value){
                    
                    if($indice == 'photo'){

                        $content1 .=  "<td scope='row' data-label='$indice'> <img src='$value'></td>";
                        
                    }else{
                    $content1 .= "<td scope='row' data-label='$indice'> $value </td>";
                } 
            }
        
            $content1 .= '<td scope ="row"><a href="?action=suppression&id_produit='. $ligne['id_produit'] .'" onclick="return( confirm(\'Voulez-vous supprimer le produit :'. $ligne['titre'] .'\') )"  >❌</a></td>';
            $content1 .= '<td scope ="row"><a href="?action=modification&id_produit='. $ligne['id_produit'] .'"><i class = "far fa-edit"></a></td>';
            $content1 .='</tr>';
            $content1 .= '</tbody>';
            
        }
        $content1 .= "</table>";

//CORRECTION EXERCICE:
    //EXERCICE : Affichez le nombre de produits et la liste des produits sous forme de tableau et faites en sorte d'afficher l'image !!
    //  $r = execute_requete(" SELECT * FROM produit ");

    //  $content .= "<h2>Liste des produits</h2>";
    //  $content .= "<p>Nombre de produits dans la boutique : ". $r->rowCount() ."</p>";
    
    //  $content .= "<table class='table table-bordered'>";
    //      $content .= "<tr>";

    //          $nombre_colonne = $r->columnCount();
    //          //columnCount() : retourne le nombre de colonnes issues du jeu de résultat retourné par la requête ($r)
        
    //          for( $i = 0; $i < $nombre_colonne; $i++ ){

    //              $titre = $r->getColumnMeta( $i );
    //              //getColumnMeta( $int ) : retourne des informations sur les colonnes (de la table) du jeu de résultat retourné par la requête 
    //                  //debug( $titre );

    //              $content .= "<th> $titre[name] </th>";
    //          }
    //      $content .= "</tr>";

    //      while( $ligne = $r->fetch( PDO::FETCH_ASSOC ) ){
    //          //fetch() : permet de retourner un tableau (ici, $ligne) avec les valeurs de la BDD, indéxé par les champs de la table 'produit' grâce au paramètre PDO::FETCH_ASSOC
    //              //Ici, '$ligne' va donc retourner UN tableau correspondant à UNE LIGNE de résultat issu du jeu de résultat de la requête ($r = object PDOStatement)
    //          //On utilise la boucle while pour afficher TOUTES les lignes TANT QU'il y en a à afficher car fetch(), retourne LA ligne suivante d'un jeu de résultat

    //          $content .= "<tr>";
    //              //debug( $ligne );

    //              foreach( $ligne as $indice => $valeur ){

    //                  if( $indice == 'photo' ){ //SI l'indice '$indice' (du tableau '$ligne' retourné par le fetch()) est égal à 'photo' ALORS, on affiche une cellule avec une balise <img> et dans l'attribut 'src', on y met la valeur correspondante '$valeur' qui représente l'adresse pour accéder à l'image en BDD

    //                      $content .= "<td> <img src='$valeur' width='80'> </td>";
    //                  }
    //                  else{ //SINON, c'est que les indices sont différents de 'photo' et donc on affiche les valeurs dans des cellules simples

    //                      $content .= "<td> $valeur </td>";
    //                  }
    //              }

    //          $content .= "</tr>";
    //      }
    //  $content .= "</table>";
    // }



// Affichage des images seulement
        $r = execute_requete("SELECT photo FROM produit ORDER BY id_produit DESC");

        while($affichage = $r->fetch(PDO::FETCH_ASSOC)){

            $content .= "<div class='img_container'>";
            $content .= "<img src='$affichage[photo]' alt='image_produit'>";
            $content .= "</div>";
        };

        
        
        // debug($affichage);

        
}

?>

<h1>Gestion produit</h1>

    <!-- <div class="display">
        <img src="<?php echo $photo_bdd ?>" alt="">
        <p> <?= $_POST['titre'] ?></p>
    </div> -->

<!-- Liens pour gérer soit l'affichage des produits, soit le formulaire d'ajout selon l'action passée dans l'URL -->
    <div class="containerlinks">
        <a href="?action=ajout" class="btn btn-secondary">Ajout Produit</a><br>
        <a href="?action=affichage" class="btn btn-secondary">Affichage produit</a>
    </div>

<br><?= $error ?><br>

<?php if( isset( $_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) : //S'il existe 'action' dans l'URL et que cette action est égale à ajout OU à 'modification' alors on affiche le formulaire 

    if(isset( $_GET['id_produit'] ) ){//S'il existe 'id_produit' dans l'url c'est que l'on est dans le cadre d'une modification

        //récupération des infos à afficher pour pré-remplir le formulaire:
        $r = execute_requete("SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]'");

        //exploitation des données:
        $article_actuel = $r->fetch(PDO::FETCH_ASSOC);
        // debug($article_actuel);


    }

    if( isset($article_actuel['reference'] ) ){ //s'il existe $artcile_actuel['reference'] c'est que l'on est dans le cadre d'une modification et la condition précédente aura été exécutée
        
        $reference = $article_actuel['reference']; //On stocke dans une variable la valeur récupérée en BDD que l'on affichera dans l'attribut value="" de l'input correspondant, "ici reference"

    }else{ //Sinon c'est que je ne suis pas dans le cadre d'une modification donc d'un ajout alors je stocke du vide dans la même variable qui sera affichée dans l'attribut value de l'input correspondant, ici reference
        $reference= '';
    }
    if( isset($article_actuel['categorie'] ) ){ 
        
        $categorie = $article_actuel['categorie']; 

    }else{ 
        $categorie= '';
    }

    //version ternaire:
    $titre = ( isset($article_actuel['titre'] ) ) ? $article_actuel['titre'] : "";
    $description = ( isset($article_actuel['description'] ) ) ? $article_actuel['description'] : "";
    $couleur = ( isset($article_actuel['couleur'] ) ) ? $article_actuel['couleur'] : "";
    $prix = ( isset($article_actuel['prix'] ) ) ? $article_actuel['prix'] : "";
    $stock = ( isset($article_actuel['stock'] ) ) ? $article_actuel['stock'] : "";


    //Taille (select/option)
    if( isset( $artcile_actuel['taille'] ) && $artcile_actuel['taille'] == 'S' ){
        $taille_s  = "selected";
    }else{
        $taille_s = "";
    }

    $taille_m = ( isset( $article_actuel['taille'] ) && $article_actuel['taille'] == 'M' ) ? "selected" : "";
    $taille_l = ( isset( $article_actuel['taille'] ) && $article_actuel['taille'] == 'L' ) ? "selected" : "";
    $taille_xl = ( isset( $article_actuel['taille'] ) && $article_actuel['taille'] == 'XL' ) ? "selected" : "";

    

    //Sexe:

    // if( isset( $article_actuel['sexe'] ) && $article_actuel['sexe'] == 'm' ){

    //     $sexe_m = "checked";
    //     $sexe_f = "";
    // }
    // else{

    //     $sexe_f = "checked";
    //     $sexe_m = "";
    // }

    $femme = ( isset( $article_actuel['sexe'] ) && $article_actuel['sexe'] == 'f' ) ? "checked"  : "";
    $homme = ( isset( $article_actuel['sexe'] ) && $article_actuel['sexe'] == 'm' ) ? "checked"  : "";

    //photo:

    if( isset( $article_actuel['photo'] ) ){ //S'il existe $article_actuel['photo'] c'estr que l'on est dans le cadre d'une modification et donc j'affiche l'image dans le formulaire grâce au chemin récupéré en BDD
        $info_photo = "<i>Vous pouvez uploader une nouvelle photo</i><br>";

        $info_photo .= "<img src='$article_actuel[photo]' width='100'><br>";

        $info_photo .= "<input type= 'hidden' name='photo_actuelle' value='$article_actuel[photo]'>"; //on crée un input hidden donc caché avec en value l'adersse de la photo récupérée en bdd pour pouvoir la récupérer lors de la modification dans le cas où l'on ne télécharge pas de nouvelle photo

        // debug($_POST);

    }else{
        $info_photo = "<br>";
    }
?>

<form method="POST" enctype="multipart/form-data">

    <div class="conteneur2">
        <label for="">Référence</label>
        <input type="text" name="reference" value="<?= $reference?>">
    </div><br>
    

    <div class="conteneur2">
        <label for="">Catégorie</label>
        <input type="text" name="categorie" value="<?= $categorie?>">
    </div><br>


    <div class="conteneur2">
        <label for="">Titre</label>
        <input type="text" name="titre" value="<?= $titre?>">
    </div><br>
    

    <div class="conteneur2">
        <label for="">Description</label>
        <textarea type="text" rows="5" name="description"><?= $description?></textarea>
    </div><br>


    <div class="conteneur2">
        <label for="">Couleur</label>
        <input type="text" name="couleur" value="<?= $couleur?>">
    </div><br>
    

    <div class="options">
        <div class="conteneur2">
            <label for="">Taille</label>
            <hr>
            <select name="taille">
                <option value="S" <?= $taille_s?>>S</option>
                <option value="M" <?= $taille_m?>>M</option>
                <option value="L" <?= $taille_l?>>L</option>
                <option value="XL" <?= $taille_xl?>>XL</option>
            </select>
        </div><br><br>

        <div class="conteneur1">
            <label for="">Sexe</label><br>
            <hr>
            <input type="radio" name="sexe" value="m" <?=$homme?>>Homme
            <input type="radio" name="sexe" value="f" <?=$femme?>>Femme
        </div>
    </div><br><br>

    <div class="conteneur2">
        <label for="">Photo</label>
        <input type="file" name="photo">
    </div><span class="info_photo"><?= $info_photo ?></span><br>

    <div class="conteneur2">
        <label for="">Prix (€)</label>
        <input type="text" name="prix" value="<?= $prix?>">
    </div><br>

    <div class="conteneur2">
        <label for="">Stock</label>
        <input type="text" name="stock" value="<?= $stock?>">
    </div><br>

    <input type="submit" class="btn btn-secondary" value="<?= ucfirst($_GET['action'])?>">
    <!-- Affichage de la valeur de l'action passée dans l'url (ajout ou modification) dans l'attribut value"" de l'input type submit
        ucfirst(): fonction de php qui permet de passer la première lettre en majuscule-->
</form><br>

<?php endif; //fermeture de la condition 'if' pour gérer l'affichage du formaulaire si on clique sur le lien ?>

<div class="conteneur4">
    <?= $content1 ?>
</div>


<div class = 'conteneur3'>
    <?= $content ?>
</div>

<?php require_once "../inc/footer.inc.php"?>