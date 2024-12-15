<?php
//var_dump(file_exists('/app/routes/Router.php'));

require_once "/app/controllers/userController.php";

$url = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'),true);

var_dump($url);

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