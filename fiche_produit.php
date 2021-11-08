<?php require_once "inc/header.inc.php"; //inclusion du fichier header?> 

<?php
//debug( $_GET );
//---------------------------------------------
//EXERCICE : 
//Création de la page fiche_produit.php

//restreindre l'accès à la page SI on a cliqué sur un lien de la page d'accueil (et donc fait passer l'id dans l'URL) SINON, on le redirige vers la page d'accueil

if( empty($_GET['id_produit'])){

    redirige('index1.php');

}else{
    // debug($_GET['id_produit']);

    $r = execute_requete("SELECT DISTINCT categorie FROM produit WHERE id_produit = '$_GET[id_produit]'");
    $categorie = $r->fetch(PDO::FETCH_ASSOC);
    // debug($categorie['categorie']);



//---------------------------------------------
//créer 2 liens : (file d'ariane)
	//l'un pour permettre de retourner à l'accueil
	//l'autre pour retourner à la catégorie précédente


//affichez la liste des informations des produits SAUF l'id_produit et le stock
//Pour l'image, on affichera l'image et non pas l'adresse de la bdd

    $r = execute_requete("SELECT * FROM produit WHERE id_produit = '$_GET[id_produit]'");

    $affichage = $r -> fetch(PDO::FETCH_ASSOC);
        // debug($affichage);

    foreach($affichage as $indice => $valeur){

        if($indice == 'id_produit' || $indice == 'stock' || $indice == 'photo' || $indice == 'titre'){

            $content .= '';
        }else{
        $content .= "<h3>$indice</h3>";
        $content .= "<p>$valeur</p>";

        }
    }


    $content1 .= "<div>";
    $content1 .= "<h2>$affichage[titre]</h2><br>";
    $content1 .= "<img src='$affichage[photo]'>";
    $content1 .= "</div>";

    $stock = $affichage['stock'];
    // debug($stock);


    if($affichage['stock']>0){

        $content .= "<form method='post' action='panier.php'>";
            $content .= "<label><strong>Quantité: <strong></label>";
                $content .= "<select name='quantite'> ";

            

            for($i = 1; $i < $stock; $i++){

                $content .= "<option value='$i'>$i</option>";
                
            }
            
            $content .= "</select><br><br>";

            $content .= "<input type='hidden' name='id_produit' value='$affichage[id_produit]'>";

            $content .= "<input type='submit' name='ajout_panier' value='Ajouter au panier' class='btn btn-secondary'>";
        $content .= "</form>";
    }else{

        $content .= "<p style='color:red'>en rupture</p>";
    }

    // if( $produit['stock'] > 0 ){ //Si le stock est supérieur à ZERO on affiche le stock

    //     $content .= "<form method='post' action='panier.php'>";
    //     //Ici, l'attribut action="panier.php" : permet d'ete redirigé sur le fichier 'panier.php' lorsque l'on valide le formulaire. Les données récupérées par $_POST seront donc traitées sur le fichier 'panier.php'
    
    //         $content .= "<label> <strong> Quantite </strong> : </label>";
    //         $content .= "<select name='quantite' >";
    //             for( $i = 1; $i <= $produit['stock']; $i++ ){
    
    //                 $content .= "<option value='$i' > $i </option>";
    //             }
    //         $content .= "</select><br><br>";
    
    //         $content .= "<input type='submit' name='ajout_panier' value='Ajouter au panier' class='btn btn-secondary' >";
        
    //     $content .= "</form>";
    // }
    // else{ //SINON, c'est que le stock est à zero
    
    //     $content .= "<p> Rupture de stock </p>";
    // }
    




}
//---------------------------------------------
//gérer le stock à part !
	//SI il est supérieur à ZERO, on affiche le nombre de produits disponibles dans un <select> avec le nombre d'options correspondant au stock
	//SINON, on affiche rupture de stock

    
    ?>
<h1>Fiche produit</h1>
<div class="fil_ariane">
    <a href='index1.php'>Accueil</a>/
    <a href='index1.php?action=display&categorie=<?=$categorie['categorie']?>'><?=ucfirst($categorie['categorie'])?></a>
</div>

<div class="conteneur_fiche">
    <div class="conteneur_description">
        <?= $content ?>
    </div>
    <div class="conteneur_img">
        <?= $content1 ?>
    </div>
</div>
<?php require_once "inc/footer.inc.php"; //inclusion du fichier footer ?>
