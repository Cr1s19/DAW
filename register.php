<?php
if (version_compare(PHP_VERSION, '5.5.0', '<')) {

  require_once('libraries/password_compatibility_library.php');
}
// include the config
require_once('config/config.php');


require_once('translations/es.php');

require_once('libraries/PHPMailer.php');

require_once('classes/Registration.php');

$registration = new Registration();
// showing the register view (with the registration form, and messages/errors)
include("views/register.php");