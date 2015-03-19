<?php include('_header.php'); ?>

  <nav id="main-nav">
    <ul>
      <li>
        <a href="index.php?logout" class="logout">Cerrar sesión</a>
      </li>
      <li>
        <a href="edit.php">Editar cuenta</a>
      </li>
    </ul>
  </nav>
  <h1 class="user-name">Bienvenido  <?php echo $login->getUsername() ?></h1>
  <div class="create">
    <a href="#" class="button blue">Crear álbum</a>
    <a href="#" class="button green">Subir imagen</a>
  </div>
</body>
</html>