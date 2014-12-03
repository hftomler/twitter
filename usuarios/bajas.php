<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Borrar Usuarios</title>
  </head>
  <body>
  <p><?= $id = 2; ?></p><?php
  
  

   $_SESSION['usuario'] = 1;


  function comprobar_usuario(){
      if (!isset($_SESSION['usuario']))
        header("Location: ../usuarios/login.php"); 
  }

  function comprobar_existe($id, $con){
  	$res = pg_query($con,"select id
  		                  	from usuarios
  		                  where id::text = '$id'");

  	if (pg_num_rows($res) != 1)
  	{ 
    	throw new Exception("El usuario con el id $id no existe"); 
  	} 
  }

  function comprobar_borrado($res){
  	if (pg_affected_rows($res) != 1)
  	{
  		throw new Exception("No se ha podido borrar el usuario");
  	}
  }

  function pintar_usuarios($id,$con){
  	$res = pg_query($con,"select * 
  		  					from usuarios
  		  				  where id::text = '$id'");

    $fila = pg_fetch_assoc($res); 


  	$cols = array('nick' => 'Nick',
  		          'password' => 'Contraseña',
  		          'rol_id' => 'Id rol'); ?>

    <table border="1">
      <thead><?php
        foreach ($cols as $k => $v) : ?> 
          <th><?= $v ?></th><?php
        endforeach; ?>
      </thead>
          <tbody><?php
            for ($i = 0; $i < pg_num_rows($res); $i++): 
              $fila = pg_fetch_assoc($res,$i); ?>
              <tr><?php
                 foreach ($cols as $k => $v) : ?> 
                    <td><?= $fila[$k] ?></td><?php
                 endforeach; ?>    
              </tr><?php
            endfor; ?>
          </tbody>
        </table>
        <form action="bajas.php" method="get">
          <input type="hidden" name="id" value="<?= $fila['id'] ?>">
          <p>¿Desea eliminar el usuario?</p>
          <input type="submit" value="Eliminar">
          <a href="index.php"><input type="button" value="Volver"></a>
        </form><?php
  }
  require '../comunes/auxiliar.php';

  $con = conectar();
  $res = pg_query($con,"begin");
  $res = pg_query($con, "lock table usuarios in share mode");

  if (isset($_GET['id']))
  {
    $id = trim($_GET['id']);
  }

  try
  {
  	comprobar_existe($id,$con);
  	pintar_usuarios($id,$con);
  	if(isset($_GET['id'])){
  		$res = pg_query($con,"delete from usuarios
  		                  where id = $id");
  		comprobar_borrado($res); ?>
  		<p>El usuario se ha borrado correctamente</p><?php
  		header("Location: login.php");
    }
  }catch(Exception $e) { ?>
  	<p>Error:<?= $e->getMessage() ?></p><?php
  }

  $res = pg_query($con,"commit");
  pg_close($con);  ?>
  </body>
</html>