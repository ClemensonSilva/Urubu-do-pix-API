<?php
require_once '/app/database/pdo.php';
class UserModel {
    public function createUser(string $user_name){
        $pdo = new Database();
        $pdo = $pdo->getConnection();
        try {
            if($user_name){
                $sql = "INSERT INTO users(user_name) VALUES(:user_name)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_name', $user_name);
                $stmt->execute();
                echo json_encode(['sucess' => 'User created corretly']);
            }
            else{
                echo json_encode(['error'=> 'The user name is mandatory']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error'=> $e->getMessage()]);
        }
        
    }

};

?>
