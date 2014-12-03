<?php session_start(); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>CERRAR SESIÓN-LOGOUT</title>
	</head>
	<body><?php
		$_SESSION = array();
		
		// Esta parte destruira la sesión pero la información de sesión no la destruira.
		if(ini_get("session.use_cookies"))
		{
			$params = session_get_cookie_params();
			setcookie(session_name(), '', 1,
						$params["path"], $params["domain"],
						$params["secure"], $params["httponly"]);		
		}
		
	   // Destruye la sesión 		
		session_destroy();    				
		// Vuelve a la ventana de login
		header("Location: login.php"); ?>
			
	   </p><b>Cerrando sesión...</b></p>
	  
	</body>
</html>