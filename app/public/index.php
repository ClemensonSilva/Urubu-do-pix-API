<?php
require_once "../vendor/autoload.php";
require_once "/app/routes/routes.php";
/*
var_dump(file_exists('../src/database/pdo.php'));
*/
var_dump(class_exists('App\controllers\TransactionController'));

use App\models\TransactionModel;

$obj = new TransactionModel();
$a = $obj->setDate();
echo TransactionModel::setDate();
var_dump(json_decode($a));


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