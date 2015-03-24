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
  <div id="albums-images">
    <?php $result = $login->getFolders();  if(count($result) > 0) {?>
      <ul id="albums-images-list">
      <?php for($i = 0; $i < count($result); $i++) {?>
        <li>
          <a href="/albums.php?<?php echo $result[$i]['album_id'] ?>">
            <span class="icon-album fontawesome-folder-close-alt"></span><br> <?php echo $result[$i]['name'] ?>
          </a>
        </li>
      <?php } ?>
      </ul>
    <?php } ?> 
  </div>
  <div class="modal">
    
    <header>
      <h2>Crear álbum</h2>
      <button class="close toggleModal">Cerrar</button>
    </header>
    
    <section>
      <p><input type="text" id="album-name" placeholder="Nombre" required></p>
    </section>
    
    <button class="button green pull-right" id="create-album">Crear</button>
    
  </div>
  <input type="hidden" id="user" value="<?php echo $_SESSION['user_id'] ?>">
<?php include('_footer.php'); ?>