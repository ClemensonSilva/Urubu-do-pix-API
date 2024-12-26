<?php

namespace App\models;   

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
                echo json_encode(['error' => 'The user name is user_balance']);
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
                echo json_encode('Results not found');
            }
        } catch ( PDOException $e) {
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