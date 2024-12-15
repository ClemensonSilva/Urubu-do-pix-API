<?php
require_once "/app/models/userModel.php";
class userController {
    public function createUser($input){
        $userModel = new UserModel();
        if($input){
            $userModel->createUser($input);
        }
        else{
            echo json_encode(['error'=> 'The user name is mandatory']);
        }
    }
}
?>