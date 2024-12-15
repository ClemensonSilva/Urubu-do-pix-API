<?php
    function load(string $controller, string $action){
        try{
            $controllerFile = ("/app/models/{$controller}.php");
            if(!class_exists($action)){
                throw new Exception("O controller: {$controller} não existe.");
            }

        }catch(Exception $e){

        }
    };

    $router = 
    [
        'POST' =>[ '/user' => load('userController', 'createUser', 'userModel') ]
    ]
    
?>