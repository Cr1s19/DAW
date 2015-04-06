<?php include('_header.php'); ?>

  <nav id="main-nav">
    <ul>
      <li>
        <a href="index.php?logout" class="logout">Cerrar sesión</a>
      </li>
    </ul>
  </nav>
  <h1 class="user-name">Bienvenido  <?php echo $login->getUsername() ?></h1>
  <div class="create">
    <button class="button blue toggleModal">Crear álbum</button>
    <button class="button green toggleModal2">Subir imagen</button>
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
    <?php $result = $login->getImages();  if(count($result) > 0) {?>
      <ul id="albums-images-list">
      <?php for($i = 0; $i < count($result); $i++) {?>
        <li>
          <a href="<?php echo $result[$i]['URL'] ?>">
            <img src="<?php echo $result[$i]['URL'] ?>">
          </a>
        </li>
      <?php } ?>
      </ul>
    <?php } ?>
  </div>
  <div class="modal" id="modal2">
    
    <header>
      <h2>Subir imágen</h2>
      <button class="close toggleModal2">Cerrar</button>
    </header>
    
    <form action="index.php" enctype="multipart/form-data" id="form" method="post" name="form">
      <div id="upload">
      <input id="file" name="file" type="file">
      </div>
      <input type="hidden" name="uploadImage" value="true">
      <button type="submit" class="button green pull-right" id="upload-pic">Subir</button>
    </form>
    
  </div>

  <div class="modal" id="modal">
    
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