<?php
require_once "../vendor/autoload.php";
require_once "/app/routes/routes.php";
var_dump(file_exists('../src/database/pdo.php'));
var_dump(class_exists('App\database\Database'));

use App\controllers\userController;

$obj = new userController();
$obj->Use();

try {
   $url = parse_url($_SERVER['REQUEST_URI'])['path'];
   var_dump($url);
   $request = $_SERVER['REQUEST_METHOD'];
   if(!isset($router[$request])){
      throw new Exception("A routa não existe");
   }
   if(!array_key_exists($url, $router[$request])){
      throw new Exception("A routa não existe");
   }
   $controller = $router[$request][$url];
   $controller();
} catch (Exception $e) {
   $e->getMessage();
 }
/* switch($method){
   case 'GET':
      echo json_encode(['sucess'=> 'esta funcionando']);
      break;
   case 'POST':
      $name = $input['user_name'];
      $userController = new userController();
      $userController->createUser($name);
      break;
} */



?>