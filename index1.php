<?php require_once "inc/header.inc.php"; //inclusion du fichier header?> 
<?php



//Affichage des produits:

//je récupère les différentes catégories de la table produit:
$r = execute_requete("SELECT DISTINCT categorie FROM produit");

$content .= "<div class='row'>";

        //Affichage des catégories
        $content .= "<div class='col-3 index_content'>";
                $content .= "<div class='list-group-item'>";

                while($categorie = $r->fetch(PDO::FETCH_ASSOC)){
                        // debug($categorie);
                        $content .= "<a href='?action=display&categorie=$categorie[categorie]' class='list-group-item'>". ucfirst($categorie['categorie']) . "</a>";
                }

                $content .= "</div>";
        $content .= "</div>";


//EXERCICE : Affichez les produits correpondants à la catégorie cliquée
$content .= "<div class='col-8 offset-1'>";
        $content .= "<div class='row'>";

if(isset($_GET['action']) && $_GET['action'] == 'display'){

        $r = execute_requete("SELECT * FROM produit WHERE categorie = '$_GET[categorie]' ORDER BY id_produit DESC");


        while($affichage = $r->fetch(PDO::FETCH_ASSOC)){

                // $content .= "<div class='col-2'>";
                // $content .= "<div class='thumbnail' style='border:1px solid #eee'>";
                
                //         $content .= "<a href='fiche_produit.php?id_produit=$affichage[id_produit]'>";
                //         $content .= "<img src='$affichage[photo]' alt='image_produit' width='80'>";
                //         $content .= "<p>$affichage[titre]</p>";
                //         $content.="<p> $affichage[prix]€</p>";
                //         // debug($affichage);

                //         $content .= "</a>";
                //         if($affichage['stock'] == 0){
                //                 $content .= "<p style='color:red'>en rupture</p>";
                //         }

                // $content .= '</div>';
                // $content .= '</div>';
                // $content .= '</div>';



			$content .= '<div class="card" style="width: 18rem;">';
				$content .= "<a href='fiche_produit.php?id_produit=$affichage[id_produit]'>";
  				$content .= "<img src='$affichage[photo]' alt='image_produit' class='card-img-top'>";
  				$content .= '<div class="card-body">';
    				$content .= "<p> $affichage[titre]</p>";
					$content.="<p> $affichage[prix]€</p>";
					$content .= "</a>";
					if($affichage['stock'] == 0){
							$content .= "<p style='color:red'>en rupture</p>";
					}
  				$content .= "</div>";
			$content .= "</div><br><br>";
        };
	
					
        
};
// $content .= '</div>';

	$content .= "</div>";
$content .= "</div>";




//CORRECTION EXERCICE
	//EXERCICE : Affichez les produits correpondants à la catégorie cliquée
	//debug( $_GET );

//         $content .= "<br><br>";

// 	$content .= "<div class='col-8 offset-1'>";
// 		$content .= "<div class='row'>";

// 		if( isset($_GET['categorie'] ) ){ //Si il existe 'categorie' dans l'URL, c'est que l'on a forcément cliquée sur une catégorie du menu !

// 			$r = execute_requete(" SELECT * FROM produit WHERE categorie = '$_GET[categorie]' ");

// 			while( $produit = $r->fetch(PDO::FETCH_ASSOC) ){

// 				//debug( $produit );

// 				$content .= "<div class='col-2'>";
// 					$content .= "<div class='thumbnail' style='border:1px solid #eee'>";

// 						$content .= "<a href=''>";
// 						//Ici, je créer un lien <a> pour accéder au fichier fiche_produit.php' et pour récupérer les indois du produit sur lequel on a cliqué, on fait passer l'id dans l'URL

// 							$content .= "<img src='$produit[photo]'>";

// 							$content .= "<p> $produit[titre]</p>";
// 							$content .= "<p> $produit[prix]</p>";

// 						$content .= "<a>";

// 						if($produit['stock'] == 0){
// 		                    $content .= "<p style='color:red'>en rupture</p>";
// 						}

// 					$content .= '</div>';
// 				$content .= '</div>';
// 			}
// 		}else{
//                         $content .= "<h3>Veuillez choisir une catégorie.</h3>";
//                 }

// 		$content .= '</div>';
// 	$content .= '</div>';

// $content .= "</div>";









?>

		<div class="title_h1 w-100">
        	<h1 id="index_h1">Bienvenue sur le Grand Marché de Noël</h1>
			<img src="assets/img/deco_noel.png" alt="">
		</div>
        <!-- <img id="photo_accueil" src='https://images.pexels.com/photos/3444345/pexels-photo-3444345.png?auto=compress&cs=tinysrgb&dpr=3&h=750&w=1260' alt="voiture, sapin noel"> -->

        <?= $content //affichage du contenu?>

<?php require_once "inc/footer.inc.php"; //inclusion du fichier footer ?>
