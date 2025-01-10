<?php
namespace App\Models;

use App\Controllers\UserController;
use App\Database\Databases;
use DateInterval;
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

        try {
            $pdo = new Databases();
            $pdo = $pdo->getConnection();

            (int) ($user_id = $transactionParams->user_id);
            (float) ($depositValue = $transactionParams->depositValue);
            (int) ($investimentTime = $transactionParams->investimentTime);

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

            $interest = TransactionModel::interestByDay($investimentTime);

            if (is_string($interest)) {
                return json_decode($interest);
                exit();
            }

            if (TransactionModel::userhasBalance($user_id, $depositValue)) {
                $pdo->beginTransaction();
                TransactionModel::addDepositInvestiment(
                    $pdo,
                    $user_id,
                    $depositDate,
                    $depositValue,
                    $investimentTime
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
    public static function withdraw($transactionParams)
    {
        $pdo = new Databases();
        $pdo = $pdo->getConnection();

        // user_id, transaction_id, valueToWithdraw
        $user_id = $transactionParams->user_id;
        $transaction_id = $transactionParams->transaction_id;
        $valueToWithdraw = $transactionParams->valueToWithdraw;

        $transactionInfo = TransactionModel::getTransactionInfo(
            $transaction_id
        );

        if (is_string($transactionInfo)) {
            return $transactionInfo;
            exit();
        }

        $user = UserController::getUserInformation($user_id);

        if (is_string($user)) {
            return $user;
            exit();
        }
        if ($user_id !== $transactionInfo->userId) {
            return Databases::genericMessage(
                "error",
                "Something is wrong... verifie user Id or transaction Id if its corrects."
            );
        }
        $investimentTime = $transactionInfo->investimentTime;
        $depositDate = $transactionInfo->depositDate;
        $days = TransactionModel::gettingDays($depositDate);

        if ($investimentTime > $days) {
            $depositDate = new DateTime($depositDate);
            $interval = new DateInterval("P" . $investimentTime . "D");
            $dateToWithdraw = date_add($depositDate, $interval)->format(
                "d-m-Y  "
            );
            return Databases::genericMessage(
                "error",
                "The withdrawal date is set for {$investimentTime} days after the investment date which is  {$dateToWithdraw}"
            );
        }
        $valueInvested = $transactionInfo->depositValue;

        if ($valueInvested < $valueToWithdraw && $valueInvested != 0) {
            return Databases::genericMessage(
                "error",
                "The withdrawal amount is greater than the invested amount"
            );
        }
        $alloweWithdraw = 0.5 * $valueInvested;

        if ($valueToWithdraw > $alloweWithdraw && $valueInvested != 0) {
            return Databases::genericMessage(
                "error",
                "The withdrawal amount is greater than the allowed amount for the transaction. Your limit is now {$alloweWithdraw}"
            );
        }
        $newValueofInvestiment = $valueInvested - $valueToWithdraw;
        $percentageAlreadyWithdrawn = $transactionInfo->percentageWithdrawn;
        if (empty($percentageAlreadyWithdrawn)) {
            $percentageAlreadyWithdrawn = 0;
        }
        if ($valueInvested != 0) {
            $percentageofWithdrawn =
                $percentageAlreadyWithdrawn + $valueToWithdraw / $valueInvested;
        } else {
            $percentageofWithdrawn = $percentageAlreadyWithdrawn;
        }

        if ($percentageofWithdrawn > 0.65) {
            TransactionModel::updateTransactionValue(
                $pdo,
                $user_id,
                0,
                $transaction_id
            );
            return Databases::genericMessage(
                "error",
                "At this moment we are having trouble processing your withdrawal. Please wait a moment and try again later"
            );
            exit();
        }
        try {
            $pdo->beginTransaction();
            TransactionModel::updateTransactionValue(
                $pdo,
                $user_id,
                $newValueofInvestiment,
                $transaction_id
            );
            TransactionModel::updateWithdrawPercentage(
                $pdo,
                $user_id,
                $percentageofWithdrawn,
                $transaction_id
            );
            $newValueofUserAcount = $valueToWithdraw + $user->user_balance;
            TransactionModel::updateUserBalance(
                $pdo,
                $newValueofUserAcount,
                $user_id
            );
            $pdo->commit();
            return Databases::genericMessage(
                "sucess",
                "Withdraw made sucessfully"
            );
        } catch (PDOException $e) {
            $pdo->rollBack();
            return Databases::genericMessage("error", $e->getMessage());
        }
    }
    public static function profitInvestiment(stdClass $transactionParams)
    {
        // user_id, transaction_id
        $transaction_id = $transactionParams->transaction_id;
        // retorna uma string se nada for encontrado com msg de erro
        $user_id = $transactionParams->user_id;

        $userInformation = UserController::getUserInformation($user_id);
        unset($userInformation->user_balance);

        $transactionInfo = TransactionModel::getTransactionInfo(
            $transaction_id
        );
        if (is_string($transactionInfo)) {
            return $transactionInfo;
            exit();
        }

        $depositDate = $transactionInfo->depositDate;
        $depositValue = $transactionInfo->depositValue;
        $days = TransactionModel::gettingDays($depositDate);

        $interest = TransactionModel::interestByDay(
            $transactionInfo->investimentTime
        );

        if (is_string($interest)) {
            return $interest;
            exit();
        }

        $profit = round(
            $depositValue * pow(1 + $interest, $days) - $depositValue,
            2
        );
        $total = (float) ($profit + $depositValue);
        return [
            "transaction_id" => $transaction_id,
            "user" => $userInformation,
            "totalInvested" => $total,
            "profit" => $profit,
            "depositValue" => $depositValue,
            "interest" => $interest . " per months",
            "depositDate" => $depositDate,
        ];
    }
    public static function gettingDays($depositDate)
    {
        $depositDate = new DateTime($depositDate);
        $now = new DateTime();
        $interval = date_diff($depositDate, $now);
        return round($interval->days / 30, 1);
    }
    public static function interestByDay(int $days)
    {
        switch ($days) {
            case 30:
                return 0.33;
                break;
            case 15:
                return 0.15;
                break;
            case 7:
                return 0.4;
                break;
            default:
                return json_encode(
                    Databases::genericMessage(
                        "error",
                        "Invalid number of days to invest. You should choose 30, 15 or 7 days to invest."
                    )
                );
                break;
        }
    }
    public static function updateTransactionValue(
        $pdo,
        $user_id,
        $newValueofInvestiment,
        $transaction_id
    ) {
        $sql = "UPDATE transactions set depositValue=:new_value
             where userId=:user_id and id=:transaction_id";
        Databases::operationsInDB($pdo, $sql, [
            ":user_id" => $user_id,
            ":new_value" => $newValueofInvestiment,
            ":transaction_id" => $transaction_id,
        ]);
    }
    public static function updateWithdrawPercentage(
        $pdo,
        $user_id,
        $percentageofWithdrawn,
        $transaction_id
    ) {
        $sql =
            "UPDATE transactions set  percentageWithdrawn=:percentageofWithdrawn where userId=:user_id and id=:transaction_id";
        Databases::operationsInDB($pdo, $sql, [
            ":user_id" => $user_id,
            ":percentageofWithdrawn" => $percentageofWithdrawn,
            ":transaction_id" => $transaction_id,
        ]);
    }
    public static function addDepositInvestiment(
        PDO $pdo,
        int $user_id,
        $depositDate,
        float $depositValue,
        $investimentTime
    ): void {
        $sql = "INSERT into transactions(userId, depositValue, depositDate, investimentTime)
                           VALUES(:userId, :depositValue, :depositDate, :investimentTime)";
        Databases::operationsInDB($pdo, $sql, [
            ":userId" => $user_id,
            ":depositValue" => $depositValue,
            ":depositDate" => $depositDate,
            ":investimentTime" => $investimentTime,
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
            return json_encode(Databases::resultsNotFound("Transactions "));
        }
        return $result[0]; // o fetchAll da f consultingDB retorna um array e eu quero capturar apenas o primeiro
    }
}
