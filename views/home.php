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
    <button class="button blue toggleModal">Crear álbum</button>
    <button class="button green">Subir imagen</button>
  </div>
  <div class="modal">
    
    <header>
      <h2>Crear álbum</h2>
      <button class="close toggleModal">Cerrar</button>
    </header>
    
    <section>
      <p><input type="text" id="album-name" placeholder="Nombre"></p>
    </section>
    
    <button class="button green pull-right">Crear</button>
    
  </div>
<?php include('_footer.php'); ?>