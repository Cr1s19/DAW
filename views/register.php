<?php include('_header.php'); ?>  

<?php if (!$registration->registration_successful && !$registration->verification_successful) { ?>
<div class="container">
  <div id="register">
    <h2 class="register-header">Registro</h2>
    <?php
    // show potential errors / feedback (from login object)
    if (isset($registration)) {
      if ($registration->errors) {
        ?>
        <ul class="errors">
          <?php
          foreach ($registration->errors as $error) {
            ?>
            <li><?php echo $error ?></li>
            <?php
          }
          ?>
        </ul>
        <?php
      }
      if ($registration->messages) {
        ?>
        <ul class="success">
          <?php
          foreach ($registration->messages as $message) {
            ?>
            <li><?php echo $message ?></li>
            <?php
          }
          ?>
        </ul>
        <?php
        
      }
    }
    ?>
    <form method="post" action="register.php" name="registerform">
      <p>
        <input id="user_name" type="text" value="Nombre" pattern="[a-zA-Z]{2,64}" name="user_name" onBlur="if(this.value == '') this.value = 'Nombre'" onFocus="if(this.value == 'Nombre') this.value = ''" required />
      </p>

      <p>
        <input id="user_email" type="email" value="Correo electrónico" name="user_email" onBlur="if(this.value == '') this.value = 'Correo electrónico'" onFocus="if(this.value == 'Correo electrónico') this.value = ''" required />
      </p>

      <p>
        <input id="user_password_new" value="Password" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" onBlur="if(this.value == '') this.value = 'Contraseña'" onFocus="if(this.value == 'Contraseña') this.value = ''" />
      </p>

      <input type="submit" name="register" value="Registrarse" />
    </form>
  </div>
</div>
<?php } else {
    // show potential errors / feedback (from login object)
    if (isset($registration)) {
      if ($registration->errors) {
        ?>
        <ul class="errors">
          <?php
          foreach ($registration->errors as $error) {
            ?>
            <li><?php echo $error ?></li>
            <?php
          }
          ?>
        </ul>
        <?php
      }
      if ($registration->messages) {
        ?>
        <ul class="success">
          <?php
          foreach ($registration->messages as $message) {
            ?>
            <li><?php echo $message ?></li>
            <?php
          }
          ?>
        </ul>
        <?php
        
      }
    }
  }
    ?>