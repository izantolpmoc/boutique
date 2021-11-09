<?php require_once "inc/header.inc.php"; //inclusion du fichier header?> 
<?php
//EXERCICE : créer la page gestion des commandes :
//restriction d'accès à la page admin :

if(!adminConnect()){
    header('location: index1.php');
    exit;
}

//-------------------------------------------------
//Affichage des commandes (sous forme de tableau) prevoir un lien (sur l'id_commande) pour afficher le détail de la commande 

$r = execute_requete("SELECT * FROM commande");

    $content .= "<br><br>";
    $content .= "<p>Nombre de commandes : ". $r->rowCount() ."</p>";

    $content .= "<table class='table table-bordered'>";
    $content .= "<thead>";
        $content .= "<tr>";

        $nombre_colonne = $r->columnCount();

        for( $i = 0; $i < $nombre_colonne; $i++){

            $titre= $r->getColumnMeta($i);

            $content .= "<th scope='col' data-label='$titre[name]'> $titre[name]</th>";            
            // debug($titre);
            
        }

        $content .= "<th scope='col' data-label='Suppression'>Suppression</th>"; 
        
        
        $content .= "</tr>";
    $content .= "</thead>";

    while( $ligne = $r->fetch(PDO::FETCH_ASSOC)){

    $content .= "<tbody>";
        $content .= "<tr>";

            foreach( $ligne as $indice => $valeur){

                    if($indice == 'id_commande'){

                        $content .= "<td scope='row' data-label='$indice'><a href='?action=affichage&id_commande=". $ligne['id_commande'] ."'>$valeur</a></td>";
                    }else{

                        $content .= "<td scope='row' data-label='$indice'>$valeur</td>";
                

                    }

                
            }
        
        $content .= '<td scope ="row" data-label="Supprimer"><a href="?action=suppression&id_commande= '. $ligne['id_commande'] .'" onclick="return( confirm(\'Voulez-vous supprimer la commande: n°  '. $ligne['id_commande'] .'\') )"  >❌</a></td>';

    

        $content .= "</tr>";
    $content .= "</tbody>";

    }

$content .= "</table>";

//-------------------------------------------------
//affichage du détail de la commande :
//debug( $_GET );
if( isset($_GET['id_commande'])){

    $r = execute_requete("SELECT * FROM details_commande WHERE id_commande = '$_GET[id_commande]' ");


    $detail = $r->fetch(PDO::FETCH_ASSOC);
    // debug($detail);

    $content1 .= "<br>";

    foreach($detail as $indice => $valeur){

        
        $content1 .= "<p> <strong> $indice </strong> : $valeur </p>";

    }

    
}

if(isset($_GET['action']) && $_GET['action'] == 'suppression'){

    execute_requete("DELETE FROM commande WHERE id_commande = '$_GET[id_commande]'");
}




?>

<?php if( isset($_GET['id_commande']) && $_GET['action'] =='affichage') : ?>

<h1>Détail de la commande</h1>

<div class="fil_ariane" id="liens_detail_commande">
    <a href='index1.php'>Accueil</a>/
    <a href='gestion_commandes.php'>Gestion commandes</a>
</div>

<?= $content1 ?>

<?php else : ?>
<h1>Gestion des commandes</h1>
<?= $content ?>

<?php endif; ?>
<?php require_once "inc/footer.inc.php"; //inclusion du fichier footer ?>