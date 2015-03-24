<?php
require_once('config/config.php');

require_once('classes/Login.php');

// crea un nuevo objeto de login, se puede ver en el archivo para más información
$login = new Login();

$db_connection = null;
$errors = array();

if (isset($_POST['type'])) {
  switch ($_POST['type']) {
    case 'album':
    $album_name = mysql_real_escape_string($_POST['name']);
    $user_id = mysql_real_escape_string($_POST['user_id']);
    if(isset($album_name)){
      $result = $login->newAlbum($album_name, $user_id);
      $res = array('result' => $result);
      echo json_encode($res);
    } else {
      $res = array('result' => false);
      echo json_encode($res);
    }
    break;
    case 'image':
    $arr_rand = generarArreglo();
    $joinedArrays = array($arr_rand, mediana($arr_rand));
    echo json_encode($joinedArrays);
    break;
  }
}
function databaseConnection()
{
  if ($db_connection != null) {
    return true;
  } else {
    try {
      $db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
      return true;
    } catch (PDOException $e) {
      $errors[] = MESSAGE_DATABASE_ERROR . $e->getMessage();
    }
  }
  return false;
}

?>