<?php
namespace App\models;

use App\database\Database;
use App\controllers\userController;
require_once '../src/database/pdo.php'; // isso é temporario ate ajustar os namespaces completamente
use PDOException;
use stdClass;

class TransactionModel
{
    public function createTransaction(stdClass $transactionParams){
        $pdo = new Database();
        $pdo = $pdo->getConnection();
        // futuramente add codigo para coletar o id do usuario usando nome e email
        try {
            if(empty($transactionParams->depositDate)){
                $transactionParams->depositDate = TransactionModel::setDate();
            }

            if(empty($transactionParams->depositValue) ||empty($transactionParams->user_id)){
                echo json_encode(['error'=> 'The transaction data is mandatory']);
            }
            else{
                $sql = "INSERT into transactions(userId, depositValue,depositDate) 
                VALUES(:userId, :depositValue, :depositDate)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':userId', $transactionParams->user_id);
                $stmt->bindParam(':depositValue', $transactionParams->depositValue);
                $stmt->bindParam(':depositDate', $transactionParams->depositDate);
                $stmt->execute();
                echo json_encode(['sucess' => 'User created corretly']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public static function userhasBalance(int $user_id, float $depositValue){
        $userController = new userController();
        $balance = $userController->getUserInformation($user_id)->user_balance;

        return ($balance > $depositValue)? true : false;
    }
    public static function setDate(){
        return date('y-m-d');
    }
}
?>