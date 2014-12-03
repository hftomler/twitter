<?php session_start(); ?>
<!DOCTYPE html> 
<html>
	<head>
		<meta charset="utf-8"/>
		<title>LOGIN</title> 
	</head>
	<body><?php
		
			require '../comunes/auxiliar.php';?>
		    <form action ="login.php" method="post">
		    	<label>Nombre: </label>
		    	<input type="text" name="nick"><br>
		    	<label>Contraseña:</label>
		    	<input type="password" name="password"><br>
		    	<input type="submit" value ="Entrar">   
		    </form>
	</body>
</html>