<?php
require_once "../vendor/autoload.php";
require_once "/app/routes/routes.php";
/*
var_dump(file_exists('../src/database/pdo.php'));
var_dump(method_exists('App\controllers\userController', 'deposit'));
*/

use App\models\TransactionModel;
use App\controllers\userController;
/* 
$obj = new TransactionModel();
$obj2 = new userController();
$obj = new stdClass();
$obj->user_id=2;
$obj->deposit= 200;

$b = $obj2->deposit($obj);
var_dump($obj);
*/

try {
   $url = parse_url($_SERVER['REQUEST_URI'])['path'];
   $request = $_SERVER[ 'REQUEST_METHOD'];
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




?>