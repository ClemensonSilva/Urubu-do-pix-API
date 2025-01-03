<?php
namespace App\Controllers;

use App\Database\Databases;
use App\Models\UserModel;
use stdClass;
// lembrar de trazer as validacaoes para o controller
class UserController
{
    public function createUsers($input)
    {
        $userModel = new UserModel();
        if (empty($input)) {
            echo json_encode(
                Databases::genericMessage("error", "The user name is mandatory")
            );
        } else {
            echo json_encode($userModel->createUsers($input));
        }
    }
    public function getUsers()
    {
        $userModel = new UserModel();
        echo json_encode($userModel->getUsers());
    }
    public function getUserInvestiments($userParams)
    {
        $userModel = new UserModel();
        echo json_encode($userModel->getUserInvestiments($userParams));
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
        echo json_encode($userModel->deposit($depositParams));
    }
}
?>
