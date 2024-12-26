<?php
namespace App\controllers;

use App\models\UserModel;
use stdClass;

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
    public function getUserInformation(string $name):stdClass{
        $userModel = new UserModel();
        return $userModel->getUserInformation($name);
    }
    public function deposit($depositParams)  {
        $userModel = new UserModel();
        $userModel->deposit($depositParams);
    }
}
?>
