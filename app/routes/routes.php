<?php
namespace app\routes;
use Exception;

function load(string $controller, string $action){
    try{
        //ERRO 
        $controllerFile = "app\\controllers\\{$controller}";
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
    'POST' =>[ '/user' => fn() => load('userController', 'createUser') ],
    'GET' =>['/user'=> fn()=> load('userController', 'getUsers')]
]
    
?>