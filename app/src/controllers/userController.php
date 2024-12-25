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
    public function getUsers(){
        $userModel = new UserModel();
        $userModel->getUsers();

    }
    public function getUserByNameInformation(string $name){
        $userModel = new UserModel();
        return $userModel->getUserByNameInformation($name);
    }
}
?>