<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html>

<link rel="stylesheet" type="text/css" href="style/style.css" />
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>Proyecto 2 DAW </title>
</head>
<body>

  <?php
                    // define variables and set to empty values
  $nameErr = $emailErr =  "";
  $name = $email =  "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
   if (empty($_POST["name"])) {
    $_SESSION["nombre"] = "name";
    $nameErr = "Es necesario poner un nombre";
  } else {
    $name = test_input($_POST["name"]);
    if (empty($_POST["email"])) {
      $_SESSION["corre"] = "email";
      $emailErr = "Campo requerido";
    }    else {
      $email = test_input($_POST["email"]); 
      header('Location: Inicio.php');    
    }
  }


}

function test_input($data) {
 $data = trim($data);
 $data = stripslashes($data);
 $data = htmlspecialchars($data);
 return $data;
}
?>

<div class="container">
  <div id="login">
  <h2 class="login-header">Iniciar sesión</h2>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"> 
      <fieldset class="clearfix">

        <p><span class="fontawesome-user"></span><input type="text" value="Correo" onBlur="if(this.value == '') this.value = 'Correo'" onFocus="if(this.value == 'Correo') this.value = ''" required></p> 
        <p><span class="fontawesome-lock"></span><input type="password"  value="Contraseña" onBlur="if(this.value == '') this.value = 'Contraseña'" onFocus="if(this.value == 'Contraseña') this.value = ''" required></p> 
        <p><input type="submit" value="Iniciar sesión" name="submit"></p>

      </fieldset>
    </form>
    <p>¿No tienes una cuenta? <a href="#">Regístrate</a><span class="fontawesome-arrow-right"></span></p>
  </div>
</div>
</body>
</html>