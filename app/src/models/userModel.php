<?php

namespace App\models;

use App\database\Database;

require_once '../src/database/pdo.php'; // isso é temporario ate ajustar os namespaces completamente
use PDOException;
use PDO;
class UserModel
{
    public function createUser($user_name)
    { // a variavel user name é do tipo stdClass
        $pdo = new Database();
        $pdo = $pdo->getConnection();
        try {
            if ($user_name) {
                $sql = "INSERT INTO users(user_name) VALUES(:user_name)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_name', $user_name->user_name);
                $stmt->execute();
                echo json_encode(['sucess' => 'User created corretly']);
            } else {
                echo json_encode(['error' => 'The user name is mandatory']);
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
            header('Content-Type: application/json');
            echo json_encode([  'data'=>$result]);
        } catch ( PDOException $e) {
            echo json_encode(['error'=> $e->getMessage()]);
        }
    }
}
?>