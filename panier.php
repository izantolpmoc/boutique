<?php require_once "inc/header.inc.php"; //inclusion du fichier header?> 

<?php
debug($_POST);

if( isset($_POST['ajout_panier'] ) ){ //Ici, on vérifie l'existence d'un "submit" dans le fichier_produit.php où 'ajout_panier' provient de l'attribut name "ajout_panier" de l'input type='submit' du formulaire de fiche_produit.php => donc lorsque l'on ajoute un produit au panier


}
?>

    <h1>Panier</h1>

<?php require_once "inc/footer.inc.php"; //inclusion du fichier footer ?>