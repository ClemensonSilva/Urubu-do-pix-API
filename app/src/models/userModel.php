<?php

namespace App\models;

use App\controllers\userController;
use App\database\Database;

require_once '../src/database/pdo.php'; // isso é temporario ate ajustar os namespaces completamente
use PDOException;
use PDO;
use stdClass;

class UserModel
{
    public function createUser( stdClass $userParams)
    { 
        $pdo = new Database();
        $pdo = $pdo->getConnection();
        try {
            if ($userParams) {
                $sql = "INSERT INTO users(user_name, user_balance) VALUES(:user_name, :user_balance)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_name', $userParams->user_name);
                $stmt->bindParam(':user_balance', $userParams->user_balance);
                $stmt->execute();
                echo json_encode(['sucess' => 'User created corretly']);
            } else {
                echo json_encode(['error'=>true, 'message'=> 'The user name is mandatory']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    public function getUsers()
    {
        $pdo = new Database();
        $pdo = $pdo->getConnection();
        try {
            $sql = "SELECT * FROM users";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!empty($result)){
                header('Content-Type: application/json');
                echo json_encode(['data'=>$result]);
            }else{
                 echo json_encode(['error'=>true, 'message'=> 'Results not found']);      
            }
        } catch ( PDOException $e) {
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }
    public function deposit(stdClass $userParams){ // user_id e deposit futuramente usar user_acount
        try {
            $pdo = new Database();
            $pdo = $pdo->getConnection();

            $userController = new userController();
            $userInfo = $userController->getUserInformation($userParams->user_id);
            $userName = $userInfo->user_name;
            if (empty($userName)) { 
                echo json_encode(['error'=>true, 'message'=> 'Results not found']); 
                exit();
            }

            $userBalance = $userInfo->user_balance; // no futuro, sera preciso fazer correcao e adicionar um campo email, que seja como dado unico no DB
            $newBalance = $userBalance + $userParams->deposit;

            $sql = "UPDATE users set user_balance =:newBalance where user_name=:userName";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':newBalance',  $newBalance);
            $stmt->bindParam(':userName', $userName);
            $stmt->execute(); 
            echo json_encode(['sucess'=>true, 'message'=> 'Deposit made successfully']);      

        }catch ( PDOException $e) {
            echo json_encode(['error'=> $e->getMessage()]);
        }
       
    }
    public function getUserInformation(string|int $userParams)
    { 
        $pdo = new Database();
        $pdo = $pdo->getConnection();
        try {
            $sql = "SELECT * FROM users where user_name= :userName or  id= :userId ";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':userName', $userParams); // vincula o placeholder usado no sql à variável que o corresponde
            $stmt->bindParam(':userId', $userParams);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ); // recebe os resultados da tabela e os transforma em um array associativo
            if(!empty($result)){
                return ($result);
            }else{
                return json_encode('Results not found');
            }
        } catch (PDOException $e) {
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }
    
}
?>