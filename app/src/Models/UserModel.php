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
                return Databases::genericMessage(
                    "sucess",
                    "User created corretly"
                );
            } else {
                return Databases::genericMessage(
                    "error",
                    "The user name is mandatory"
                );
            }
        } catch (PDOException $e) {
            return Databases::genericMessage("error", $e->getMessage());
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
                return ["data" => $result];
            } else {
                return Databases::resultsNotFound("Results");
            }
        } catch (PDOException $e) {
            return Databases::genericMessage("error", $e->getMessage());
        }
    }

    public function getUserInvestiments(stdClass $userParams)
    {
        $pdo = new Databases();
        $stdObjbect = new stdClass();
        $pdo = $pdo->getConnection();

        $user_id = $userParams->user_id;

        // consultar DB e verificar as transactions que existem para esse usuario
        $sql = "SELECT * FROM transactions WHERE userId=:user_id";
        $results = Databases::consultingDB($pdo, $sql, [
            ":user_id" => $user_id,
        ]);
        if (empty($results)) {
            return Databases::resultsNotFound("User Transactions");
        }
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
                return Databases::resultsNotFound("User");
            }

            $userBalance = $userInfo->user_balance; // no futuro, sera preciso fazer correcao e adicionar um campo email, que seja como dado unico no DB
            $newBalance = $userBalance + $userParams->deposit;

            $sql =
                "UPDATE users set user_balance =:newBalance where user_name=:userName";
            Databases::operationsInDB($pdo, $sql, [
                ":userName" => $userName,
                ":newBalance" => $newBalance,
            ]);

            return Databases::genericMessage(
                "sucess",
                "Deposit made successfully"
            );
        } catch (PDOException $e) {
            return Databases::genericMessage("error", $e->getMessage());
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
                return Databases::resultsNotFound("User");
            }
        } catch (PDOException $e) {
            return Databases::genericMessage("error", $e->getMessage());
        }
    }
}
?>
