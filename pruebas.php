<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="comunes/tuits.css">
    <title>PRUEBAS</title> 
  </head>
  <body><?php
    
      require 'comunes/auxiliar.php';
      $array=buscar_hashtags();
      print_r($array);?>
  </body>
</html>