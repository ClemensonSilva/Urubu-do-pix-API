<?php

namespace App\models;

use App\database\Database;
use App\controllers\userController;
use PDO;

require_once '../src/database/pdo.php'; // isso é temporario ate ajustar os namespaces completamente
use PDOException;
use stdClass;
use Transliterator;

class TransactionModel
{   
    public function createTransaction(stdClass $transactionParams)
    { // user_id e depositValue

        // futuramente add codigo para coletar o id do usuario usando nome e email
        try {
            $pdo = new Database();
            $pdo = $pdo->getConnection();
            // variables
            $user_id = $transactionParams->user_id;
            $depositValue = $transactionParams->depositValue;
            $depositDate = $transactionParams->depositDate;
            $user_balance = userController::getUserInformation($user_id)->user_balance;

            if (empty($depositDate)) {
                $depositDate = TransactionModel::setDate();
            }

            if (empty($depositValue) || empty($user_id)) {
                echo json_encode(['error' => 'The transaction data is mandatory']);
            } else {
                if (TransactionModel::userhasBalance($user_id, $depositValue)) {
                    $pdo->beginTransaction();
                    TransactionModel::addDepositInvestiment($pdo, $user_id, $depositDate, $depositValue);
                   
                    $newBalance = $user_balance - $depositValue;
                   
                    TransactionModel::updateUserBalance($pdo,$newBalance,$user_id);
                    $pdo->commit();
                    echo json_encode(['sucess' => 'Transaction finished corretlly']);
                } else {
                    $pdo->rollBack();
                    echo json_encode(['error' => 'Insuficient Balance']);
                }
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    public static function  addDepositInvestiment(PDO $pdo,int $user_id,  $depositDate, float $depositValue):void{
        $sql = "INSERT into transactions(userId, depositValue,depositDate) 
        VALUES(:userId, :depositValue, :depositDate)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $user_id);
        $stmt->bindParam(':depositValue', $depositValue);
        $stmt->bindParam(':depositDate', $depositDate);
        $stmt->execute();
    }
    public static function updateUserBalance(PDO $pdo, float $newBalance, int $user_id):void{
        $sql = "UPDATE users set user_balance =:newBalance where id=:user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':newBalance',  $newBalance);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute(); 

    }

    public static function userhasBalance(int $user_id, float $depositValue)
    {
        $balance = userController::getUserInformation($user_id)->user_balance;
        return ($balance > $depositValue) ? true : false;
    }
    public static function setDate()
    {
        return date('y-m-d');
    }
}
