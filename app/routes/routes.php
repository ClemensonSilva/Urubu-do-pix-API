<?php
namespace app\routes;
use Exception;

function load(string $controller, string $action){
    try{
        $controllerFile = "App\\controllers\\{$controller}";
        if(!class_exists($controllerFile)){
            throw new Exception("O controller: {$controller} não existe.");
        }
        $controllerFile = new $controllerFile();

        if(!method_exists($controllerFile, $action)){
            throw new Exception("O método: {$action} não existe.");
        };
        $controllerFile->$action(json_decode(file_get_contents('php://input')), true); // importante ver com detalhes a documentacao

    }catch(Exception $e){
        echo $e->getMessage();
    }

};
$router = 
[
    'GET' =>['/user'=> fn()=> load('userController', 'getUsers')],
    'POST' =>[
        '/transaction'=> fn()=> load('TransactionController', 'createTransaction'),
        '/deposit'=> fn()=> load('userController', 'deposit'),
        '/create/user'=> fn()=>load('userController', 'createUsers')],
];
?>