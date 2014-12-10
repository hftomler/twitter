<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="../comunes/tuits.css">
    <title>Insertar un twit</title>
  </head>
  <body><?php
    require '../comunes/auxiliar.php';
    
    // Define la variable global para los mensajes de estado.
    $GLOBALS['mensajes_estado'] = ["mensaje" => "", "tipo" => 'err'];
    $tpp = 5;                          // Tuits por página

     // Recibe el usuario logueado o redirige a login.
 
      if (isset($_SESSION['usuario'])) {
        $logged_id = $_SESSION['usuario'];
      } else {
        $logged_id = comprobar_usuario();
      }

      $usuario_id = (isset($_POST['usuario_id']))? trim($_POST['usuario_id']): $logged_id;
      $nick = devolver_nick($logged_id);
      $mensaje = (isset($_POST['mensaje']))? trim($_POST['mensaje']): '';


    // COMPROBACIONES DE DATOS RECIBIDOS POR POST

    $pag = (isset($_POST['pag'])? trim($_POST['pag']): 1);
    $hashtag = (isset($_GET['hashtag']))? trim($_GET['hashtag']): "";


    if (isset($_POST['insertar_tuit'])) {
      insertar_tuit($_POST['insertar_tuit'], $logged_id, $usuario_id);
      $usuario_id = $logged_id;
    }

    if (isset($_POST['borrar_tuit'], $_POST['tuit_id'])) {
      borrar_tuit($_POST['tuit_id']);
    }

    if (isset($_POST['modificar_tuit'], $_POST['mensaje_mod'])) {
      modificar_tuit($_POST['tuit_id'], $_POST['mensaje_mod']);
    }


    if (isset($_GET['ouser_id']) && $_GET['ouser_id'] !='') {
      $usuario_id = $_GET['ouser_id'];
    }


    // VARIABLES PARA PAGINACIÓN ?>

    <div id="principal"><?php
      include('../comunes/header.php');?>
      <aside>
      <article>
        <h3 class="titulo">Logged User</h3>
        <p><span class="leyenda">Usuario:</span> <?= $nick ?></p>
          <form action="index.php" method="POST">
            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
            <textarea class="enviar_msj" name="insertar_tuit" rows="7" cols="22" 
            maxlength="140" placeholder="Introduzca el mensaje (máx. 140 carácteres)"
            required="required"><?= $mensaje ?></textarea>
            <input type="image" src="../images/enviar_normal.png" value="Enviar" title="Enviar tuit">
          </form>
      </article>
      <article>
        <img src="../images/divider.png" />
        <h3 class="titulo">Top Follow Users</h3><?php
        $lista_usuarios = devolver_lista_usuarios($usuario_id, 'nick', true);
        if (pg_affected_rows($lista_usuarios) >0) {
          for ($i = 0; $i < $tpp; $i++) {
            $fila = pg_fetch_assoc($lista_usuarios, $i);
            extract($fila);?>
            <span class="resaltar">
              @<a href="index.php?ouser_id=<?= $id ?>&ver_user" title="Seguidores de <?= $nick ?>"><?= $nick ?>
            <span class="numero_tuits">(<?= $nfollowers ?>)</span></a></span><br/><?php
          }
        }?>
      </article>
      </aside>
      <section><?php
        if ($usuario_id !='' && (!isset($_POST['editar_msj']))) {
          $ttot = contar_tuits($usuario_id); // Tuits totales del usuario
          $nenlaces = 2;                     // Número de enlaces a cada lado de la página actual
          if ($ttot > 0) {
            $npag = ceil($ttot/$tpp);
        }
          $tuits = devolver_tuits($usuario_id, $tpp, $pag); // RECUPERO LOS TUITS DEL USUARIO
          if (pg_affected_rows($tuits) >0) { // SI EL USUARIO TIENE TUITS DIBUJO LA TABLA CON ELLOS?>
            <table>
              <thead>
                <tr>
                  <th colspan="4">TIMELINE DEL USUARIO <?= strtoupper(devolver_nick($usuario_id)) ?></th>
                </tr>
                <tr>
                  <th width="6%">ID.</th>
                  <th width="50%">MENSAJE</th>
                  <th width="14%">FECHA</th>
                  <th width="10%">ACCIONES</th>
                </tr>
              </thead>
              <tbody><?php
                  for ($i = 0; $i < pg_num_rows($tuits); $i++) {
                    $fila = pg_fetch_assoc($tuits, $i);
                    extract($fila);?>
                    <tr class="mensajes">
                      <td class="id"><?= $id ?></td>
                      <td class="justificado mensaje"><?php 
                        if (!(is_null($from_id))) {?>
                          <span class="resaltar">@<a href="index.php?ouser_id=<?=$from_id ?>&ver_user"><?= devolver_nick($from_id) ?></a> - </span><?php
                        }?><?= $mensaje ?>
                      </td>

                      <td class="fecha"><?= $fecha_formateada ?></td>
                      <td class="accion"><?php 
                        if ($logged_id == $usuario_id) {?>
                          <form style="display:inline" action="index.php" method="POST">
                            <input type="hidden" name="tuit_id" value="<?= $id ?>">
                            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                            <input type="image" src="../images/delete_icon.png" name="borrar_tuit"
                                   value="borrar_tuit" title="Borrar tuit">
                          </form>
                          <form style="display:inline" action="index.php" method="POST">
                            <input type="hidden" name="tuit_id" value="<?= $id ?>">
                            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                            <input type="image" src="../images/edit_icon.png" name="editar_msj"
                                   value="modificar_tuit" title="Editar tuit">
                          </form><?php
                        } else {?>
                          <form style="display:inline" action="index.php" method="POST">
                            <input type="hidden" name="mensaje" value="<?= $mensaje ?>">
                            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                            <input type="image" src="../images/retuit_icon.png" name="retuitear" title="Retuitear">
                          </form><?php
                        }?>
                      </td>
                    </tr><?php
                  }?>
              </tbody>
            </table>
            <div class="cont_paginacion"><?= mostrar_paginacion($pag, $npag, $nenlaces, $usuario_id) ?><?php
          } else {?>
              <h2>EL USUARIO <?= devolver_nick($usuario_id) ?> NO TIENE TUITS</h2><?php
          }
        } else { 
            if (isset($_POST['editar_msj'])) {
              $tuit = devolver_tuit($_POST['tuit_id']);
              if (pg_affected_rows($tuit) >0) {
                $fila = pg_fetch_assoc($tuit, 0);
                extract($fila);?>
                <h2>Editar mensaje con ID Nº <?= $id ?></h2>
                <form action="index.php" method="POST">
                    <input type="hidden" name="tuit_id" value="<?= $id ?>">
                    <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                    <textarea class="editar_msj" name="mensaje_mod" rows="4" 
                    maxlength="140" ><?= $mensaje ?></textarea><br/>
                    <input type="submit" name="modificar_tuit" value="Guardar">
                </form><?php
              }
            }
        }?>
      </section>
      <aside id="bloque_derecho">
        <h3 class="titulo">Top 5 Users</h3><?php
        $lista_usuarios = devolver_lista_usuarios($usuario_id, 'tuits', false);
        if (pg_affected_rows($lista_usuarios) >0) {
          for ($i = 0; $i < $tpp; $i++) {
            $fila = pg_fetch_assoc($lista_usuarios, $i);
            extract($fila);?>
            <span class="resaltar">
              @<a href="index.php?ouser_id=<?= $id ?>&ver_user" title="Usuario(tuits)"><?= $nick ?>
            <span class="numero_tuits">(<?= contar_tuits($id) ?>)</span></a></span><br/><?php
          }
        }?>
        <br/>
        <img src="../images/divider_small.png"/>
        <h3 class="titulo">5 Trending Topics</h3><?php
        $hashtags_array =  buscar_hashtags();
        arsort($hashtags_array);
        $cont = $tpp;
        // for ($i = 0; $i < count($hashtags_array); $i++) {
        foreach ($hashtags_array as $key => $value) {
          if ($cont == 0) {
            break;
          }
          $cont -= 1;?>
          <span class="hashtag_leyenda">
            #<a href="index.php?hashtag=<?= $key ?>&ouser_id=<?= $usuario_id ?>"
               title="Ver tuits hashtag -> <?= $hashtags_array[$key] ?>"><?= $key ?>
               <span class="numero_rep_hashtags">(<?= $hashtags_array[$key] ?>)</span>
           </span></a><br/><?php
        }?><br/>
        <form action="index.php" method="GET">
          <input class="hashtag" type="text" name="hashtag" 
                 value="<?= $hashtag ?>" placeholder="Hashtag" />
          <input type="hidden" name="ouser_id" value="<?= $usuario_id ?>">
        </form>
      </aside>
      <section class="mensaje_error" <?= hay_mensajes() ?>>
        <span class="<?= $GLOBALS['mensajes_estado']['tipo'] ?> "><?= $GLOBALS['mensajes_estado']['mensaje'] ?></span>
      </section>
      <section class="res1"><?php
        $last_tuits = devolver_ntuits($tpp, "");
        if (pg_affected_rows($last_tuits) >0) { // SI EXISTEN TUITS DIBUJO LA TABLA CON ELLOS ?>
          <table>
            <thead>
              <tr>
                <th colspan="3">ÚLTIMOS <?= $tpp ?> tuits globales en JATAS</th>
              </tr>
              <tr>
                <th width="60%">MENSAJE</th>
                <th width="14%">FECHA</th>
                <th width="10%">RETUIT</th>
              </tr>
            </thead>
            <tbody><?php
                for ($i = 0; $i < pg_num_rows($last_tuits); $i++) {
                  $fila = pg_fetch_assoc($last_tuits, $i);
                  extract($fila);?>
                  <tr class="mensajes">
                    <td class="justificado mensaje"><?php 
                      if (!(is_null($from_id))) {?>
                        <span class="resaltar">@<a href="index.php?ouser_id=<?=$from_id ?>&ver_user"><?= devolver_nick($from_id) ?></a> - </span><?php
                      }?><?= $mensaje ?>
                    </td>
                    <td class="fecha"><?= $fecha_formateada ?></td>
                    <td class="accion">
                        <form style="display:inline" action="index.php" method="POST">
                          <input type="hidden" name="mensaje" value="<?= $mensaje ?>">
                          <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                          <input type="image" src="../images/retuit_icon.png" name="retuitear" title="Retuitear">
                        </form>
                    </td>
                  </tr><?php
                }?>
            </tbody>
          </table><?php
        }?>
      </section>
      <section class="res2"><?php
        $last_tuits = devolver_ntuits($tpp, $hashtag);
        if (pg_affected_rows($last_tuits) >0) { // SI EXISTEN TUITS DIBUJO LA TABLA CON ELLOS ?>
          <table>
            <thead>
              <tr>
                <th colspan="3">
                  ÚLTIMOS <?= $tpp ?> tuits con el Hashtag 
                  <span class="resaltar_claro">#<?= $hashtag ?></span>
                </th>
              </tr>
              <tr>
                <th width="60%">MENSAJE</th>
                <th width="14%">FECHA</th>
                <th width="10%">RETUIT</th>
              </tr>
            </thead>
            <tbody><?php
                for ($i = 0; $i < pg_num_rows($last_tuits); $i++) {
                  $fila = pg_fetch_assoc($last_tuits, $i);
                  extract($fila);?>
                  <tr class="mensajes">
                    <td class="justificado mensaje"><?php 
                      if (!(is_null($from_id))) {?>
                        <span class="resaltar">@<a href="index.php?ouser_id=<?=$from_id ?>&ver_user"><?= devolver_nick($from_id) ?></a> - </span><?php
                      }?><?= $mensaje ?>
                    </td>
                    <td class="fecha"><?= $fecha_formateada ?></td>
                    <td class="accion">
                        <form style="display:inline" action="index.php" method="POST">
                          <input type="hidden" name="mensaje" value="<?= $mensaje ?>">
                          <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                          <input type="image" src="../images/retuit_icon.png" name="retuitear" title="Retuitear">
                        </form>
                    </td>
                  </tr><?php
                }?>
            </tbody>
          </table><?php
        }?>
      </section>
      <?php
      include('../comunes/footer.php');?>
    </div>
  </body>
</html>