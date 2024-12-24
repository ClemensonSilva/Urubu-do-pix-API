<?php
namespace App\database;
use PDO;

use PDOException;
class Database {
    private $pdo;
    public function __construct() {
        try{
            $this->pdo = new PDO('mysql:dbname=urubuDoPix;host=mysql', 'root', 'miguel30', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        }
        catch(PDOException $e){
            exit($e->getMessage());
        }
    }
    public function getConnection(){
        return $this->pdo;
    }
}
return ;
?>


