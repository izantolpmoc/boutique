<?php

//fonction debugage: (permet de faire un print_r "amélioré")

// function debug($arg){

//     echo "<div style = 'background:#fdq500; z-index:1000; padding:20px;'>";


//         $trace = debug_backtrace();
//         //debug_backtrace(): fonciton interne de php qui retourne un array avec des infos de l'endroit où l'on fait appel à la fonction


//         echo "<p> Debug demandé dans le fichier: " . $trace[0]['file'] . " à la ligne: " .$trace[0]['line'] . "</p>";

//         echo '<pre>';

//             print_r($arg);
        
//         echo '</pre>';

//     echo '</div>';

// }

function debug($arg){  //version Leeroy
    $br = "<br>";
    $brr = "<br><br>";

    print("<div style='background:#b3b3b3; z-index:1000; padding: 20px; border-radius: 5px'>");

        // contient la ligne où la func est appelée
        $trace = debug_backtrace();

        print("<pre style='font-size: 1.2rem; margin: 0'>");

            print("<p style='font-weight: bold; display: inline; background-color: black; color: white; border-radius: 3px'> File: </p> " . $trace[0]["file"] . $brr);
            print_r("<p style='font-weight: bold; display: inline; background-color: black; color: white; border-radius: 3px'> Line: </p> " . $trace[0]["line"] . $brr);

            print("<p style='background: black; color: white; padding: 20px; margin: 0;'>");
            print("Console debug by naikho | <span style='color: green'>" . date("h:i:s") . "</span> : " . $brr);
            print("<span style='margin-left: 50px; display: block;'>");
                print_r($arg);
            print("</span>");
            print("$br</p>");

        print("</pre>");
    print("</div>");
}

//--------------------------------
// Fonction pour exécuter la requête:

function execute_requete($req){

    global $pdo;

    $pdostatement = $pdo->query( $req );

    return $pdostatement;
}

// $r = execute_requete("SELECT * FROM membre");


//----------------------------------
//Fonction userConnect() : si l'internaute est connecté, on renvoie "true", s'il n'est pas connecté on renvoie "false"

function userConnect(){
    if( !isset( $_SESSION['membre'])){ //Si la session/membre n'existe pas, cela signifie que l'on est pas connecté et donc on renvoit false

        return false;

    }else{ //Sinon, c'est que la session/membre existe et donc que l'on est connecté, on renvoie "true"
        return true;
    }
}

//------------------------------------
//fontion adminConnect(): si l'amdin est connecté, renvoie "true", s'il n'est pas connecté on renvoie "false

function adminConnect(){

    if( userConnect() && $_SESSION['membre']['statut'] == 1){ //Si l'utilisateur est connecté ET qu'il est admin (staut =1) on renvoie "true"
        return true;

    }else{ //Sinon, c'est que son statut est à zéro, on renvoie false
        
        return false;
    }
}

//fonction de redirection lorsque l'on est bloqué par une erreur de type "Cannot modify header information"
function redirige($url){
    die('<meta http-equiv="refresh" content="0;URL='.$url.'">');
    }

//fonction pour créer un panier
function creation_panier(){ //si la session/panier n'existe pas, on la crée

    if( !isset( $_SESSION['panier'] ) ){
        $_SESSION['panier'] = array();

        $_SESSION['panier']['titre'] = array();
        $_SESSION['panier']['id_produit'] = array();
        $_SESSION['panier']['quantite'] = array();
        $_SESSION['panier']['prix'] = array();

    }
}

//fonction d'ajout au panier
function ajout_panier($titre, $id_produit, $quantite, $prix){

    creation_panier(); //Ici, on fait appel à la fonction déclarée ci-dessus
        //SOIT, le panier n'existe pas et on le crée (càd la première fois que l'on tente d'ajouter un produit au panier)
        //SOIT il existe et on l'utilise (puisqu'on ne rentre pas dnas la condition de la fonction creation_panier())

    $index = array_search($id_produit, $_SESSION['panier']['id_produit']);
    // debug($index);
    //array_search(arg 1, arg2);
        //arg 1 :  ce que l'on recherche
        //arg2 : dans quel tableau on effctue la recherche
    //la valeur de retour de la fonction renverra la clé correspondant à l'indice du tableau SI il y a une correspondance de la recher, sinon "false"
        if($index !== false){ //Si index est strictement different de false, c'est que le produit est déjà présetn dans le panier car la fonction array(search) aura trouvé un indice correspondant et donc on va ajouter la quantité avec la nouvelle récuéprée lors de l'ajout au panier
            $_SESSION['panier']['quantite'][$index] += $quantite;
        }else{ //Sinon, c'est que le produit n'est pas dans le panier (la fonction array-search() n'a pas trouvé de correspondance) et donc on unsert toutes les infos dans session/panier


    $_SESSION['panier']['titre'][] = $titre;
    $_SESSION['panier']['id_produit'][] = $id_produit;
    $_SESSION['panier']['quantite'][] = $quantite;
    $_SESSION['panier']['prix'][] = $prix;

    //ATTENTION de bien penser à mettre des crochets VIDES ce qui permet d'ajouter une valeur supplémentaire à un tableau
        }
}

function montant_total(){
    $total = 0;

    for($i = 0; $i < sizeof($_SESSION['panier']['titre']); $i++){
    
        $total += ($_SESSION['panier']['quantite'][$i] * $_SESSION['panier']['prix'][$i]);
        //A chaque tout de boucle (qui correspond au nombre de produits dans le panier), on ajoute le montant total (quantité*prix) pour chaque produit dans la variable $total
    }
    return $total;
}