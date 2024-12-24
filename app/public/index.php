<?php
var_dump(file_exists('../vendor/autoload.php'));
require "../vendor/autoload.php";
require "/app/routes/routes.php";

use app\public\FunctionHelper\FunctionHelper;
use app\controllers\userController;

var_dump(FunctionHelper::getUri('path'));
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'),true);

try {
   $url = FunctionHelper::getUri('path');
   $request = FunctionHelper::getMethod();
   if(!isset($router[$request])){
      throw new Exception("A routa não existe");
   }
   if(!array_key_exists($url, $router[$request])){
      throw new Exception("A routa não existe");
   }
   $controller = $router[$request][$uri];
   $controller();
} catch (Exception $e) {
   $e->getMessage();
 }
switch($method){
   case 'GET':
      echo json_encode(['sucess'=> 'esta funcionando']);
      break;
   case 'POST':
      $name = $input['user_name'];
      $userController = new userController();
      $userController->createUser($name);
      break;
}



?>