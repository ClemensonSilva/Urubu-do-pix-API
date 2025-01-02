<?php
namespace App\Database;
use PDO;

use PDOException;
use stdClass;

class Databases
{
    private $pdo;
    public function __construct()
    {
        try {
            $this->pdo = new PDO(
                "mysql:dbname=urubuDoPix;host=mysql",
                "root",
                "miguel30",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
    public function getConnection()
    {
        return $this->pdo;
    }
    public static function consultingDB($pdo, $sql, $parametros = [])
    {
        try {
            $stmt = $pdo->prepare($sql);
            foreach ($parametros as $param => $valor) {
                $stmt->bindParam($param, $valor);
            }
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            if ($result != null) {
                return $result;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
return;
?>
