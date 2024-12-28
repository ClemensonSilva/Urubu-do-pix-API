<?php

namespace App\models;

use App\database\Database;
use App\controllers\userController;
use PDO;
use DateTime;

require_once "../src/database/pdo.php"; // isso é temporario ate ajustar os namespaces completamente
use PDOException;
use stdClass;

class TransactionModel
{
    public function createTransaction(stdClass $transactionParams)
    {
        // user_id e depositValue

        // futuramente add codigo para coletar o id do usuario usando nome e email
        try {
            $pdo = new Database();
            $pdo = $pdo->getConnection();
            // variables
            $user_id = $transactionParams->user_id;
            $depositValue = $transactionParams->depositValue;
            $depositDate = $transactionParams->depositDate;
            $user_balance = userController::getUserInformation($user_id)
                ->user_balance;

            if (empty($depositDate)) {
                $depositDate = TransactionModel::setDate();
            }

            if (empty($depositValue) || empty($user_id)) {
                echo json_encode([
                    "error" => "The transaction data is mandatory",
                ]);
            } else {
                if (TransactionModel::userhasBalance($user_id, $depositValue)) {
                    $pdo->beginTransaction();
                    TransactionModel::addDepositInvestiment(
                        $pdo,
                        $user_id,
                        $depositDate,
                        $depositValue
                    );

                    $newBalance = $user_balance - $depositValue;

                    TransactionModel::updateUserBalance(
                        $pdo,
                        $newBalance,
                        $user_id
                    );
                    $pdo->commit();
                    echo json_encode([
                        "sucess" => "Transaction finished corretlly",
                    ]);
                } else {
                    echo json_encode(["error" => "Insuficient Balance"]);
                }
            }
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
    public function profitInvestiment(stdClass $transactionParams)
    {
        // user_id, transaction_id,
        $transaction_id = $transactionParams->transaction_id;
        $transactionInfo = TransactionModel::getTransactionInfo(
            $transaction_id
        );
        $user_id = $transactionParams->user_id;
        $userInformation = userController::getUserInformation($user_id);
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
        $days = $interval->days;

        $interest = 0.33;

        $profit = $depositValue * pow(1 + $interest, $days) - $depositValue;
        echo json_encode([
            "transaction_id" => $transaction_id,
            "user" => $userInformation,
            "profit" => $profit,
            "depositValue" => $depositValue,
            "interest" => $interest,
            "depositDate" => $depositDate,
        ]);
    }

    public static function addDepositInvestiment(
        PDO $pdo,
        int $user_id,
        $depositDate,
        float $depositValue
    ): void {
        $sql = "INSERT into transactions(userId, depositValue,depositDate)
        VALUES(:userId, :depositValue, :depositDate)";
        Database::consultingDB($pdo, $sql, [
            ":depositValue" => $depositValue,
            ":depositDate" => $depositDate,
            ":user_id" => $user_id,
        ]);
    }
    public static function updateUserBalance(
        PDO $pdo,
        float $newBalance,
        int $user_id
    ): void {
        $sql = "UPDATE users set user_balance =:newBalance where id=:user_id";
        Database::consultingDB($pdo, $sql, [
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
        $pdo = new Database();
        $pdo = $pdo->getConnection();
        $sql = "SELECT * FROM transactions where id=:transaction_id";
        $result = Database::consultingDB($pdo, $sql, [
            ":transaction_id" => $transaction_id,
        ]);
        if (!$result) {
            return json_encode([
                "error" => true,
                "message" => "Transaction not found",
            ]);
        }
        return $result[0];
    }
}
