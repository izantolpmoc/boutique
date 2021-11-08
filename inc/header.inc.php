<?php require_once "init.inc.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon site</title>

	<!-- CDN CSS BOOTSTRAP -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <!-- CDN FONT AWESOME-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

	<!-- CSS PERSO -->
	<link rel="stylesheet" href="assets/css/style.css">

</head>
<body>
    
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top ">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?php echo URL ?>index1.php">🎅 Grand Marché de Noël </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class=" collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="<?php echo URL ?>index1.php">Accueil</a>
        </li>

        <?php 
        if(!userConnect()) : //Si l'utilisateur n'est pas connecté, on affiche les liens inscription et connexion
        ?>

        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>inscription.php">Inscription</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>connexion.php">Connexion</a>
        </li>

        <?php else: //Sinon c'est que l'on est  connecté et donc on affiche les liens: profil et déconnexion

        ?>

        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>profil.php">Profil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>connexion.php?action=deconnexion">Déconnexion</a>
          <!-- La déconnexion se fera sur le fichier connexion.php -->
        </li>


        <?php endif; ?>

        <li class="nav-item">
          <a class="nav-link" href="<?php echo URL ?>panier.php">Panier</a>
        </li>
        
        <?php if( adminConnect() ): //Si l'admin est connecté, on affiche le menu Backoffice
        ?>

        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Backoffice
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
            <li><a class="dropdown-item" href="<?php echo URL ?>admin/gestion_produit.php">Gestion produit</a></li>
            <li><a class="dropdown-item" href="<?php echo URL ?>gestion_membre.php">Gestion membres</a></li>
            <li><a class="dropdown-item" href="#">Something else here</a></li>
          </ul>
        </li>

        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<div class="container">