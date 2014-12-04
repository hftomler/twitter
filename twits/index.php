<?php session_start(); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="tuits.css">
    <title>Insertar un twit</title>
  </head>
  <body><?php
    require '../comunes/auxiliar.php';

     // Recibe el usuario logueado o redirige a login.
 
      if (isset($_SESSION['usuario'])) {
        $logged_id = $_SESSION['usuario'];
      } else {
        $logged_id = comprobar_usuario();
      }

      $usuario_id = (isset($_POST['usuario_id']))? trim($_POST['usuario_id']): $logged_id;
      $nick = comprobar_nick($logged_id);
      $mensaje = (isset($_POST['mensaje']))? trim($_POST['mensaje']): '';

    // COMPROBACIONES DE DATOS RECIBIDOS POR POST

    $pag = (isset($_POST['pag'])? trim($_POST['pag']): 1);

    if (isset($_POST['insertar_tuit'])) {
      insertar_tuit($_POST['insertar_tuit'], $logged_id);
      $usuario_id = $logged_id;
    }

    if (isset($_POST['borrar_tuit'], $_POST['tuit_id'])) {
      borrar_tuit($_POST['tuit_id']);
    }

    if (isset($_POST['modificar_tuit'], $_POST['mensaje_mod'])) {
      modificar_tuit($_POST['tuit_id'], $_POST['mensaje_mod']);
    }

    if (isset($_GET['ver_user'], $_GET['ouser_id']) && $_GET['ouser_id'] !='') {
      $usuario_id = $_GET['ouser_id'];
    }


    // VARIABLES PARA PAGINACIÓN ?>

    <div id="principal"><?php
      include('../comunes/header.php');?>
      <aside>
        <img class="rotulo" src="../images/logged.png" />
        <div class="leyenda">
          Usuario:
        </div>
        <div class="dato">
          <?= $logged_id ?>
        </div>
        <div class="leyenda">
          Nick:
        </div>
        <div class="dato">
          <?= $nick ?>
        </div>
          <form action="index.php" method="POST">
            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
            <textarea class="enviar_msj" name="insertar_tuit" rows="8" cols="22" 
            maxlength="140" placeholder="Introduzca el mensaje (máx. 140 carácteres)"
            required="required"><?= $mensaje ?></textarea>
            <input type="submit" value="Enviar">
          </form>
        <img src="../images/divider.png" />
        <img class="rotulo" src="../images/timeline.png" />
        <form action="index.php" method="GET">
          Usuario
          <input  type="number" width="15" name="ouser_id"
                  min="<?= usuario_min() ?>" max="<?= usuario_max() ?>" 
                  placeholder="Valor entre <?= usuario_min() ?> y <?= usuario_max() ?>"
                  title="Dejar vacío para timeline de <?= comprobar_nick($logged_id) ?>"><br/>
          <input type="submit" name="ver_user" value="Ver Timeline">
        </form>
      </aside>
      <section><?php
        if ($usuario_id !='' && (!isset($_POST['editar_msj']))) {
          $ttot = contar_tuits($usuario_id); // Tuits totales del usuario
          $tpp = 5;                          // Tuits por página
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
                  <th width="8%">ID.</th>
                  <th width="60%">MENSAJE</th>
                  <th width="16%">FECHA</th>
                  <th width="16%">ACCIONES</th>
                </tr>
              </thead>
              <tbody><?php
                  for ($i = 0; $i < pg_num_rows($tuits); $i++) {
                    $fila = pg_fetch_assoc($tuits, $i);
                    extract($fila);?>
                    <tr class="mensajes">
                      <td class="id"><?= $id ?></td>
                      <td class="justificado mensaje"><?= $mensaje ?></td>
                      <td class="fecha"><?= $fecha_formateada ?></td>
                      <td class="accion"><?php 
                        if ($logged_id == $usuario_id) {?>
                          <form style="display:inline" action="index.php" method="POST">
                            <input type="hidden" name="tuit_id" value="<?= $id ?>">
                            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                            <input type="submit" name="borrar_tuit" value="Borrar">
                          </form>
                          <form style="display:inline" action="index.php" method="POST">
                            <input type="hidden" name="tuit_id" value="<?= $id ?>">
                            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                            <input type="submit" name="editar_msj" value="Editar">
                          </form><?php
                        } else {?>
                          <form style="display:inline" action="index.php" method="POST">
                            <input type="hidden" name="mensaje" value="<?= $mensaje ?>">
                            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                            <input type="submit" name="retuitear" value="Retuit">
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
      </section><?php
      include('../comunes/footer.php');?>
    </div>
  </body>
</html>