<?php session_start(); ?>
<!DOCTYPE html> 
<html>
	<head>
		<meta charset="utf-8"/>
		<title>LOGIN</title> 
	</head>
	<body><?php
		
			require '../comunes/auxiliar.php';
			
			if(isset($_POST['nick'],$_POST['password'])) {
				$nick = trim($_POST['nick']);
				$password = trim($_POST['password']);
				$con = conectar();
				$res = pg_query($con, "select id 
									from usuarios
									where nick = '$nick' and
									  password = md5('$password')"); 
		    	if(pg_num_rows($res) > 0) {
		     		$fila = pg_fetch_assoc($res);

						$_SESSION['usuario'] = $fila['id'];
						if (isset($_SESSION['url'])) {
		     			header("Location:".$_SESSION['url']);
	     			} else {
	     				header("Location:/twitter/twits/index.php");
	     			}
		 			} else { ?>
			 			<h3>Error: Contraseña no válida </h3><?php
					}
			}?> 	
			 
		    <form action ="login.php" method="post">
		    	<label>Nombre: </label>
		    	<input type="text" name="nick"><br>
		    	<label>Contraseña:</label>
		    	<input type="password" name="password"><br>
		    	<input type="submit" value ="Entrar">   
		    </form>
	</body>
</html>