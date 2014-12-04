    <header>
      <div class="login">
        <a style="text-decoration:none" href="/twitter/index.php" title="Volver al inicio">
                          <img class="user img_post" src="../images/home.png" alt="Inicio"/>
        </a><?php
        if (!isset($_SESSION['usuario'])) {?>
          <a style="text-decoration:none" href="../usuarios/login.php" title="Iniciar Sesión">
                        <img class="user  img_post" src="../images/login.png" alt="Iniciar Sesión"/><?php
        } else {
         $user_id = comprobar_usuario();
         $nick = comprobar_nick($user_id);?>
          <a style="text-decoration:none" href="../usuarios/logout.php" title="Cerrar sesión => <?= $nick ?>">
                        <img class="user img_post" src="../images/logout.png" alt="Cerrar sesión => <?= $nick ?>"/><?php          
        }?>     
        </a>
      </div>
    </header>