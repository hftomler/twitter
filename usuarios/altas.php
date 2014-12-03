<?php session_start() ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Alta de Usuarios</title>
  </head>
  <body><?php
    require '../comunes/auxiliar.php';

    $con = conectar();
    $errores = [];

    $_SESSION['usuario'] = 9;

    function comprobar_usuario(){
      if (!isset($_SESSION['usuario']))
        header("Location: ../usuarios/login.php"); 
    }

    function comprobar_admin(){
      global $con;

      $usuario = trim($_SESSION['usuario']);

      $res = pg_query_params($con, "select *
                               from usuarios
                              where id = $1", [$usuario]);

      if (pg_num_rows($res) == 1){
        $fila = pg_fetch_assoc($res);

        if($fila['rol_id'] != 1){
          $errores[] = "Error: Usuario sin permisos.";

          comprobar_errores();
        }
      }
    }

    function comprobar_errores(){
      global $errores;

      if(!empty($errores))
        throw new Exception();    
    }

    function obtener_usuario(){
      $usuario = (isset($_SESSION['usuario'])) ?
                                        (int) trim($_SESSION['usuario']) : "";
      return $usuario;
    }

    function form_nuevo_usuario(){ 
      global $con; ?>

      <form action="altas.php" method="post">
        <label for="nick">Nick</label>
        <input type="text" name="nick"> <br />
        <label for="pass">Contraseña</label>
        <input type="password" name="password"> <br />
        <label for="rol">Rol</label>

        <select name="rol"> <?php
          $res = pg_query($con, "select * from roles");
          $fila = pg_fetch_all($res);

          for ($i = 0; $i < count($fila); $i++) { 
            if($fila[$i]['descripcion'] != "Invitado"){ ?>
              <option name="id" value="<?= $fila[$i]['id'] ?>"> 
                <?= $fila[$i]['descripcion'] ?>
              </option> <?php
            }
          }   ?>

        </select> <br />

        <input type="submit" value="Alta">        
      </form>

      <a href="index.php"><button>Cancelar</button></a><?php
    }

    function limpiar_datos(){
      foreach ($_POST as $key => $value)
        $_POST["$key"] = trim($value);
    }

    function comprobar_restricciones(){
      global $errores;

      if(!(strlen($_POST['nick']) <= 15))
        $errores[] = "Error: Nick debe ser inferior o igual a 15 caracteres";

      if(strlen($_POST['password']) == 0){
        $errores[] = "Error: Debe introducir una contraseña";
      }
    }

    function pintar_usuario_insertado(){
      global $con;

      $nick = trim($_POST['nick']);

      $res = pg_query_params($con, "select *
                                      from usuarios
                                     where nick = $1", [$nick]);

      if (pg_num_rows($res) == 1){ ?>
        <p>El usuario <strong><?= $nick ?></strong> ha sido insertado correctamente</p> <br /> 
        <a href="altas.php"><button>Volver</button></a> <?php
      }

    }

    function insertar(){
      global $con;
      global $errores;

      extract($_POST);
      $password = md5($password);

      $res = pg_query_params($con, "select *
                               from usuarios
                              where nick = $1", [$nick]);

      if(pg_num_rows($res) == 0){
        
        $res = pg_query($con, "begin");
        $res = pg_query($con, "lock table usuarios in share mode");
        $res = pg_query_params($con, "insert into usuarios 
                                                  (nick, password, rol_id)
                                      values ($1, $2, $3)", 
                                                  [$nick, $password, $rol]);

        $res = pg_query($con, "commit");
      }else{
        $errores[] = "Error: el nick ya existe";

        comprobar_errores();
      }
    }

    try{
      if(!isset($_POST['nick'])){
        comprobar_usuario();
        form_nuevo_usuario();
      }else{
        comprobar_admin();
        limpiar_datos();
        comprobar_restricciones();
        comprobar_errores();
        insertar();
        pintar_usuario_insertado();
      }
    }catch(Exception $e){
      foreach ($errores as $v) { ?>
        <p><?= $v ?></p> <?php
      } ?>
      
      <a href="altas.php"><button>Volver</button></a> <?php

    }finally {
      pg_close($con);
    } ?>
  </body>
</html>