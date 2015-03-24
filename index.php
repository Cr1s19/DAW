<?php

if (version_compare(PHP_VERSION, '5.5.0', '<')) {
  require_once('libraries/password_compatibility_library.php');
}

// Configuración
require_once('config/config.php');

require_once('translations/es.php');

// Correos
require_once('libraries/PHPMailer.php');

// clase para el Login
require_once('classes/Login.php');

// crea un nuevo objeto de login, se puede ver en el archivo para más información
$login = new Login();
$login->getFolders();

// valida si ya se inició sesión
if ($login->isUserLoggedIn() == true) {
  include("views/home.php");
} else {
  include("views/login.php");
}
