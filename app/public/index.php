<?php
require_once "../vendor/autoload.php";
require_once "/app/routes/routes.php";
/*
var_dump(method_exists("App\Models\UserModel", "getUsers"));
 */

use App\Models\TransactionModel;
use App\Controllers\UserController;
/*
$obj2 = new userController();
$obj = new stdClass();
$obj->user_id=2;
$obj->deposit= 200;

$b = $obj2->deposit($obj);
var_dump($obj);
var_dump(TransactionModel:  :getTransactionInfo(1));
$obj2 = new TransactionModel();
$obj = new stdClass();
$obj->user_id = 2;
$obj->depositValue = 16;
$obj2->createTransaction($obj);
$obj2 = new UserController();
$obj = new stdClass();
$obj->user_id = 2;
$obj2->getUserInvestiments($obj);
*/

try {
    $url = parse_url($_SERVER["REQUEST_URI"])["path"];
    $request = $_SERVER["REQUEST_METHOD"];
    if (!isset($router[$request])) {
        throw new Exception("A routa não existe");
    }
    if (!array_key_exists($url, $router[$request])) {
        throw new Exception("A routa não existe");
    }
    $controller = $router[$request][$url];
    $controller();
} catch (Exception $e) {
    $e->getMessage();
}

?>
