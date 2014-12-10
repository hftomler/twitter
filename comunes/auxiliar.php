<?php

  function conectar()
  {
   return pg_connect("host=localhost user=twitter password=twitter
                       dbname=twitter");
  }

// FUNCIONES RELACIONADAS CON EL USUARIO

  function comprobar_usuario() // Comprueba si existe la variable de Sesión ['usuario'] y si no existe
                               // redirige a la página de login.
  {
    if (!isset($_SESSION['usuario'])) {
      $_SESSION['url'] = $_SERVER["REQUEST_URI"];
      header("Location: ../usuarios/login.php");
    }
    return $_SESSION['usuario'];
  }

  function existe_nick($nick) { // Devuelve verdadero si existe el nick proprocionado, falso si no existe.
    $con = conectar();

    $res = pg_query($con, "select id from usuarios
                           where nick = '$nick'");

    pg_close();

    if (pg_num_rows($res) == 1) {
      return true;
    } else {
      return false;
    }
  }

  function devolver_id($nick) { // Devuelve el id de un nick facilitado.
                                // Devuelve falso si no existe ese nick.
    if (existe_nick($nick)) {
      $con = conectar();
      $res = pg_query($con, "select id from usuarios
                           where nick = '$nick'");
      pg_close();

      if (pg_num_rows($res) == 1) {
        $fila = pg_fetch_assoc($res);
        return $fila['id'];
      }
    } else {
      return false;
    }
  }

  function usuario_max() { // Devuelve el número de identifidor de usuario máximo.
    
    $max = 0; 
    $con = conectar();

    $res = pg_query($con, "select max(id) as max from usuarios");

    if (pg_affected_rows($res) == 1) {
      $fila = pg_fetch_assoc($res, 0);
      $max = $fila['max'];
    }
    pg_close();
    return $max;
  }

  function usuario_min() { // Devuelve el identificador de usuario mínimo
    
    $min = 1; 
    $con = conectar();

    $res = pg_query($con, "select min(id) as min from usuarios");

    if (pg_affected_rows($res) == 1) {
      $fila = pg_fetch_assoc($res, 0);
      $min = $fila['min'];
    }
    pg_close();
    return $min;
  }

  function devolver_nick($usuario_id) // Devuelve el nick de un identificador de usuario.
  {
    $con = conectar();

    $res = pg_query($con, "select nick from usuarios where id::text = '$usuario_id'");

    if (pg_affected_rows($res) == 1) {
      $fila = pg_fetch_assoc($res);
      $nick = $fila['nick'];
    }

    pg_close();
    return $nick;
  }

  function devolver_lista_usuarios($usuario_id, $order_column, $follow) { 
        /* Devuelve todos los usuarios excepto el usuario indicado. La variable $follow es booleana
        e indica si lo que se devuelve es los seguidores (true) o los tuits(false) de cada usuario.*/
  $con = conectar();
  if (!$follow) { 

    $res = pg_query($con, "select u.nick as nick, u.id as id,
                                  (select count(t.usuario_id) as tuits
                                   from tuits as t 
                                   where u.id = t.usuario_id)
                          from usuarios as u 
                          where u.id::text != '$usuario_id'
                          order by $order_column desc");
  } else {
    $res = pg_query($con, "select u.nick as nick, u.id,
                                 (select count(f.id_fd) as nfollowers
                                 from followers f 
                                 where u.id = f.id_fd)
                          from usuarios as u
                          order by nfollowers desc, $order_column asc;");
  }
    pg_close();
    return $res; 
  }

// FUNCIONES RELACIONADAS CON LOS TUITS

  function contar_tuits($usuario) { // Cuenta los tuits de un usuario determinado, si la variable
                                    // proporcionada vale 0 cuenta todos los tuits.
    $con = conectar();
    if ($usuario == 0) {
      $res = pg_query($con, "select count(*) as ntuits from tuits");  
    } else {
      $res = pg_query($con, "select count(*) as ntuits from tuits where usuario_id::text = '$usuario'");
    }
    if (pg_affected_rows($res) == 1) {
      $fila = pg_fetch_assoc($res);
      $ntuits = $fila['ntuits'];
    }
    pg_close();

    return $ntuits;    
  }

  function contar_followers($usuario) { // Cuenta los followers de un usuario determinado.

    $con = conectar();
    $res = pg_query($con, "select count(*) as nfollowers from followers where id_fd::text = '$usuario'");
    if (pg_affected_rows($res) == 1) {
      $fila = pg_fetch_assoc($res);
      $nfollowers = $fila['nfollowers'];
    }
    pg_close();

    return $nfollowers;
  }


  function devolver_tuits($usuario_id, $tpp, $pag) { // DEVUELVE LOS $tpp TUITS DE UN USUARIO ESPECIFICADOS
                                                     // EMPEZANDO POR LA PÁGINA $pag.
    $con = conectar();
    $res = pg_query($con, "select id, mensaje, from_id, to_char(fecha, '\"<b>\"dd-mm-yyyy\"</b>
                                               <br/><b>Hora:</b> \"HH24:MI') as fecha_formateada
                                  from tuits 
                                  where usuario_id::text = '$usuario_id'
                                  order by fecha desc
                                  limit $tpp
                                  offset ($pag-1)*$tpp");
    pg_close();

    return $res;
  }

  function devolver_tuit($id) { // Devuelve un tuit determinado para edición.
    $con = conectar();
    $res = pg_query($con, "select id, mensaje, to_char(fecha, 'dd-mm-yyyy\" a las \"HH24:MI:SS') as fecha_formateada
    from tuits where id::text = '$id'");
    if (pg_affected_rows($res) == 0) {
      $GLOBALS['mensajes_estado']['mensaje'] = "Se ha producido un error al crear el tuit";
      $GLOBALS['mensajes_estado']['tipo'] = "err";
    }
    pg_close();
    return $res;
  }

  function devolver_ntuits($ntuits_dev, $hashtag) { 
                            // DEVUELVE EL NÚMERO DE ntuits MÁS RECIENTES DE UN hashtag ESPECIFICADO
                            // SI EL hashtag ES UNA CADENA VACÍA DEVUELVE LOS ntuits MÁS RECIENTES.

    if ($hashtag !="" && strlen($hashtag) >3) {
      $hashtag = "#" . $hashtag;
      $con = conectar();
      $res = pg_query($con, "select id, mensaje, from_id, to_char(fecha, '\"<b>\"dd-mm-yyyy\"</b>
                                                 <br/><b>Hora:</b> \"HH24:MI') as fecha_formateada
                                    from tuits 
                                    where upper(mensaje) like upper('%$hashtag%')
                                    order by fecha desc
                                   limit $ntuits_dev");
    } else {
      $con = conectar();
      $res = pg_query($con, "select id, mensaje, from_id, to_char(fecha, '\"<b>\"dd-mm-yyyy\"</b>
                                                 <br/><b>Hora:</b> \"HH24:MI') as fecha_formateada
                                    from tuits 
                                    where upper(mensaje) like upper('%$hashtag%')
                                    order by fecha desc
                                   limit $ntuits_dev");
    }
    pg_close();

    return $res;
  }

  function buscar_hashtags() {
    $con = conectar();
    $res = pg_query($con, "select unnest(string_to_array(mensaje, ' ')) as x, id from tuits;");
    $car_eli = array(".", ",", ";", ":", "-", "?", "¿", "!", "¡", "=", "#"); 

    for ($i=0; $i < pg_affected_rows($res); $i++) { // EXTRAIGO LOS HASHTAGS DE LOS MENSAJES
        $fila = pg_fetch_assoc($res, $i);
        extract($fila);
        if (substr($x, 0, 1) == '#') {
          $x = str_replace($car_eli, "", $x); // LIMPIO LOS HASHTAGS DE CARACTERES INSERVIBLES INCLUIDO LA #
          $x = strtoupper($x);                // CONVIERTO TODOOS LOS HASHTAGS A MAYÚSCULAS
          if (isset($hash_array["$x"])) {
            $hash_array["$x"] += 1;
          } else {
            $hash_array["$x"] = 1;
          }
        }
    }
    return $hash_array;
  }

  function insertar_tuit($mensaje, $logged_id, $usuario_id) {
    $con = conectar();
    if ($logged_id == $usuario_id) {
      $res = pg_query($con, "insert into tuits (usuario_id, mensaje)
                             values ($usuario_id, '$mensaje')");
    } else {
      $res = pg_query($con, "insert into tuits (usuario_id, from_id, mensaje)
                             values ($logged_id, $usuario_id, '$mensaje')");
    }

    if (pg_affected_rows($res) > 0) {
      $GLOBALS['mensajes_estado']['mensaje'] = "El tuit se ha creado de forma correcta";
      $GLOBALS['mensajes_estado']['tipo'] = "ok";
    } else {
      $GLOBALS['mensajes_estado']['mensaje'] = "Se ha producido un error al crear el tuit";
      $GLOBALS['mensajes_estado']['tipo'] = "err";
    }
    pg_close();
  }

  function borrar_tuit($tuit_id) {

      if (comprobar_tuit($tuit_id)) {
        $con = conectar();
        $res = pg_query($con, "delete from tuits where id::text = '$tuit_id'");
        if (pg_affected_rows($res) > 0) {
          $GLOBALS['mensajes_estado']['mensaje'] = "El tuit se ha borrado de forma correcta";
          $GLOBALS['mensajes_estado']['tipo'] = "ok";
        } else {
          $GLOBALS['mensajes_estado']['mensaje'] = "Se ha producido un error al borrar el tuit";
          $GLOBALS['mensajes_estado']['tipo'] = "err";
        }
      } else {
          $GLOBALS['mensajes_estado']['mensaje'] = "No existe ningún tuit con ese ID.";
          $GLOBALS['mensajes_estado']['tipo'] = "err";
      }
    pg_close();
  }

  function modificar_tuit($tuit_id, $mensaje) {

      if (comprobar_tuit($tuit_id)) {
        $con = conectar();
        $res = pg_query($con, "update tuits set mensaje='$mensaje', fecha = current_timestamp 
                               where id::text = '$tuit_id'");
      } else {
          $GLOBALS['mensajes_estado']['mensaje'] = "No existe ningún tuit con ese ID.";
          $GLOBALS['mensajes_estado']['tipo'] = "err";
      }
      pg_close();
  }

  function comprobar_tuit($tuit_id) { // comprueba si un tuit existe por su identificador

    $con = conectar();
    $res = pg_query($con, "select * from tuits where id::text = '$tuit_id'");
    pg_close();

    if (pg_affected_rows($res) == 1) {
      return true;
    } else {
      return false;
    }
  }


  // GENERAL

  function hay_mensajes() {
    if (empty($GLOBALS['mensajes_estado']['mensaje'])) {
      echo 'hidden';
    }
  }

  // PAGINACIÓN

  function mostrar_paginacion($pag, $npag, $nenlaces, $usuario_id) {
    // $pag = página actual
    // $npag = número de páginas totales
    // $nenlaces = número de enlaces a cada lado de la página actual
    $inicio = $pag - $nenlaces;
    $fin = $pag + $nenlaces;
    if ($pag > 1) {?>
      <form class="pag" action="index.php" method = "POST">
        <input type="hidden" name="pag" value="<?= $pag-1 ?>">
        <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
        <input class="pg_anterior" type="submit" value="Ant." title="Pág. Anterior">
      </form><?php
    }      
    if ($inicio <1) {
      $inicio = 1;
      $fin = $inicio+($nenlaces*2);
    }
    if ($fin > $npag) {
      $fin = $npag;
      $inicio = $fin-($nenlaces*2);
      $inicio = ($inicio<1) ? $inicio = 1 : $inicio;
    }
    for ($i=$inicio; $i <= $fin; $i++) {?>
        <form class="pag" action="index.php" method = "POST">
          <input type="hidden" name="pag" value="<?= $i ?>">
          <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>"><?php
          if ($i == $pag) {
            $but_class= "pg_actual";
          } else {
            $but_class= "pg";
          }?>
          <input class="<?= $but_class ?>" type="submit" value="<?= $i ?>" title="Pág. <?= $i ?>">
        </form><?php
    }
    if ($pag < $npag) {?>
      <form class="pag" action="index.php" method = "POST">
        <input type="hidden" name="pag" value="<?= $pag+1 ?>">
        <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
        <input class="pg_siguiente" type="submit" value="Sig." title="Pág. Siguiente">
      </form><?php
    }      
  }