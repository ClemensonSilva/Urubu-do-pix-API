<?php
namespace App\Models;

use App\Controllers\UserController;
use App\Database\Databases;
use PDO;
use DateTime;

require_once "../src/Database/Pdo.php"; // isso é temporario ate ajustar os namespaces completamente
use PDOException;
use stdClass;

// CRIAR REGRAS DE NEGOCIO MAIS ESPECIFICAS
class TransactionModel
{
    public function createTransaction(stdClass $transactionParams)
    {
        // user_id e depositValue

        // futuramente add codigo para coletar o id do usuario usando nome e email
        try {
            $pdo = new Databases();
            $pdo = $pdo->getConnection();

            (int) ($user_id = $transactionParams->user_id);
            (float) ($depositValue = $transactionParams->depositValue);

            if (empty($transactionParams->depositDate)) {
                $depositDate = $transactionParams->depositDate = TransactionModel::setDate();
            } else {
                $depositDate = $transactionParams->depositDate;
            }

            if (is_string(UserController::getUserInformation($user_id))) {
                return Databases::resultsNotFound("User");
            }
            $user_balance = UserController::getUserInformation($user_id)
                ->user_balance;

            if (TransactionModel::userhasBalance($user_id, $depositValue)) {
                $pdo->beginTransaction();
                TransactionModel::addDepositInvestiment(
                    $pdo,
                    $user_id,
                    $depositDate,
                    $depositValue
                );

                (float) ($newBalance = $user_balance - $depositValue);

                TransactionModel::updateUserBalance(
                    $pdo,
                    $newBalance,
                    $user_id
                );
                $pdo->commit();
                return Databases::genericMessage(
                    "sucess",
                    "Transaction made sucessfuly"
                );
            } else {
                $pdo->rollBack();
                return Databases::genericMessage(
                    "error",
                    "Insuficient Balance"
                );
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            return Databases::genericMessage("error", $e->getMessage());
        }
    }

    public static function profitInvestiment(stdClass $transactionParams)
    {
        // user_id, transaction_id,
        $transaction_id = $transactionParams->transaction_id;
        $transactionInfo = TransactionModel::getTransactionInfo(
            $transaction_id
        ); // retorna uma string se nada for encontrado com msg de erro
        $user_id = $transactionParams->user_id;
        $userInformation = UserController::getUserInformation($user_id);
        unset($userInformation->user_balance);

        if (is_string($transactionInfo)) {
            return $transactionInfo;
            exit();
        }
        $depositDate = $transactionInfo->depositDate;
        $depositValue = $transactionInfo->depositValue;

        $depositDate = new DateTime($depositDate);
        $now = new DateTime();
        $interval = date_diff($depositDate, $now);
        $days = round($interval->days / 30, 1);

        $interest = 0.33;

        $profit = round(
            $depositValue * pow(1 + $interest, $days) - $depositValue,
            5
        ); // filtro para impedir que valor chegue ao infinto no PHP
        return [
            "transaction_id" => $transaction_id,
            "user" => $userInformation,
            "profit" => $profit,
            "depositValue" => $depositValue,
            "interest" => $interest . " per months",
            "depositDate" => $depositDate,
        ];
    }

    public static function addDepositInvestiment(
        PDO $pdo,
        int $user_id,
        $depositDate,
        float $depositValue
    ): void {
        $sql = "INSERT into transactions(userId, depositValue, depositDate)
                           VALUES(:userId, :depositValue, :depositDate)";
        Databases::operationsInDB($pdo, $sql, [
            ":userId" => $user_id,
            ":depositValue" => $depositValue,
            ":depositDate" => $depositDate,
        ]);
    }
    public static function updateUserBalance(
        PDO $pdo,
        float $newBalance,
        int $user_id
    ): void {
        $sql = "UPDATE users set user_balance = :newBalance where id= :user_id";
        $stmt = $pdo->prepare($sql);
        Databases::operationsInDB($pdo, $sql, [
            ":newBalance" => $newBalance,
            ":user_id" => $user_id,
        ]);
    }

    public static function userhasBalance(int $user_id, float $depositValue)
    {
        $balance = userController::getUserInformation($user_id)->user_balance;
        return $balance > $depositValue ? true : false;
    }
    public static function setDate()
    {
        return date("y-m-d");
    }

    public static function getTransactionInfo(
        int $transaction_id
    ): stdClass|string {
        $pdo = new Databases();
        $pdo = $pdo->getConnection();
        $sql = "SELECT * FROM transactions where id=:transaction_id";
        $result = Databases::consultingDB($pdo, $sql, [
            ":transaction_id" => $transaction_id,
        ]);
        if (!$result) {
            return Databases::resultsNotFound("Transactions ");
        }
        return $result[0]; // o fetchAll da f consultingDB retorna um array e eu quero capturar apenas o primeiro
    }
}
