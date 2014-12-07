<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../comunes/tuits.css">
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
		 			} else { 
		 				$error = "Error: Login no válido";
					}
			}

			if (isset($_POST['new_nick'], $_POST['new_password'], $_POST['new_password_repeat'])) {
				if (!(existe_nick($_POST['new_nick']))) { // Comprobamos que no exista ese nick ya registrado
					$nick = trim($_POST['new_nick']);
					$password = trim($_POST['new_password']);
					if ($password == trim($_POST['new_password_repeat'])) { // Si las contraseñas coinciden
																																  // Insertamos el usuario en la BBDD
						$con = conectar();
						$res = pg_query($con, "insert into usuarios (nick, password)
																	 values ('$nick', md5('$password'))");
						$_SESSION['usuario'] = devolver_id($nick);
     				header("Location:/twitter/twits/index.php");
					} else { 
						$error_registro = "Error: Las contraseñas no coinciden";
					}
				} else {
					$error_registro = "Error: Ya existe ese nick";
				}

			}

			?>
    	<div id="principal"><?php
      include('../comunes/header.php');?>
			<section class="cont_ancho">
				<div class="login_form"> 
					<h3 class="titulo">Identifícate</h3>
			    <form action ="login.php" method="post">
			    	<label class="etiqueta">Nick: </label>
			    	<input class="nick" type="text" name="nick" autofocus><br><br>
			    	<label class="etiqueta">Contraseña:</label>
			    	<input class="password" type="password" name="password">
			    	<p class="entrar"><input type="image" src="../images/login_button.png"
			    				 value ="Entrar" title="Enviar login"></p>
			    </form><?php
			    if (isset($_POST['nick'],$_POST['password']) && (!isset($_SESSION['usuario']))) {?>
						<h3 class="error"><?= $error ?></h3><?php
					}?>
		    </div> 
		    <p style="display:inline; text-align: left" class="resaltar">¿ Aún no tienes cuenta en JATAS?</p>
				<div class="register_form"> 
					<h3 class="titulo">Regístrate</h3>
			    <form action ="login.php" method="post">
			    	<label class="etiqueta">Nick: </label>
			    	<input class="nick" type="text" name="new_nick" autofocus><br><br>
			    	<label class="etiqueta">Contraseña:</label>
			    	<input class="password" type="password" name="new_password"><br><br>
			    	<label class="etiqueta">Rep. Contraseña:</label>
			    	<input class="password" type="password" name="new_password_repeat">
			    	<p class="entrar"><input type="image" src="../images/register_button.png"
			    				 value ="Registrarse" title="Regístrarse"></p>
			    </form><?php
			    if (isset($_POST['new_nick'],$_POST['new_password'],$_POST['new_password_repeat'])
			    	  && (trim($_POST['new_password']) != trim($_POST['new_password_repeat']))) {?>
						<h3 class="error"><?= $error_registro ?></h3><?php
					}?>
		    </div> 
	    </section><?php
      	include('../comunes/footer.php');?>
	    </div>
	</body>
</html>