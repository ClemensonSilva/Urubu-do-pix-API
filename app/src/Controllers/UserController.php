<?php
namespace App\Controllers;

use App\Models\UserModel;
use stdClass;

class UserController
{
    public function createUsers($input)
    {
        $userModel = new UserModel();
        if (empty($input)) {
            echo json_encode(["error" => "The user name is mandatory"]);
        } else {
            $userModel->createUsers($input);
        }
    }
    public function getUsers()
    {
        $userModel = new UserModel();
        $userModel->getUsers();
    }
    public static function getUserInformation(
        int|string $userParams
    ): stdClass|string {
        $userModel = new UserModel();
        return $userModel->getUserInformation($userParams);
    }
    public function deposit($depositParams)
    {
        $userModel = new UserModel();
        $userModel->deposit($depositParams);
    }
}
?>
