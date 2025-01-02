<?php
namespace App\Models;

use App\Controllers\UserController;
use App\Database\Databases;
use PDO;
use DateTime;

require_once "../src/Database/Pdo.php"; // isso é temporario ate ajustar os namespaces completamente
use PDOException;
use stdClass;

class TransactionModel
{
    public function createTransaction(stdClass $transactionParams)
    {
        // user_id e depositValue

        // futuramente add codigo para coletar o id do usuario usando nome e email
        try {
            $pdo = new Databases();
            $pdo = $pdo->getConnection();

            if (
                empty($transactionParams->depositValue) ||
                empty($transactionParams->user_id)
            ) {
                echo json_encode([
                    "error" => "The transaction data is mandatory",
                ]);
                exit();
            }

            (int) ($user_id = $transactionParams->user_id);
            (float) ($depositValue = $transactionParams->depositValue);

            if (empty($transactionParams->depositDate)) {
                $transactionParams->depositDate = TransactionModel::setDate();
            } else {
                $depositDate = $transactionParams->depositDate;
            }

            if (is_string(userController::getUserInformation($user_id))) {
                echo json_encode([
                    "error" => true,
                    "message" => "User not found",
                ]);
                exit();
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
                echo json_encode([
                    "sucess" => true,
                    "message" => "Transaction made sucessfuly",
                ]);
            } else {
                echo json_encode([
                    "error" => true,
                    "message" => "Insuficient Balance",
                ]);
                $pdo->rollBack();
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
            echo $transactionInfo;
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
        $sql = "INSERT into transactions(userId, depositValue, depositDate)
                           VALUES(:userId, :depositValue, :depositDate)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":userId", $user_id, PDO::PARAM_INT);
        $stmt->bindParam(":depositValue", $depositValue);
        $stmt->bindParam(":depositDate", $depositDate);
        $stmt->execute();
    }
    public static function updateUserBalance(
        PDO $pdo,
        float $newBalance,
        int $user_id
    ): void {
        $sql = "UPDATE users set user_balance = :newBalance where id= :user_id";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(":newBalance", $newBalance, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
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
            return json_encode([
                "error" => true,
                "message" => "Transaction not found",
            ]);
        }
        return $result[0];
    }
}
