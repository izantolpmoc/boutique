<?php 

//Création/ouverture du fichier session
session_start();

//Première ligne de code, se positionne en haut et en premier avant tout traitement php

//---------------------------------------
// connexion à la bdd: 'boutique'

$pdo = new PDO('mysql:host=localhost;dbname=boutique', 'root', '', array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));


//---------------------------------------
// definition d'une constante

define('URL', "http://localhost/PHP/boutique/");
//correspond à l'URL de la racine de notre site


//-----------------------------------------
//definition des variables:

$content = '';//variable prévue pour recevoir du contenu
$error = '';//variable prévue pour recevoir les messages d'erreur
$content1 = '';

//------------------------------------------
require_once "fonction.inc.php";





