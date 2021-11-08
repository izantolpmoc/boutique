<?php require_once "inc/header.inc.php"; //inclusion du fichier header?> 

<?php

// debug($_POST);

if( isset($_POST['ajout_panier'] ) ){ //Ici, on vérifie l'existence d'un "submit" dans le fichier_produit.php où 'ajout_panier' provient de l'attribut name "ajout_panier" de l'input type='submit' du formulaire de fiche_produit.php => donc lorsque l'on ajoute un produit au panier


    $r = execute_requete("SELECT titre, prix FROM  produit WHERE id_produit = '$_POST[id_produit]'");
    //Ici, $_POST[id_produit] provient de l'input type hidden dans le formulaire du fichierfiche_produit.php

    $produit = $r->fetch(PDO::FETCH_ASSOC);
    // debug($produit);

    //appel de la fonction creation_panier
    creation_panier();
    ajout_panier($produit['titre'], $_POST['id_produit'], $_POST['quantite'], $produit['prix']);
    //Ici, la quantité et lidproduit proviennet du formulaire de fiche produit.php (donc du post)
    //le titre et le prix proviennent de la requete ci dessus

    // debug($_SESSION);

}
//--------------------------------------------
//--------------------------------------------
//--------------------------------------------
//EXERCICE : gérer la validation du panier : SI on valide le panier

if(isset( $_POST['payer']) && $_POST['payer'] == "Payer"){

    $id_membre_connecte = $_SESSION['membre']['id_membre'];
        // debug($id_membre_connecte);

    $montant_commande = montant_total();
        // debug($montant_commande);

    //insertion dans la table commande (NOW())

    $pdo->exec(" INSERT INTO commande( id_membre, montant, date ) 
    
    VALUES( $id_membre_connecte, $montant_commande, NOW() ) 
    ");
    // execute_requete("INSERT INTO commande( id_membre, montant, date) 
    //             VALUES( $id_membre_connecte, $montant_commande, NOW())
    //             ");
                
    //récupération du numéro de la commande (lastInsertId())

    $id_commande = $pdo -> lastInsertId();
    
    $content .= "<div class ='alert alert-success'> Merci pour votre commande, le numéro de la commande est le: $id_commande</div>";
    //insertion dans la table details_commande (for...)

    for( $i = 0; $i < sizeof( $_SESSION['panier']['id_produit']); $i++){
        execute_requete("INSERT INTO details_commande( id_commande, id_produit, quantite, prix)
        VALUES( $id_commande,
        '".$_SESSION['panier']['id_produit'][$i]."',
        '".$_SESSION['panier']['quantite'][$i]."',
        '".$_SESSION['panier']['prix'][$i]."') ");

    //modification du stock en conséquence de la commande (update)

    execute_requete("UPDATE produit SET
    stock = stock - " . $_SESSION['panier']['quantite'][$i] ."
    
    WHERE id_produit = " . $_SESSION['panier']['id_produit'][$i] ." )");
    }

        //vider le panier
        unset( $_SESSION['panier'] );
        //unset( $arg ) : permet de supprimer une variable ($arg)
    
}

if(isset( $_GET['action']) && $_GET['action'] == 'vider'){
    unset($_SESSION['panier']);
    //Cette portion de code se situe avant l'affichage car on détruit la session/panier et donc il n'y a plus rien à afficher
}

//--------------------------------------------
//Autre EXERCICE : donnez la possibilité à l'utilisateur de vider son panier au click via un lien <a>

//--------------------------------------------

//EXERCICE: affichage du contenu du panier sous forme de tableau
    //si le panier est vide on indiquera qu'il est vide sinon on affichera les infos du panier
// $total = 0;

// if(isset($_SESSION['panier']['id_produit'])){ //Si la session/panier/id_produit est vide, c'est que je n'ai rien dans mon panier

    

//     for($i = 0; $i < sizeof($_SESSION['panier']['titre']); $i++){

//         global $total;
//         $prixtotal_articles = ($_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i]);


//         $content .= "<div class='panier'>";
//         $content .= "<h3>".$_SESSION['panier']['titre'][$i]."</h3>";
        
//         $content .= "<p> Quantité: ".$_SESSION['panier']['quantite'][$i]. " • ". $_SESSION['panier']['prix'][$i]."€</p>";
//         // $content .= "<p>".$_SESSION['panier']['prix'][$i]."€</p>";
//         $content .= "<p>Total: $prixtotal_articles" ."€ </p>";
//         $content .= "</div>";

        
//         $total += $prixtotal_articles;

        
//     }
    // $content .= "<div class='total'>";
    // $content .= "<h3>Total: ". $total."€</h3>";
    // $content .= "</div>";

// }else{
//     $content .= "<p>Vous n'avez pas encore d'articles dans votre panier</p>";
// }

//CORRECTION EXERCICE:
    //EXERCICE: Affichage du contenu du panier (sous forme de tableau)
    //SI le panier est vide on indiquera qu'il est vide sinon on affichera les infos du panier
    $content .= "<table class='table'>";
    $content .= "<tr>
                    <th>Titre</th>
                    <th>Quantite</th>
                    <th>Prix</th>
                </tr>";

    if( empty($_SESSION['panier']['id_produit']) ){ //Si la session/panier/id_produit est vide, c'est que je n'ai rien dans mon panier

        $content .= "<tr> <td colspan='3'> Votre panier est vide !</td> </tr>";
    }
    else{ //SINON, c'est qu'il y a des produits dans le panier et donc on les affiche 

        for( $i = 0; $i < sizeof( $_SESSION['panier']['titre'] ); $i++ ){

            $content .= "<tr>";
                $content .= "<td>" . $_SESSION['panier']['titre'][$i] . "</td>";
                $content .= "<td>" . $_SESSION['panier']['quantite'][$i] . "</td>";

                //ici, on multiplie la quantite avec le prix:
                $prix_total = $_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i] . "€";

                $content .= "<td>" . $prix_total . "</td>";
            $content .= "</tr>";

        }
         //Affichage du montant total :
         $content .= "<tr>
         <td colspan='2'>&nbsp;</td>
         <th>". montant_total() ."€ </th>  
     </tr>";

     if( userConnect() ){//Si l'utilisateur est connecté, on affiche un bouton pour valider la commande
        $content .= "<tr>";
        $content .= "<td colspan='3'>";

            $content .= "<form method='post'>";

                $content .= "<input type='submit' name='payer' value='Payer' class='btn btn-secondary'>";

            $content .= "</form>";

        $content .= "</td>";
    $content .= "</tr>";
    }else{//Sinon, c'est que l'on n'est pas connecté et donc on affiche des liens pour que l'internaute se connecte ou s'inscrive

        $content .= "<tr>";
        $content .= "<td colspan='3'>";

            $content .= "<p>Veuillez vous 
                            <a href='". URL ."connexion.php'> connecter</a> ou vous
                            <a href='". URL ."inscription.php'> inscrire </a>
                        </p>";

        $content .= "</td>";
    $content .= "</tr>";
    }

      //Vider le panier :
      $content .= "<tr>";
      $content .= "<td>";

          $content .= "<a href='?action=vider' class='btn btn-secondary' > Vider le panier </a>";

      $content .= "</td>";
  $content .= "</tr>";
    }


$content .= "</table>";

// $content .= "<div class='total'>";
//     $content .= "<h3>Total: ". montant_total()."€</h3>";
// $content .= "</div>";


?>

    <h1>Panier 🛒</h1>

    <?= $content ?>

<?php require_once "inc/footer.inc.php"; //inclusion du fichier footer ?>