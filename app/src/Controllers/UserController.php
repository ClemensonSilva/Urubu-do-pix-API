<?php
namespace App\Controllers;

use App\Database\Databases;
use App\Models\UserModel;
use stdClass;
// lembrar de trazer as validacaoes para o controller
require_once "../src/Database/Pdo.php";
class UserController
{
    public function createUsers($input)
    {
        $userModel = new UserModel();
        if (empty($input->user_name) || empty($input->user_balance)) {
            echo json_encode(
                Databases::genericMessage("error", "The user data is mandatory")
            );
            return 0;
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
        if (empty($userParams->user_id)) {
            echo json_encode(
                Databases::genericMessage(
                    "error",
                    "You have to insert a user id."
                )
            );
            return 0;
        }
        $userModel = new UserModel();
        echo json_encode($userModel->getUserInvestiments($userParams));
    }
    public static function getUserInformation(
        int|string $userParams
    ): stdClass|string {
        if (empty($userParams)) {
            echo json_encode(
                Databases::genericMessage(
                    "error",
                    "User name or user id is mandatory"
                )
            );
            return 0;
        }
        $userModel = new UserModel();
        return $userModel->getUserInformation($userParams);
    }
    public function deposit($depositParams)
    {
        if (empty($userParams->user_id) || empty($userParams->deposit)) {
            echo json_encode(
                Databases::genericMessage(
                    "error",
                    "User id or depositvalue is mandatory"
                )
            );
            return 0;
        }
        $userModel = new UserModel();
        echo json_encode($userModel->deposit($depositParams));
    }
}
?>
