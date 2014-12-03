<?php

  function conectar()
  {
   return pg_connect("host=localhost user=twitter password=twitter
                       dbname=twitter");
  }

// FUNCIONES RELACIONADAS CON EL USUARIO

  function comprobar_usuario()
  {
    if (!isset($_SESSION['usuario'])) {
      $_SESSION['url'] = $_SERVER["REQUEST_URI"];
      header("Location: /tienda/usuarios/login.php");
    }
    return $_SESSION['usuario'];
  }

  function usuario_max() {
    
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

  function usuario_min() {
    
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

  function devolver_nick($usuario_id)
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

// FUNCIONES RELACIONADAS CON LOS TUITS

  function contar_tuits($usuario) {
    $con = conectar();
    $res = pg_query($con, "select count(*) as ntuits from tuits where usuario_id::text = '$usuario'");  
    if (pg_affected_rows($res) == 1) {
      $fila = pg_fetch_assoc($res);
      $ntuits = $fila['ntuits'];
    }
    pg_close();

    return $ntuits;    
  }

  function devolver_tuits($usuario_id, $tpp, $pag) {
    $con = conectar();
    $res = pg_query($con, "select id, mensaje, to_char(fecha, '\"<b>\"dd-mm-yyyy\"</b>
                                               <br/><b>Hora:</b> \"HH24:MI') as fecha_formateada
                                  from tuits 
                                  where usuario_id::text = '$usuario_id'
                                  order by fecha desc
                                  limit $tpp
                                  offset ($pag-1)*$tpp");
    pg_close();

    return $res;
  }

  function devolver_tuit($id) {
    $con = conectar();
    $res = pg_query($con, "select id, mensaje, to_char(fecha, 'dd-mm-yyyy\" a las \"HH24:MI:SS') as fecha_formateada
                           from tuits where id::text = '$id'");
    pg_close();

    return $res;
  }


  function insertar_tuit($mensaje, $usuario_id) {
    $con = conectar();
    $res = pg_query($con, "insert into tuits (usuario_id, mensaje)
                           values ($usuario_id, '$mensaje')");
    pg_close();
  }

  function borrar_tuit($tuit_id) {

      if (comprobar_tuit($tuit_id)) {
        $con = conectar();
        $res = pg_query($con, "delete from tuits where id::text = '$tuit_id'");
        pg_close();
      } else {
        return "No existe ningún tuit con id $tuit_id";
      }
  }

  function modificar_tuit($tuit_id, $mensaje) {

      if (comprobar_tuit($tuit_id)) {
        $con = conectar();
        $res = pg_query($con, "update tuits set mensaje='$mensaje', fecha = current_timestamp where id::text = '$tuit_id'");
        pg_close();
      } else {
        return "No existe ningún tuit con id $tuit_id";
      }
  }

  function comprobar_tuit($tuit_id) {

    $con = conectar();
    $res = pg_query($con, "select * from tuits where id::text = '$tuit_id'");
    pg_close();

    if (pg_affected_rows($res) == 1) {
      return true;
    } else {
      return false;
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