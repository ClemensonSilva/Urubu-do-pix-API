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

        $valueInvested = $transactionInfo->depositValue;
        if ($valueInvested < $valueToWithdraw) {
            return Databases::genericMessage(
                "error",
                "The withdrawal amount is greater than the invested amount"
            );
        }
        $alloweWithdraw = 0.5 * $valueInvested;

        if ($valueToWithdraw > $alloweWithdraw) {
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
        $percentageofWithdrawn =
            $percentageAlreadyWithdrawn + $valueToWithdraw / $valueInvested;

        if ($percentageofWithdrawn > 0.65) {
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

        // nao permitir que um valor maior que 20% do valor investido seja retirado por withdraw

        // ao fazer suscessivas retiradas de valores, o valor investido deverá "sumir" quando o usuario chegar a retirar 65% do valor
        // iniciamente depositado no investimento garantido que a API sempre consiga retirar ganhos sobre o investimento
    }
    public static function profitInvestiment(stdClass $transactionParams)
    {
        // user_id, transaction_id,
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

        $depositDate = new DateTime($depositDate);
        $now = new DateTime();
        $interval = date_diff($depositDate, $now);
        $days = round($interval->days / 30, 1);

        $interest = 0.33;

        $profit = round(
            $depositValue * pow(1 + $interest, $days) - $depositValue,
            5
        );
        $total = (float) ($profit + $depositValue); // filtro para impedir que valor chegue ao infinto no PHP
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
            return json_encode(Databases::resultsNotFound("Transactions "));
        }
        return $result[0]; // o fetchAll da f consultingDB retorna um array e eu quero capturar apenas o primeiro
    }
}
