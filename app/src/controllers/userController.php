<?php
namespace App\controllers;

use App\models\UserModel;

class userController {
    public function createUser($input){
        $userModel = new UserModel();
        if(empty($input)){
            echo json_encode(['error'=> 'The user name is mandatory']);
        }
        else{
            $userModel->createUser($input);
        }
    }
    public function Use(){
        echo json_encode(['sucess'=> 'esta funcionando']);
    }
}
?>