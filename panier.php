<?php require_once "inc/header.inc.php"; //inclusion du fichier header?> 

<?php
debug($_POST);

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

    debug($_SESSION);
}
?>

    <h1>Panier</h1>

<?php require_once "inc/footer.inc.php"; //inclusion du fichier footer ?>