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

    // COMPROBACIONES DE DATOS RECIBIDOS POR POST

    $usuario_id= (isset($_POST['usuario_id']) ? $_POST['usuario_id'] : '');

    if (isset($_POST['mensaje_ins'])) {
      insertar_tuit($_POST['mensaje_ins'], $usuario_id);
    }

    if (isset($_POST['borrar_msj'], $_POST['tuit_id'])) {
      borrar_tuit($_POST['tuit_id']);
    }?>
    <div id="principal">
      <header>
      </header>
      <aside>
        <form action="index.php" method="POST">
          Usuario:
          <input  type="number" width="15" name="usuario_id"
                  min="<?= usuario_min() ?>" max="<?= usuario_max() ?>" value="<?= $usuario_id ?>"
                  placeholder="Valor entre <?= usuario_min() ?> y <?= usuario_max() ?>"><br/>
          <input type="submit" name="enviar" title="Enviar">
        </form><?php

        if ($usuario_id !='') { // HAY UN USUARIO IDENTIFICADO EN EL SISTEMA?>
          <form action="index.php" method="POST">
            <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
            <textarea class="enviar_msj" name="mensaje_ins" rows="8" cols="22" 
            maxlength="140" placeholder="Introduzca el mensaje (máx. 140 carácteres)"
            required="required"></textarea>
            <input type="submit" value="Enviar">
          </form><?php
        }?>
      </aside>
      <section><?php
        if ($usuario_id !='') {
          $tuits = devolver_tuits($usuario_id); // RECUPERO LOS TUITS DEL USUARIO
          if (pg_affected_rows($tuits) >0) { // SI EL USUARIO TIENE TUITS DIBUJO LA TABLA CON ELLOS?>
            <table>
              <thead>
                <tr>
                  <th colspan="4">TIMELINE DEL USUARIO <?= strtoupper(devolver_nick($usuario_id)) ?></th>
                </tr>
                <tr>
                  <th width="8%">ID.</th>
                  <th width="50%">MENSAJE</th>
                  <th width="24%">FECHA</th>
                  <th width="18%">ACCIONES</th>
                </tr>
              </thead>
              <tbody><?php
                  for ($i = 0; $i < pg_num_rows($tuits); $i++) {
                    $fila = pg_fetch_assoc($tuits, $i);
                    extract($fila);?>
                    <tr>
                      <td><?= $id ?></td>
                      <td class="justificado"><?= $mensaje ?></td>
                      <td><?= $fecha_formateada ?></td>
                      <td>
                        <form style="display:inline" action="index.php" method="POST">
                          <input type="hidden" name="tuit_id" value="<?= $id ?>">
                          <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                          <input type="submit" name="borrar_msj" value="Borrar">
                        </form>
                        <form style="display:inline" action="editar_tuit.php" method="POST" id="edit_form">
                          <input type="hidden" name="tuit_id" value="<?= $id ?>">
                          <input type="hidden" name="usuario_id" value="<?= $usuario_id ?>">
                          <input type="submit" name="editar_msj" value="Editar">
                        </form>
                      </td>
                    </tr><?php
                  }?>
              </tbody>
            </table><?php
          } else {?>
            <h2>EL USUARIO <?= devolver_nick($usuario_id) ?> NO TIENE TUITS</h2><?php
          }
        }?>
      </section>
      <footer>
      </footer>
    </div>
  </body>
</html>
