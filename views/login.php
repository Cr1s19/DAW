<?php include('_header.php'); ?>

<div class="container">
  <div id="login">
    <h2 class="login-header">Iniciar sesión</h2>
    <?php
    // show potential errors / feedback (from login object)
    if (isset($login)) {
        if ($login->errors) {
            ?>
            <ul class="errors">
            <?php
            foreach ($login->errors as $error) {
                ?>
                <li><?php echo $error ?></li>
                <?php
            }
            ?>
            </ul>
            <?php
        }
    }
    ?>
    <form method="post" action="index.php" method="post" name="loginform"> 
      <fieldset class="clearfix">
        <p><span class="fontawesome-user"></span><input type="text" name="user_name" value="Correo" onBlur="if(this.value == '') this.value = 'Correo'" onFocus="if(this.value == 'Correo') this.value = ''" required></p> 
        <p><span class="fontawesome-lock"></span><input type="password" name="user_password" autocomplete="off" value="Contraseña" onBlur="if(this.value == '') this.value = 'Contraseña'" onFocus="if(this.value == 'Contraseña') this.value = ''" required></p> 
        <p><input type="checkbox" id="user_rememberme" name="user_rememberme" value="1" />
        <label for="user_rememberme">Recordarme</label></p><br>
        <p><input type="submit" value="Iniciar sesión" name="login"></p>
      </fieldset>
    </form>
    <p>¿No tienes una cuenta? <a href="register.php">Regístrate</a><span class="fontawesome-arrow-right"></span></p>
  </div>
</div>
</body>
</html>