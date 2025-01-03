<?php
namespace App\Models;

use App\Controllers\TransactionController;
use App\Controllers\UserController;
use App\Database\Databases;

require_once "../src/Database/Pdo.php"; // isso é temporario ate ajustar os namespaces completamente
use PDOException;
use PDO;
use stdClass;

class UserModel
{
    public function createUsers(stdClass $userParams)
    {
        $pdo = new Databases();
        $pdo = $pdo->getConnection();
        try {
            if ($userParams) {
                $sql =
                    "INSERT INTO users(user_name, user_balance) VALUES(:user_name, :user_balance)";
                Databases::operationsInDB($pdo, $sql, [
                    ":user_name" => $userParams->user_name,
                    ":user_balance" => $userParams->user_balance,
                ]);
                echo json_encode([
                    "sucess" => true,
                    "message" => "User created corretly",
                ]);
            } else {
                echo json_encode([
                    "error" => true,
                    "message" => "The user name is mandatory",
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    public function getUsers()
    {
        try {
            $pdo = new Databases();
            $pdo = $pdo->getConnection();
            $sql = "SELECT * FROM users";
            $result = Databases::consultingDB($pdo, $sql);
            if (!empty($result)) {
                header("Content-Type: application/json");
                echo json_encode(["data" => $result]);
            } else {
                echo json_encode([
                    "error" => true,
                    "message" => "Results not found",
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    public function getUserInvestiments(stdClass $userParams)
    {
        $pdo = new Databases();
        $stdObjbect = new stdClass();
        $pdo = $pdo->getConnection();
        // user_id
        if (empty($userParams->user_id)) {
            echo json_encode([
                "error" => true,
                "message" => "This user doesn´t exists",
            ]);
            exit();
        }

        $user_id = $userParams->user_id;

        // consultar DB e verificar as transactions que existem para esse usuario
        $sql = "SELECT * FROM transactions WHERE userId=:user_id";
        $results = Databases::consultingDB($pdo, $sql, [
            ":user_id" => $user_id,
        ]);
        $arrayOfResults = [];
        foreach ($results as $result) {
            $stdObjbect->user_id = $user_id;
            $stdObjbect->transaction_id = $result->id;
            $result = TransactionModel::profitInvestiment($stdObjbect);
            $name = $result["user"]->user_name;
            unset($result["user"]);
            array_push($arrayOfResults, $result);
        }
        array_unshift($arrayOfResults, $name);
        return $arrayOfResults;
    }

    public function deposit(stdClass $userParams)
    {
        // user_id e deposit futuramente usar user_acount
        try {
            $pdo = new Databases();
            $pdo = $pdo->getConnection();

            $userInfo = UserController::getUserInformation(
                $userParams->user_id
            );
            $userName = $userInfo->user_name;
            if (empty($userName)) {
                echo json_encode([
                    "error" => true,
                    "message" => "Results not found",
                ]);
                exit();
            }

            $userBalance = $userInfo->user_balance; // no futuro, sera preciso fazer correcao e adicionar um campo email, que seja como dado unico no DB
            $newBalance = $userBalance + $userParams->deposit;

            $sql =
                "UPDATE users set user_balance =:newBalance where user_name=:userName";
            Databases::operationsInDB($pdo, $sql, [
                ":userName" => $userName,
                ":newBalance" => $newBalance,
            ]);

            echo json_encode([
                "sucess" => true,
                "message" => "Deposit made successfully",
            ]);
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    public function getUserInformation(string|int $userParams)
    {
        $pdo = new Databases();
        $pdo = $pdo->getConnection();
        try {
            $sql =
                "SELECT * FROM users where user_name= :userName or  id= :userId ";
            $result = Databases::consultingDB($pdo, $sql, [
                ":userName" => $userParams,
                ":userId" => $userParams,
            ]);
            // recebe os resultados da tabela e os transforma em um array associativo
            if (!empty($result)) {
                return $result[0]; // a funcao consultingDB retorna um array, mas quero coletar o primeiro e unico objeto contido neste array
            } else {
                return json_encode("Results not found");
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}
?>
